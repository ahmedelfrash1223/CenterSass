<?php

namespace App\Services\Teacher;

use App\Enums\Teacher\InvoiceStatusEnum;
use App\Models\Invoice;
use App\Models\Student;
use App\Models\Teacher;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

class PaymentQueryService
{
    public function __construct(
        private readonly InvoiceService $invoiceService
    ) {}

    public function searchStudents(Teacher $user, string $query): Builder
    {
        $ownerId = $user->getOwnerId();
        $accessibleBranches = $user->getAccessibleBranchIds();

        $builder = Student::query()
            ->where('teacher_id', $ownerId)
            ->with(['branch', 'activeGroups'])
            ->withCount(['activeGroups as groups_count']);

        if (! $user->isOwner()) {
            empty($accessibleBranches)
                ? $builder->whereRaw('1 = 0')
                : $builder->whereIn('branch_id', $accessibleBranches);
        }

        $query = trim($query);

        if ($query === '') {
            return $builder->orderBy('name');
        }

        $upper = strtoupper($query);
        $lower = strtolower($query);
        $digits = preg_replace('/\D/', '', $query);

        $builder->where(function ($q) use ($query, $upper, $lower, $digits) {
            $q->where('name', 'like', "%{$query}%")
                ->orWhere('username', 'like', "%{$query}%")
                ->orWhere('email', 'like', "%{$query}%")
                ->orWhere('phone', 'like', "%{$query}%")
                ->orWhere('code', $upper)
                ->orWhere('code', $lower)
                ->orWhere('uuid', $lower);

            if (strlen($digits) >= 4) {
                $q->orWhere('barcode_digits', $digits)
                    ->orWhere('barcode_digits', 'like', "%{$digits}%");
            }
        });

        return $builder->orderBy('name');
    }

    public function dueGroupsForStudent(Teacher $user, Student $student, ?string $month = null): array
    {
        $ownerId = $user->getOwnerId();

        if ($student->teacher_id !== $ownerId) {
            return [];
        }

        if (! $user->isOwner() && ! in_array($student->branch_id, $user->getAccessibleBranchIds())) {
            return [];
        }

        $date = $month
            ? Carbon::createFromFormat('Y-m', $month)
            : Carbon::now();

        $periodStart = $date->copy()->startOfMonth();
        $periodEnd = $date->copy()->endOfMonth();

        $invoice = $this->invoiceService->ensureForStudent($student, $periodStart, $periodEnd, $user);

        if (! $invoice) {
            return [];
        }

        $invoice = Invoice::with(['items.group'])->find($invoice->id);

        $activeGroups = $student->activeGroups()
            ->where('groups.teacher_id', $ownerId)
            ->where('groups.has_pricing', true)
            ->get();

        $result = [];

        foreach ($activeGroups as $group) {
            $item = $invoice->items->firstWhere('group_id', $group->id);

            if ($item) {
                $remaining = (float) $item->remaining_amount;
                $paid = (float) $item->paid_amount;
                $amount = (float) $item->total;

                $result[] = [
                    'group' => $group,
                    'invoice' => $invoice,
                    'item' => $item,
                    'item_id' => $item->id,
                    'amount' => $amount,
                    'paid' => $paid,
                    'remaining' => $remaining,
                    'is_fully_paid' => $remaining <= 0,
                    'status' => $remaining <= 0
                        ? 'paid'
                        : ($paid > 0 ? 'partial' : 'unpaid'),
                ];
            }
        }

        return $result;
    }

    public function studentsUnpaid(Teacher $user, ?string $month = null): Builder
    {
        $ownerId = $user->getOwnerId();
        $accessibleBranches = $user->getAccessibleBranchIds();

        $date = $month ? Carbon::createFromFormat('Y-m', $month) : Carbon::now();
        $periodStart = $date->copy()->startOfMonth();
        $periodEnd = $date->copy()->endOfMonth();

        $periodStartStr = $periodStart->toDateString();
        $periodEndStr = $periodEnd->toDateString();

        $builder = Student::query()
            ->where('teacher_id', $ownerId)
            ->where('is_active', 1)
            ->where(function ($q) use ($periodStartStr, $periodEndStr, $ownerId) {
                $q->whereHas('invoices', function ($sub) use ($periodStartStr, $periodEndStr) {
                    $sub->whereDate('period_start', $periodStartStr)
                        ->whereDate('period_end', $periodEndStr)
                        ->where('status', InvoiceStatusEnum::UNPAID->value)
                        ->whereNull('cancelled_at')
                        ->where('remaining_amount', '>', 0);
                })
                    ->orWhere(function ($sub) use ($periodStartStr, $periodEndStr, $ownerId) {
                        $sub->whereHas('activeGroups', function ($g) use ($ownerId) {
                            $g->where('groups.teacher_id', $ownerId)
                                ->where('groups.has_pricing', true);
                        })
                            ->whereDoesntHave('invoices', function ($inv) use ($periodStartStr, $periodEndStr) {
                                $inv->whereDate('period_start', $periodStartStr)
                                    ->whereDate('period_end', $periodEndStr)
                                    ->whereNull('cancelled_at');
                            });
                    });
            })
            ->with(['branch', 'activeGroups'])
            ->withCount(['activeGroups as groups_count']);

        if (! $user->isOwner()) {
            empty($accessibleBranches)
                ? $builder->whereRaw('1 = 0')
                : $builder->whereIn('branch_id', $accessibleBranches);
        }

        return $builder->orderBy('name');
    }

    public function studentsPartial(Teacher $user, ?string $month = null): Builder
    {
        $ownerId = $user->getOwnerId();
        $accessibleBranches = $user->getAccessibleBranchIds();

        $date = $month ? Carbon::createFromFormat('Y-m', $month) : Carbon::now();
        $periodStart = $date->copy()->startOfMonth();
        $periodEnd = $date->copy()->endOfMonth();

        $periodStartStr = $periodStart->toDateString();
        $periodEndStr = $periodEnd->toDateString();

        $builder = Student::query()
            ->where('teacher_id', $ownerId)
            ->where('is_active', 1)
            ->whereHas('invoices', function ($sub) use ($periodStartStr, $periodEndStr) {
                $sub->whereDate('period_start', $periodStartStr)
                    ->whereDate('period_end', $periodEndStr)
                    ->where('status', InvoiceStatusEnum::PARTIAL->value)
                    ->whereNull('cancelled_at')
                    ->where('remaining_amount', '>', 0);
            })
            ->with(['branch', 'activeGroups'])
            ->withCount(['activeGroups as groups_count']);

        if (! $user->isOwner()) {
            empty($accessibleBranches)
                ? $builder->whereRaw('1 = 0')
                : $builder->whereIn('branch_id', $accessibleBranches);
        }

        return $builder->orderBy('name');
    }
}
