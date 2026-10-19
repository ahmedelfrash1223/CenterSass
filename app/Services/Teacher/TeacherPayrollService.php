<?php

namespace App\Services\Teacher;

use App\Enums\Teacher\ExpenseSourceEnum;
use App\Enums\Teacher\TeacherPaymentTypeEnum;
use App\Enums\Teacher\TeacherPayrollStatusEnum;
use App\Enums\Teacher\TeacherRoleEnum;
use App\Models\GroupSchedule;
use App\Models\Teacher;
use App\Models\TeacherBranchPayrollSetting;
use App\Models\TeacherPayroll;
use App\Models\TeacherPayrollSetting;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class TeacherPayrollService
{
    public function __construct(
        protected ExpenseService $expenseService,
    ) {}

    public function resolveSettings(Teacher $teacher, ?int $branchId): array
    {
        if ($branchId) {
            $branch = TeacherBranchPayrollSetting::where('teacher_id', $teacher->id)
                ->where('branch_id', $branchId)
                ->where('is_active', true)
                ->first();

            if ($branch) {
                return [
                    'payment_type' => $branch->payment_type,
                    'monthly_salary' => (float) $branch->monthly_salary,
                    'per_session_rate' => (float) $branch->per_session_rate,
                    'hourly_rate' => (float) $branch->hourly_rate,
                    'session_deduction_rate' => (float) $branch->session_deduction_rate,
                    'source' => 'branch',
                ];
            }
        }

        $general = TeacherPayrollSetting::where('teacher_id', $teacher->id)
            ->where('is_active', true)
            ->first();

        if ($general) {
            return [
                'payment_type' => $general->payment_type,
                'monthly_salary' => (float) $general->monthly_salary,
                'per_session_rate' => (float) $general->per_session_rate,
                'hourly_rate' => (float) $general->hourly_rate,
                'session_deduction_rate' => (float) $general->session_deduction_rate,
                'source' => 'general',
            ];
        }

        return [
            'payment_type' => TeacherPaymentTypeEnum::MONTHLY,
            'monthly_salary' => 0.0,
            'per_session_rate' => 0.0,
            'hourly_rate' => 0.0,
            'session_deduction_rate' => 0.0,
            'source' => 'none',
        ];
    }

    public function generateForTeacher(
        Teacher $teacher,
        Carbon $periodStart,
        Carbon $periodEnd,
        ?int $branchId = null,
        ?Teacher $generatedBy = null,
    ): ?TeacherPayroll {
        [$totalSessions, $cancelledSessions, $payableSessions, $totalHours] =
            $this->calculateSessions($teacher, $periodStart, $periodEnd, $branchId);

        $settings = $this->resolveSettings($teacher, $branchId);

        if ($settings['source'] === 'none') {
            return null;
        }

        $isAssistant = $teacher->role === TeacherRoleEnum::ASSISTANT;

        if (! $isAssistant && $totalSessions === 0 && $settings['monthly_salary'] <= 0) {
            return null;
        }

        if ($isAssistant && $settings['monthly_salary'] <= 0) {
            return null;
        }

        $baseSalary = 0.0;
        $sessionsAmount = 0.0;
        $hoursAmount = 0.0;

        switch ($settings['payment_type']) {
            case TeacherPaymentTypeEnum::MONTHLY:
                $baseSalary = $settings['monthly_salary'];
                break;
            case TeacherPaymentTypeEnum::PER_SESSION:
                $sessionsAmount = $payableSessions * $settings['per_session_rate'];
                break;
            case TeacherPaymentTypeEnum::HOURLY:
                $hoursAmount = $totalHours * $settings['hourly_rate'];
                break;
            case TeacherPaymentTypeEnum::MONTHLY_PLUS_PER_SESSION:
                $baseSalary = $settings['monthly_salary'];
                $sessionsAmount = $payableSessions * $settings['per_session_rate'];
                break;
        }

        $cancellationDeduction = $cancelledSessions * $settings['session_deduction_rate'];
        $sessionsAmount = max(0, $sessionsAmount - $cancellationDeduction);

        $netAmount = max(0, $baseSalary + $sessionsAmount + $hoursAmount);

        return DB::transaction(function () use (
            $teacher, $branchId, $periodStart, $periodEnd,
            $settings, $totalSessions, $cancelledSessions, $payableSessions,
            $totalHours, $baseSalary, $sessionsAmount, $hoursAmount,
            $netAmount, $generatedBy
        ) {
            $existing = TeacherPayroll::where('teacher_id', $teacher->id)
                ->whereDate('period_start', $periodStart->toDateString())
                ->whereDate('period_end', $periodEnd->toDateString())
                ->where('branch_id', $branchId)
                ->first();

            if ($existing && $existing->status === TeacherPayrollStatusEnum::PAID) {
                return $existing;
            }

            if ($existing) {
                $existing->delete();
            }

            return TeacherPayroll::create([
                'teacher_id' => $teacher->id,
                'branch_id' => $branchId,
                'period_start' => $periodStart->toDateString(),
                'period_end' => $periodEnd->toDateString(),
                'period_label' => $this->buildPeriodLabel($periodStart, $periodEnd),
                'payment_type' => $settings['payment_type']->value,
                'total_sessions' => $totalSessions,
                'cancelled_sessions' => $cancelledSessions,
                'payable_sessions' => $payableSessions,
                'total_hours' => $totalHours,
                'base_salary' => $baseSalary,
                'sessions_amount' => $sessionsAmount,
                'hours_amount' => $hoursAmount,
                'bonus' => 0,
                'deductions' => 0,
                'net_amount' => $netAmount,
                'paid_amount' => 0,
                'status' => TeacherPayrollStatusEnum::PENDING->value,
                'generated_by' => $generatedBy?->id,
            ]);
        });
    }

    protected function calculateSessions(
        Teacher $teacher,
        Carbon $periodStart,
        Carbon $periodEnd,
        ?int $branchId
    ): array {
        $query = GroupSchedule::query()->where('teacher_id', $teacher->id);

        if ($branchId !== null) {
            $query->whereHas('group', fn ($q) => $q->where('branch_id', $branchId));
        }

        $schedules = $query->get();

        if ($schedules->isEmpty()) {
            return [0, 0, 0, 0.0];
        }

        $dayMap = [
            'Saturday' => 6, 'Sunday' => 0, 'Monday' => 1, 'Tuesday' => 2,
            'Wednesday' => 3, 'Thursday' => 4, 'Friday' => 5,
        ];

        $total = 0;
        $cancelled = 0;
        $payable = 0;
        $hours = 0.0;

        foreach ($schedules as $schedule) {
            $dayOfWeek = $dayMap[$schedule->day] ?? null;
            if ($dayOfWeek === null) {
                continue;
            }

            $occurrences = $this->countOccurrences($periodStart, $periodEnd, $dayOfWeek);

            if ($occurrences <= 0) {
                continue;
            }

            $total += $occurrences;

            if ($schedule->is_cancelled) {
                $cancelled += $occurrences;
            } else {
                $payable += $occurrences;
                $hours += $occurrences * (float) $schedule->hours;
            }
        }

        return [$total, $cancelled, $payable, $hours];
    }

    protected function countOccurrences(Carbon $start, Carbon $end, int $dayOfWeek): int
    {
        $count = 0;
        $cursor = $start->copy();
        while ($cursor->lte($end)) {
            if ($cursor->dayOfWeek === $dayOfWeek) {
                $count++;
            }
            $cursor->addDay();
        }

        return $count;
    }

    public function markAsReviewed(TeacherPayroll $payroll, Teacher $user, ?string $notes = null): bool
    {
        return $payroll->update([
            'is_reviewed' => true,
            'reviewed_at' => now(),
            'reviewed_by' => $user->id,
            'review_notes' => $notes,
        ]);
    }

    public function addBonus(TeacherPayroll $payroll, float $amount, string $reason, Teacher $user): void
    {
        DB::transaction(function () use ($payroll, $amount, $reason, $user) {
            $expense = $this->expenseService->record(
                user: $user,
                source: ExpenseSourceEnum::TEACHER_BONUS,
                amount: $amount,
                sourceId: $payroll->id,
                branchId: $payroll->branch_id,
                teacherPayeeId: $payroll->teacher_id,
                teacherPayrollId: $payroll->id,
                description: "بونص: {$reason}",
                meta: [
                    'payroll_reference' => $payroll->reference,
                    'period_label' => $payroll->period_label,
                ],
                occurredAt: now(),
            );

            $payroll->bonuses()->create([
                'amount' => $amount,
                'reason' => $reason,
                'expense_id' => $expense->id,
                'added_by' => $user->id,
            ]);

            $payroll->increment('bonus', $amount);
            $payroll->increment('net_amount', $amount);
        });
    }

    public function addDeduction(TeacherPayroll $payroll, float $amount, string $reason, Teacher $user): void
    {
        DB::transaction(function () use ($payroll, $amount, $reason, $user) {
            $payroll->deductionsItems()->create([
                'amount' => $amount,
                'reason' => $reason,
                'added_by' => $user->id,
            ]);

            $payroll->increment('deductions', $amount);
            $payroll->decrement('net_amount', $amount);
        });
    }

    public function markAsPaid(
        TeacherPayroll $payroll,
        float $amount,
        Teacher $user,
        string $paymentMethod = 'cash',
        ?string $referenceNumber = null,
        ?string $notes = null,
    ): bool {
        return DB::transaction(function () use (
            $payroll, $amount, $user, $paymentMethod, $referenceNumber, $notes
        ) {
            $expense = $this->expenseService->record(
                user: $user,
                source: ExpenseSourceEnum::TEACHER_SALARY,
                amount: $amount,
                sourceId: $payroll->id,
                branchId: $payroll->branch_id,
                teacherPayeeId: $payroll->teacher_id,
                teacherPayrollId: $payroll->id,
                description: "راتب {$payroll->period_label}",
                meta: [
                    'payroll_reference' => $payroll->reference,
                    'payment_method' => $paymentMethod,
                ],
                occurredAt: now(),
            );

            $payroll->payments()->create([
                'teacher_id' => $payroll->teacher_id,
                'branch_id' => $payroll->branch_id,
                'amount' => $amount,
                'payment_method' => $paymentMethod,
                'reference_number' => $referenceNumber,
                'notes' => $notes,
                'paid_at' => now(),
                'paid_by' => $user->id,
                'expense_id' => $expense->id,
            ]);

            $newPaid = (float) $payroll->paid_amount + $amount;

            $status = $newPaid >= (float) $payroll->net_amount
                ? TeacherPayrollStatusEnum::PAID
                : TeacherPayrollStatusEnum::PARTIAL;

            $payroll->update([
                'paid_amount' => $newPaid,
                'status' => $status->value,
                'paid_at' => now(),
                'paid_by' => $user->id,
                'notes' => $notes,
            ]);

            return true;
        });
    }

    // ============================================
    // Helpers
    // ============================================

    protected function buildPeriodLabel(Carbon $start, Carbon $end): string
    {
        $months = [
            1 => 'يناير', 2 => 'فبراير', 3 => 'مارس', 4 => 'أبريل',
            5 => 'مايو', 6 => 'يونيو', 7 => 'يوليو', 8 => 'أغسطس',
            9 => 'سبتمبر', 10 => 'أكتوبر', 11 => 'نوفمبر', 12 => 'ديسمبر',
        ];

        if ($start->month === $end->month && $start->year === $end->year) {
            return $months[$start->month].' '.$start->year;
        }

        return $start->format('Y-m-d').' → '.$end->format('Y-m-d');
    }
}
