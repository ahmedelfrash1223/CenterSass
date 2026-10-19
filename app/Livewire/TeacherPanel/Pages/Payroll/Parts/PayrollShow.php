<?php

namespace App\Livewire\TeacherPanel\Pages\Payroll\Parts;

use App\Enums\Teacher\SubjectEnum;
use App\Enums\Teacher\TeacherPaymentTypeEnum;
use App\Enums\Teacher\TeacherPermissionEnum;
use App\Models\GroupSchedule;
use App\Models\TeacherPayroll;
use App\Services\Teacher\TeacherPayrollService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class PayrollShow extends Component
{
    public TeacherPayroll $payroll;

    // Bonus
    public string $bonusAmount = '';

    public string $bonusReason = '';

    // Deduction
    public string $deductionAmount = '';

    public string $deductionReason = '';

    // Pay
    public string $payAmount = '';

    public string $paymentMethod = 'cash';

    public string $paymentNotes = '';

    public bool $confirmPartialPay = false;

    public bool $confirmFullPay = false;

    // Settings
    public string $payment_type = 'monthly';

    public string $monthly_salary = '0';

    public string $per_session_rate = '0';

    public string $hourly_rate = '0';

    public string $session_deduction_rate = '0';

    // Tabs
    public string $activeTab = 'details';

    // Form toggles
    public bool $showBonusForm = false;

    public bool $showDeductionForm = false;

    public bool $showPayForm = false;

    public bool $showSettingsForm = false;

    public function mount(TeacherPayroll $payroll): void
    {
        $user = Auth::guard('teacher')->user();

        abort_unless($user->hasPermission(TeacherPermissionEnum::PAYROLL_VIEW), 403);

        abort_unless(
            $payroll->teacher_id === $user->getOwnerId() ||
            $payroll->teacher?->getOwnerId() === $user->getOwnerId(),
            404
        );

        $this->payroll = $payroll->load(['teacher', 'branch', 'bonuses', 'deductionsItems', 'payments']);

        $this->loadPaymentSettings();
    }

    protected function loadPaymentSettings(): void
    {
        $setting = $this->payroll->teacher?->payrollSetting;

        if ($setting) {
            $this->payment_type = $setting->payment_type->value;
            $this->monthly_salary = (string) $setting->monthly_salary;
            $this->per_session_rate = (string) $setting->per_session_rate;
            $this->hourly_rate = (string) $setting->hourly_rate;
            $this->session_deduction_rate = (string) $setting->session_deduction_rate;
        }
    }

    public function setTab(string $tab): void
    {
        $this->activeTab = $tab;
    }

    // ============================================
    // Settings
    // ============================================

    public function saveSettings(): void
    {
        $user = Auth::guard('teacher')->user();
        abort_unless($user->hasPermission(TeacherPermissionEnum::PAYROLL_MANAGE), 403);

        $this->validate([
            'payment_type' => 'required|string',
            'monthly_salary' => 'numeric|min:0',
            'per_session_rate' => 'numeric|min:0',
            'hourly_rate' => 'numeric|min:0',
            'session_deduction_rate' => 'numeric|min:0',
        ]);

        $teacher = $this->payroll->teacher;

        if ($teacher) {
            $teacher->payrollSetting()->updateOrCreate(
                ['teacher_id' => $teacher->id],
                [
                    'payment_type' => $this->payment_type,
                    'monthly_salary' => (float) $this->monthly_salary,
                    'per_session_rate' => (float) $this->per_session_rate,
                    'hourly_rate' => (float) $this->hourly_rate,
                    'session_deduction_rate' => (float) $this->session_deduction_rate,
                    'is_active' => true,
                ]
            );
        }

        $this->showSettingsForm = false;
        $this->dispatch('show-success-message', message: 'تم حفظ إعدادات الأجر.');
    }

    // ============================================
    // Bonus & Deduction
    // ============================================

    public function addBonus(TeacherPayrollService $service): void
    {
        $user = Auth::guard('teacher')->user();
        abort_unless($user->hasPermission(TeacherPermissionEnum::PAYROLL_ADD_BONUS), 403);

        $this->validate([
            'bonusAmount' => 'required|numeric|min:0.01',
            'bonusReason' => 'required|string|max:255',
        ]);

        $service->addBonus($this->payroll, (float) $this->bonusAmount, $this->bonusReason, $user);

        $this->payroll->refresh()->load(['bonuses', 'deductionsItems', 'payments']);
        $this->reset(['bonusAmount', 'bonusReason', 'showBonusForm']);
        $this->dispatch('show-success-message', message: 'تم إضافة البونص.');
    }

    public function addDeduction(TeacherPayrollService $service): void
    {
        $user = Auth::guard('teacher')->user();
        abort_unless($user->hasPermission(TeacherPermissionEnum::PAYROLL_ADD_DEDUCTION), 403);

        $this->validate([
            'deductionAmount' => 'required|numeric|min:0.01',
            'deductionReason' => 'required|string|max:255',
        ]);

        $service->addDeduction($this->payroll, (float) $this->deductionAmount, $this->deductionReason, $user);

        $this->payroll->refresh()->load(['bonuses', 'deductionsItems', 'payments']);
        $this->reset(['deductionAmount', 'deductionReason', 'showDeductionForm']);
        $this->dispatch('show-success-message', message: 'تم إضافة الخصم.');
    }

    // ============================================
    // Pay
    // ============================================

    public function requestPay(bool $full = false): void
    {
        $this->confirmPartialPay = ! $full;
        $this->confirmFullPay = $full;

        if ($full) {
            // ✅ دفع كل المتبقي من الحساب
            $this->payAmount = (string) $this->amounts['remaining'];
        } else {
            // ✅ دفع المتبقي من "حتى الآن" (بعد الدفعات السابقة)
            $this->payAmount = (string) $this->partialRemaining;
        }
    }

    public function cancelPay(): void
    {
        $this->confirmPartialPay = false;
        $this->confirmFullPay = false;
        $this->payAmount = '';
    }

    public function confirmPayment(TeacherPayrollService $service): void
    {
        $user = Auth::guard('teacher')->user();
        abort_unless($user->hasPermission(TeacherPermissionEnum::PAYROLL_MARK_PAID), 403);

        // ✅ لو المبلغ أكبر من المتبقي → رفض
        $remaining = (float) $this->amounts['remaining'];
        $amount = $this->payAmount !== '' ? (float) $this->payAmount : $remaining;

        if ($amount <= 0) {
            $this->dispatch('show-error-message', message: 'المبلغ غير صحيح.');

            return;
        }

        if ($amount > $remaining + 0.01) { // 0.01 tolerance
            $this->dispatch('show-error-message', message: "المبلغ أكبر من المتبقي ({$remaining} ج.م).");

            return;
        }

        $service->markAsPaid(
            payroll: $this->payroll,
            amount: $amount,
            user: $user,
            paymentMethod: $this->paymentMethod,
            notes: $this->paymentNotes ?: null,
        );

        $this->payroll->refresh()->load(['bonuses', 'deductionsItems', 'payments']);
        $this->reset(['payAmount', 'paymentNotes', 'showPayForm', 'confirmPartialPay', 'confirmFullPay']);
        $this->dispatch('show-success-message', message: 'تم تسجيل الدفع.');
    }

    // ============================================
    // Statistics
    // ============================================

    public function getAllSessionsProperty()
    {
        return $this->buildSessionsUntilDate($this->payroll->period_end);
    }

    public function getSessionsUntilTodayProperty()
    {
        $today = now();
        $end = $today->lessThan($this->payroll->period_end) ? $today : $this->payroll->period_end;

        return $this->buildSessionsUntilDate($end);
    }

    protected function buildSessionsUntilDate(Carbon $end)
    {
        $start = $this->payroll->period_start->copy();
        $end = $end->copy();

        if ($end->lessThan($start)) {
            return collect();
        }

        $schedules = GroupSchedule::query()
            ->where('teacher_id', $this->payroll->teacher_id)
            ->when($this->payroll->branch_id, fn ($q) => $q->whereHas('group', fn ($g) => $g->where('branch_id', $this->payroll->branch_id)))
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

    public function getStatsProperty(): array
    {
        $sessions = $this->allSessions;

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

    public function getStatsUntilTodayProperty(): array
    {
        $sessions = $this->sessionsUntilToday;

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

    public function getAmountsProperty(): array
    {
        $settings = app(TeacherPayrollService::class)
            ->resolveSettings($this->payroll->teacher, $this->payroll->branch_id);

        $stats = $this->stats;
        $statsToday = $this->statsUntilToday;

        $fullAmount = 0.0;
        $amountUntilToday = 0.0;

        switch ($settings['payment_type']) {
            case TeacherPaymentTypeEnum::MONTHLY:
                $fullAmount = $settings['monthly_salary'];
                $amountUntilToday = $settings['monthly_salary'];
                break;
            case TeacherPaymentTypeEnum::PER_SESSION:
                $fullAmount = $stats['total'] * $settings['per_session_rate'];
                $amountUntilToday = $statsToday['total'] * $settings['per_session_rate'];
                break;
            case TeacherPaymentTypeEnum::HOURLY:
                $fullAmount = $stats['hours_total'] * $settings['hourly_rate'];
                $amountUntilToday = $statsToday['hours_total'] * $settings['hourly_rate'];
                break;
            case TeacherPaymentTypeEnum::MONTHLY_PLUS_PER_SESSION:
                $fullAmount = $settings['monthly_salary'] + ($stats['total'] * $settings['per_session_rate']);
                $amountUntilToday = $settings['monthly_salary'] + ($statsToday['total'] * $settings['per_session_rate']);
                break;
        }

        $cancellationDeduction = $stats['cancelled'] * $settings['session_deduction_rate'];
        $netAfterSessions = max(0, $fullAmount - $cancellationDeduction);

        $netAmount = max(0, $netAfterSessions + (float) $this->payroll->bonus - (float) $this->payroll->deductions);

        $paid = (float) $this->payroll->paid_amount;
        $remaining = max(0, $netAmount - $paid);

        return [
            'full' => round($fullAmount, 2),
            'until_today' => round($amountUntilToday, 2),
            'cancellation_deduction' => round($cancellationDeduction, 2),
            'bonus' => (float) $this->payroll->bonus,
            'deductions' => (float) $this->payroll->deductions,
            'net' => round($netAmount, 2),
            'paid' => round($paid, 2),
            'remaining' => round($remaining, 2),
        ];
    }

    /**
     * ✅ المبلغ المتبقي من "حتى الآن" (بعد الدفعات السابقة)
     */
    public function getPartialRemainingProperty(): float
    {
        $untilToday = (float) $this->amounts['until_today'];
        $paid = (float) $this->amounts['paid'];

        return max(0, round($untilToday - $paid, 2));
    }

    public function getPartialAmountProperty(): float
    {
        return (float) $this->amounts['until_today'];
    }

    public function getPartialSessionsCountProperty(): int
    {
        return (int) $this->statsUntilToday['total'];
    }

    public function render()
    {
        $user = Auth::guard('teacher')->user();

        return view('teacher-panel.pages.payroll.parts.payroll-show', [
            'canAddBonus' => $user->hasPermission(TeacherPermissionEnum::PAYROLL_ADD_BONUS),
            'canAddDeduction' => $user->hasPermission(TeacherPermissionEnum::PAYROLL_ADD_DEDUCTION),
            'canMarkPaid' => $user->hasPermission(TeacherPermissionEnum::PAYROLL_MARK_PAID),
            'canManage' => $user->hasPermission(TeacherPermissionEnum::PAYROLL_MANAGE),
            'allSessions' => $this->allSessions,
            'stats' => $this->stats,
            'statsToday' => $this->statsUntilToday,
            'amounts' => $this->amounts,
            'partialAmount' => $this->partialAmount,
            'partialRemaining' => $this->partialRemaining,
            'partialSessions' => $this->partialSessionsCount,
            'hasSettings' => $this->payroll->teacher?->payrollSetting !== null,
        ]);
    }
}
