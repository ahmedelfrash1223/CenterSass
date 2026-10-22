<?php

namespace App\Services\Teacher;

use App\Enums\Teacher\RevenueSourceEnum;
use App\Models\Admin;
use App\Models\Revenue;
use App\Models\Teacher;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class RevenueService
{
    public function record(
        Teacher|Admin $user,
        RevenueSourceEnum $source,
        float $amount,
        ?int $sourceId = null,
        ?int $branchId = null,
        ?int $studentId = null,
        ?string $description = null,
        array $meta = [],
        \DateTimeInterface|string|null $occurredAt = null,
        ?int $ownerTeacherId = null,
    ): Revenue {
        $teacherId = $user instanceof Teacher ? $user->getOwnerId() : $ownerTeacherId;

        if ($teacherId === null) {
            throw new InvalidArgumentException('An owning teacher ID is required when recording revenue as an admin.');
        }

        return DB::transaction(function () use (
            $user, $teacherId, $source, $amount, $sourceId, $branchId, $studentId,
            $description, $meta, $occurredAt
        ) {
            return Revenue::create([
                'teacher_id' => $teacherId,
                'recorded_by' => $user instanceof Teacher ? $user->id : null,
                'recorded_by_admin_id' => $user instanceof Admin ? $user->id : null,
                'source' => $source->value,
                'source_id' => $sourceId,
                'amount' => $amount,
                'branch_id' => $branchId,
                'student_id' => $studentId,
                'description' => $description,
                'meta' => $meta,
                'occurred_at' => $occurredAt
                    ? Carbon::parse($occurredAt)
                    : now(),
            ]);
        });
    }

    public function recordForSource(
        Teacher $user,
        RevenueSourceEnum $source,
        int $sourceId,
        float $amount,
        array $context = [],
    ): Revenue {
        return $this->record(
            user: $user,
            source: $source,
            amount: $amount,
            sourceId: $sourceId,
            branchId: $context['branch_id'] ?? null,
            studentId: $context['student_id'] ?? null,
            description: $context['description'] ?? null,
            meta: $context['meta'] ?? [],
            occurredAt: $context['occurred_at'] ?? null,
        );
    }

    public function updateAmount(Revenue $revenue, float $amount): bool
    {
        return $revenue->update(['amount' => $amount]);
    }

    public function deleteForSource(RevenueSourceEnum $source, int $sourceId): int
    {
        return Revenue::where('source', $source->value)
            ->where('source_id', $sourceId)
            ->delete();
    }
}
