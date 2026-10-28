<?php

namespace App\Http\Controllers\TeacherPanel;

use App\Enums\Teacher\SubjectEnum;
use App\Enums\Teacher\TeacherPaymentTypeEnum;
use App\Http\Controllers\Controller;
use App\Models\GroupSchedule;
use App\Models\TeacherPayroll;
use App\Services\Teacher\TeacherPayrollService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Mpdf\Mpdf;
use Mpdf\Output\Destination;

class PayrollPdfController extends Controller
{
    // ============================================
    // PDF الحساب
    // ============================================

    public function download(TeacherPayroll $payroll)
    {
        return $this->buildPayrollPdf($payroll, Destination::DOWNLOAD);
    }

    public function stream(TeacherPayroll $payroll)
    {
        return $this->buildPayrollPdf($payroll, Destination::INLINE);
    }

    protected function buildPayrollPdf(TeacherPayroll $payroll, string $destination)
    {
        $user = Auth::guard('teacher')->user();

        abort_unless(
            $payroll->teacher_id === $user->getOwnerId() ||
            $payroll->teacher?->getOwnerId() === $user->getOwnerId(),
            404
        );

        $payroll->load(['teacher', 'branch', 'bonuses', 'deductionsItems', 'payments']);

        $amounts = $this->calculateAmounts($payroll);
        $stats = $this->calculateStats($payroll);

        $html = view('teacher-panel.pages.payroll.pdf.payroll-pdf', [
            'payroll' => $payroll,
            'amounts' => $amounts,
            'stats' => $stats,
        ])->render();

        $mpdf = $this->makeMpdf();

        $mpdf->WriteHTML($html);

        $filename = "payroll-{$payroll->reference}.pdf";

        return response($mpdf->Output($filename, $destination))
            ->header('Content-Type', 'application/pdf');
    }

    // ============================================
    // PDF الحصص
    // ============================================

    public function sessionsDownload(TeacherPayroll $payroll)
    {
        return $this->buildSessionsPdf($payroll, Destination::DOWNLOAD);
    }

    public function sessionsStream(TeacherPayroll $payroll)
    {
        return $this->buildSessionsPdf($payroll, Destination::INLINE);
    }

    protected function buildSessionsPdf(TeacherPayroll $payroll, string $destination)
    {
        $user = Auth::guard('teacher')->user();

        abort_unless(
            $payroll->teacher_id === $user->getOwnerId() ||
            $payroll->teacher?->getOwnerId() === $user->getOwnerId(),
            404
        );

        $payroll->load(['teacher', 'branch']);

        $sessions = $this->buildSessions($payroll);

        $html = view('teacher-panel.pages.payroll.pdf.payroll-sessions-pdf', [
            'payroll' => $payroll,
            'sessions' => $sessions,
        ])->render();

        $mpdf = $this->makeMpdf();

        $mpdf->WriteHTML($html);

        $filename = "sessions-{$payroll->reference}.pdf";

        return response($mpdf->Output($filename, $destination))
            ->header('Content-Type', 'application/pdf');
    }

    // ============================================
    // Helpers
    // ============================================

    protected function makeMpdf(): Mpdf
    {
        $mpdf = new Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4',
            'margin_left' => 12,
            'margin_right' => 12,
            'margin_top' => 15,
            'margin_bottom' => 20,
            'margin_header' => 5,
            'margin_footer' => 10,
            'default_font' => 'dejavusans',
            'default_font_size' => 11,
            'autoScriptToLang' => true,
            'autoLangToFont' => true,
            'tempDir' => storage_path('app/mpdf'),
        ]);

        $mpdf->SetDirectionality('rtl');

        $mpdf->SetHTMLFooter('
            <div style="text-align: center; font-size: 9px; color: #999; border-top: 1px solid #ddd; padding-top: 5px;">
                نبذة — نظام إدارة المراكز التعليمية | '.now()->format('Y-m-d H:i').'
            </div>
        ');

        return $mpdf;
    }

    protected function calculateStats(TeacherPayroll $payroll): array
    {
        $sessions = $this->buildSessions($payroll);

        return [
            'total' => $sessions->count(),
            'cancelled' => $sessions->where('status', 'cancelled')->count(),
            'completed' => $sessions->where('status', 'completed')->count(),
            'upcoming' => $sessions->where('status', 'upcoming')->count(),
            'hours_total' => $sessions->sum('hours'),
            'hours_cancelled' => $sessions->where('status', 'cancelled')->sum('hours'),
            'hours_completed' => $sessions->where('status', 'completed')->sum('hours'),
            'hours_upcoming' => $sessions->where('status', 'upcoming')->sum('hours'),
        ];
    }

    protected function calculateAmounts(TeacherPayroll $payroll): array
    {
        $service = app(TeacherPayrollService::class);
        $settings = $service->resolveSettings($payroll->teacher, $payroll->branch_id);

        $stats = $this->calculateStats($payroll);

        $fullAmount = 0.0;

        switch ($settings['payment_type']) {
            case TeacherPaymentTypeEnum::MONTHLY:
                $fullAmount = $settings['monthly_salary'];
                break;
            case TeacherPaymentTypeEnum::PER_SESSION:
                $fullAmount = $stats['total'] * $settings['per_session_rate'];
                break;
            case TeacherPaymentTypeEnum::HOURLY:
                $fullAmount = $stats['hours_total'] * $settings['hourly_rate'];
                break;
            case TeacherPaymentTypeEnum::MONTHLY_PLUS_PER_SESSION:
                $fullAmount = $settings['monthly_salary'] + ($stats['total'] * $settings['per_session_rate']);
                break;
        }

        $cancellationDeduction = $stats['cancelled'] * $settings['session_deduction_rate'];
        $netAfterSessions = max(0, $fullAmount - $cancellationDeduction);
        $netAmount = max(0, $netAfterSessions + (float) $payroll->bonus - (float) $payroll->deductions);
        $paid = (float) $payroll->paid_amount;
        $remaining = max(0, $netAmount - $paid);

        return [
            'full' => round($fullAmount, 2),
            'cancellation_deduction' => round($cancellationDeduction, 2),
            'bonus' => (float) $payroll->bonus,
            'deductions' => (float) $payroll->deductions,
            'net' => round($netAmount, 2),
            'paid' => round($paid, 2),
            'remaining' => round($remaining, 2),
        ];
    }

    protected function buildSessions(TeacherPayroll $payroll): Collection
    {
        $start = $payroll->period_start->copy();
        $end = $payroll->period_end->copy();

        $schedules = GroupSchedule::query()
            ->where('teacher_id', $payroll->teacher_id)
            ->when($payroll->branch_id, fn ($q) => $q->whereHas('group', fn ($g) => $g->where('branch_id', $payroll->branch_id)))
            ->with(['group', 'group.branch', 'subject'])
            ->get();

        $dayMap = [
            'Saturday' => 6, 'Sunday' => 0, 'Monday' => 1, 'Tuesday' => 2,
            'Wednesday' => 3, 'Thursday' => 4, 'Friday' => 5,
        ];

        $sessions = [];

        foreach ($schedules as $schedule) {
            $dayOfWeek = $dayMap[$schedule->day] ?? null;
            if ($dayOfWeek === null) {
                continue;
            }

            $cursor = $start->copy();
            while ($cursor->lte($end)) {
                if ($cursor->dayOfWeek === $dayOfWeek) {
                    $isCancelled = $schedule->is_cancelled;
                    $isClosed = $schedule->group?->isClosedForDate($cursor) ?? false;

                    $status = match (true) {
                        $isCancelled => 'cancelled',
                        $isClosed => 'completed',
                        default => 'upcoming',
                    };

                    $subjectEnum = SubjectEnum::tryFrom($schedule->subject?->subject ?? '');

                    $sessions[] = [
                        'date' => $cursor->copy(),
                        'start_time' => $schedule->start_time?->format('H:i'),
                        'end_time' => $schedule->end_time?->format('H:i'),
                        'group' => $schedule->group?->name,
                        'branch' => $schedule->group?->branch?->name,
                        'subject' => $subjectEnum?->label() ?? $schedule->subject?->subject_label ?? '—',
                        'hours' => (float) $schedule->hours,
                        'status' => $status,
                    ];
                }
                $cursor->addDay();
            }
        }

        usort($sessions, fn ($a, $b) => $a['date']->timestamp <=> $b['date']->timestamp);

        return collect($sessions);
    }
}
