<?php

namespace App\Services\Teacher;

use App\Enums\Teacher\PricingTypeEnum;
use App\Enums\Teacher\TeacherRoleEnum;
use App\Models\Attendance;
use App\Models\Group;
use App\Models\GroupSchedule;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Student;
use App\Models\Teacher;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class InvoiceService
{
    public function __construct(
        private readonly InvoiceNumberGenerator $numberGenerator
    ) {}

    /**
     * Generate an invoice for a student for a specific month.
     *
     * @param Student $student
     * @param Carbon  $periodStart  First day of the billing period
     * @param Carbon  $periodEnd    Last day of the billing period
     * @param Teacher $user         Who is generating the invoice
     * @return Invoice|null
     */
    public function generateForStudent(
        Student $student,
        Carbon $periodStart,
        Carbon $periodEnd,
        Teacher $user
    ): ?Invoice {
        $ownerId = $user->getOwnerId();

        // Guard: student must belong to the same owner
        if ($student->teacher_id !== $ownerId) {
            return null;
        }

        // Prevent duplicate invoice for the same period
        $exists = Invoice::where('student_id', $student->id)
            ->where('teacher_id', $ownerId)
            ->whereDate('period_start', $periodStart->toDateString())
            ->whereDate('period_end', $periodEnd->toDateString())
            ->whereNull('cancelled_at')
            ->exists();

        if ($exists) {
            return null;
        }

        // Get active groups for the student
        $groups = $student->activeGroups()
            ->where('groups.teacher_id', $ownerId)
            ->where('groups.has_pricing', true)
            ->get();

        if ($groups->isEmpty()) {
            return null;
        }

        // Build invoice items from group pricings
        $items = [];
        $subtotal = 0.0;

        foreach ($groups as $group) {
            $item = $this->buildInvoiceItem($student, $group, $periodStart, $periodEnd);

            if ($item) {
                $items[] = $item;
                $subtotal += $item['total'];
            }
        }

        if (empty($items)) {
            return null;
        }

        return DB::transaction(function () use (
            $student, $ownerId, $periodStart, $periodEnd, $items, $subtotal, $user
        ) {
            $periodLabel = $this->buildPeriodLabel($periodStart, $periodEnd);

            $invoice = Invoice::create([
                'invoice_number' => $this->numberGenerator->invoice(),
                'student_id' => $student->id,
                'teacher_id' => $ownerId,
                'branch_id' => $student->branch_id,
                'period_start' => $periodStart->toDateString(),
                'period_end' => $periodEnd->toDateString(),
                'period_label' => $periodLabel,
                'subtotal' => $subtotal,
                'discount' => 0,
                'extra' => 0,
                'total' => $subtotal,
                'paid_amount' => 0,
                'remaining_amount' => $subtotal,
                'status' => 'unpaid',
                'breakdown' => ['groups' => $items],
                'created_by' => $user->id,
            ]);

            foreach ($items as $item) {
                InvoiceItem::create([
                    'invoice_id' => $invoice->id,
                    'group_id' => $item['group_id'],
                    'description' => $item['description'],
                    'pricing_type' => $item['pricing_type'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'total' => $item['total'],
                    'details' => $item['details'] ?? null,
                ]);
            }

            return $invoice;
        });
    }

    /**
     * Build an invoice item for a single group + student + period.
     * Returns null when there's nothing to charge.
     */
    protected function buildInvoiceItem(
        Student $student,
        Group $group,
        Carbon $periodStart,
        Carbon $periodEnd
    ): ?array {
        $pricingType = PricingTypeEnum::tryFrom($group->pricing_type);
        $amount = (float) $group->pricing_amount;

        if ($amount <= 0 || !$pricingType) {
            return null;
        }

        return match ($pricingType) {
            PricingTypeEnum::FIXED_MONTHLY => [
                'group_id' => $group->id,
                'description' => "شهري ثابت - {$group->name}",
                'pricing_type' => $pricingType->value,
                'quantity' => 1,
                'unit_price' => $amount,
                'total' => $amount,
                'details' => [
                    'group_name' => $group->name,
                    'type' => 'fixed_monthly',
                ],
            ],

            PricingTypeEnum::PER_SESSION => $this->buildPerSessionItem($student, $group, $amount, $periodStart, $periodEnd),

            PricingTypeEnum::PER_SUBJECT => $this->buildPerSubjectItem($student, $group, $amount),

            PricingTypeEnum::PER_GRADE => [
                'group_id' => $group->id,
                'description' => "حسب الصف - {$group->name}",
                'pricing_type' => $pricingType->value,
                'quantity' => 1,
                'unit_price' => $amount,
                'total' => $amount,
                'details' => [
                    'group_name' => $group->name,
                    'grade' => $group->grade,
                    'type' => 'per_grade',
                ],
            ],
        };
    }

    /**
     * Per-session: count distinct schedules attended in the period × amount.
     */
    protected function buildPerSessionItem(
        Student $student,
        Group $group,
        float $amount,
        Carbon $periodStart,
        Carbon $periodEnd
    ): ?array {
        $sessionsCount = Attendance::query()
            ->where('student_id', $student->id)
            ->where('group_id', $group->id)
            ->whereBetween('attendance_date', [$periodStart, $periodEnd])
            ->whereIn('status', ['present', 'late'])
            ->distinct('schedule_id')
            ->count('schedule_id');

        if ($sessionsCount <= 0) {
            return null;
        }

        return [
            'group_id' => $group->id,
            'description' => "حضور {$sessionsCount} حصة - {$group->name}",
            'pricing_type' => PricingTypeEnum::PER_SESSION->value,
            'quantity' => $sessionsCount,
            'unit_price' => $amount,
            'total' => $sessionsCount * $amount,
            'details' => [
                'group_name' => $group->name,
                'sessions' => $sessionsCount,
                'type' => 'per_session',
            ],
        ];
    }

    /**
     * Per-subject: count distinct subjects in the group × amount.
     */
    protected function buildPerSubjectItem(
        Student $student,
        Group $group,
        float $amount
    ): ?array {
        $subjectsCount = GroupSchedule::query()
            ->where('group_id', $group->id)
            ->distinct('subject_id')
            ->count('subject_id');

        if ($subjectsCount <= 0) {
            return null;
        }

        return [
            'group_id' => $group->id,
            'description' => "{$subjectsCount} مواد - {$group->name}",
            'pricing_type' => PricingTypeEnum::PER_SUBJECT->value,
            'quantity' => $subjectsCount,
            'unit_price' => $amount,
            'total' => $subjectsCount * $amount,
            'details' => [
                'group_name' => $group->name,
                'subjects' => $subjectsCount,
                'type' => 'per_subject',
            ],
        ];
    }

    /**
     * Human-readable label for a billing period.
     * e.g. "سبتمبر 2026"
     */
    protected function buildPeriodLabel(Carbon $start, Carbon $end): string
    {
        $months = [
            1 => 'يناير', 2 => 'فبراير', 3 => 'مارس', 4 => 'أبريل',
            5 => 'مايو', 6 => 'يونيو', 7 => 'يوليو', 8 => 'أغسطس',
            9 => 'سبتمبر', 10 => 'أكتوبر', 11 => 'نوفمبر', 12 => 'ديسمبر',
        ];

        if ($start->month === $end->month && $start->year === $end->year) {
            return $months[$start->month] . ' ' . $start->year;
        }

        return $start->format('Y-m-d') . ' → ' . $end->format('Y-m-d');
    }

    /**
     * Generate invoices for every active student in scope.
     * Returns count of generated invoices.
     */
    public function generateBulk(
        Teacher $user,
        Carbon $periodStart,
        Carbon $periodEnd,
        ?int $branchId = null,
        ?array $groupIds = null
    ): int {
        $ownerId = $user->getOwnerId();
        $accessibleBranches = $user->getAccessibleBranchIds();

        $studentsQuery = Student::query()
            ->where('teacher_id', $ownerId)
            ->where('is_active', 1);

        // Branch scope for non-owner
        if (!$user->isOwner()) {
            $studentsQuery->whereIn('branch_id', $accessibleBranches);
        }

        // Optional branch filter
        if ($branchId) {
            $studentsQuery->where('branch_id', $branchId);
        }

        // Optional group filter
        if (!empty($groupIds)) {
            $studentsQuery->whereHas('activeGroups', function ($q) use ($groupIds) {
                $q->whereIn('groups.id', $groupIds);
            });
        }

        $count = 0;

        $studentsQuery->chunk(50, function ($students) use (
            $periodStart, $periodEnd, $user, &$count
        ) {
            foreach ($students as $student) {
                $invoice = $this->generateForStudent($student, $periodStart, $periodEnd, $user);
                if ($invoice) {
                    $count++;
                }
            }
        });

        return $count;
    }
}
