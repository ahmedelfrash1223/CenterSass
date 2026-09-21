<?php

namespace App\Services\Teacher;

use App\Enums\Teacher\DiscountTypeEnum;
use App\Enums\Teacher\PricingTypeEnum;
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

    public function ensureForStudent(
        Student $student,
        Carbon $periodStart,
        Carbon $periodEnd,
        Teacher $user
    ): ?Invoice {
        $ownerId = $user->getOwnerId();

        if ($student->teacher_id !== $ownerId) {
            return null;
        }

        $existing = Invoice::where('student_id', $student->id)
            ->where('teacher_id', $ownerId)
            ->whereDate('period_start', $periodStart->toDateString())
            ->whereDate('period_end', $periodEnd->toDateString())
            ->whereNull('cancelled_at')
            ->with('items')
            ->first();

        if ($existing) {
            return $existing;
        }

        $groups = $student->activeGroups()
            ->where('groups.teacher_id', $ownerId)
            ->where('groups.has_pricing', true)
            ->get();

        if ($groups->isEmpty()) {
            return null;
        }

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
            $invoice = Invoice::create([
                'invoice_number'   => $this->numberGenerator->invoice(),
                'student_id'       => $student->id,
                'teacher_id'       => $ownerId,
                'branch_id'        => $student->branch_id,
                'period_start'     => $periodStart->toDateString(),
                'period_end'       => $periodEnd->toDateString(),
                'period_label'     => $this->buildPeriodLabel($periodStart, $periodEnd),
                'subtotal'         => $subtotal,
                'discount_type'    => DiscountTypeEnum::FIXED->value,
                'discount_value'   => 0,
                'discount'         => 0,
                'discount_reason'  => null,
                'extra'            => 0,
                'total'            => $subtotal,
                'paid_amount'      => 0,
                'remaining_amount' => $subtotal,
                'status'           => 'unpaid',
                'breakdown'        => ['groups' => $items],
                'created_by'       => $user->id,
            ]);

            foreach ($items as $item) {
                InvoiceItem::create([
                    'invoice_id'        => $invoice->id,
                    'group_id'          => $item['group_id'],
                    'description'       => $item['description'],
                    'pricing_type'      => $item['pricing_type'],
                    'quantity'          => $item['quantity'],
                    'unit_price'        => $item['unit_price'],
                    'total'             => $item['total'],
                    'discount_type'     => DiscountTypeEnum::FIXED->value,
                    'discount_value'    => 0,
                    'discount_amount'   => 0,
                    'discount_reason'   => null,
                    'discounted_total'  => $item['total'],
                    'paid_amount'       => 0,
                    'remaining_amount'  => $item['total'],
                    'details'           => $item['details'] ?? null,
                ]);
            }

            return $invoice->load('items');
        });
    }

    public function generateForStudent(
        Student $student,
        Carbon $periodStart,
        Carbon $periodEnd,
        Teacher $user
    ): ?Invoice {
        return $this->ensureForStudent($student, $periodStart, $periodEnd, $user);
    }

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
                'group_id'     => $group->id,
                'description'  => "شهري ثابت - {$group->name}",
                'pricing_type' => $pricingType->value,
                'quantity'     => 1,
                'unit_price'   => $amount,
                'total'        => $amount,
                'details'      => ['group_name' => $group->name, 'type' => 'fixed_monthly'],
            ],
            PricingTypeEnum::PER_SESSION => $this->buildPerSessionItem($student, $group, $amount, $periodStart, $periodEnd),
            PricingTypeEnum::PER_SUBJECT => $this->buildPerSubjectItem($student, $group, $amount),
            PricingTypeEnum::PER_GRADE   => [
                'group_id'     => $group->id,
                'description'  => "حسب الصف - {$group->name}",
                'pricing_type' => $pricingType->value,
                'quantity'     => 1,
                'unit_price'   => $amount,
                'total'        => $amount,
                'details'      => ['group_name' => $group->name, 'grade' => $group->grade, 'type' => 'per_grade'],
            ],
        };
    }

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
            'group_id'     => $group->id,
            'description'  => "حضور {$sessionsCount} حصة - {$group->name}",
            'pricing_type' => PricingTypeEnum::PER_SESSION->value,
            'quantity'     => $sessionsCount,
            'unit_price'   => $amount,
            'total'        => $sessionsCount * $amount,
            'details'      => ['group_name' => $group->name, 'sessions' => $sessionsCount, 'type' => 'per_session'],
        ];
    }

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
            'group_id'     => $group->id,
            'description'  => "{$subjectsCount} مواد - {$group->name}",
            'pricing_type' => PricingTypeEnum::PER_SUBJECT->value,
            'quantity'     => $subjectsCount,
            'unit_price'   => $amount,
            'total'        => $subjectsCount * $amount,
            'details'      => ['group_name' => $group->name, 'subjects' => $subjectsCount, 'type' => 'per_subject'],
        ];
    }

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

        if (!$user->isOwner()) {
            $studentsQuery->whereIn('branch_id', $accessibleBranches);
        }

        if ($branchId) {
            $studentsQuery->where('branch_id', $branchId);
        }

        if (!empty($groupIds)) {
            $studentsQuery->whereHas('activeGroups', fn($q) => $q->whereIn('groups.id', $groupIds));
        }

        $count = 0;

        $studentsQuery->chunk(50, function ($students) use ($periodStart, $periodEnd, $user, &$count) {
            foreach ($students as $student) {
                if ($this->ensureForStudent($student, $periodStart, $periodEnd, $user)) {
                    $count++;
                }
            }
        });

        return $count;
    }
}
