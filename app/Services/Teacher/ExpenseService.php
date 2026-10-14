<?php

namespace App\Services\Teacher;

use App\Enums\Teacher\ExpenseSourceEnum;
use App\Models\Expense;
use App\Models\Teacher;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ExpenseService
{
    public function record(
        Teacher $user,
        ExpenseSourceEnum $source,
        float $amount,
        ?int $sourceId = null,
        ?int $branchId = null,
        ?int $teacherPayeeId = null,
        ?int $teacherPayrollId = null,
        ?string $description = null,
        array $meta = [],
        \DateTimeInterface|string|null $occurredAt = null,
    ): Expense {
        return DB::transaction(function () use (
            $user, $source, $amount, $sourceId, $branchId,
            $teacherPayeeId, $teacherPayrollId, $description, $meta, $occurredAt
        ) {
            return Expense::create([
                'teacher_id' => $user->getOwnerId(),
                'recorded_by' => $user->id,
                'source' => $source->value,
                'source_id' => $sourceId,
                'amount' => $amount,
                'branch_id' => $branchId,
                'teacher_payee_id' => $teacherPayeeId,
                'teacher_payroll_id' => $teacherPayrollId,
                'description' => $description,
                'meta' => $meta,
                'occurred_at' => $occurredAt ? Carbon::parse($occurredAt) : now(),
            ]);
        });
    }

    public function deleteForSource(ExpenseSourceEnum $source, int $sourceId): int
    {
        return Expense::where('source', $source->value)
            ->where('source_id', $sourceId)
            ->delete();
    }
}
