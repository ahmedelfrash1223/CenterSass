<?php

namespace App\Traits\Teacher;

use App\Models\Branch;

trait HasBranchAccess
{
    public function getAccessibleBranchIds(): array
    {
        if ($this->isOwner()) {
            return Branch::where('teacher_id', $this->id)->pluck('id')->toArray();
        }

        return $this->branches()->pluck('branches.id')->toArray();
    }

    /**
     * Check whether the current teacher has access to a specific branch.
     * Accepts null safely — returns false for null/0.
     */
    public function hasAccessToBranch(?int $branchId): bool
    {
        if ($branchId === null || $branchId === 0) {
            return false;
        }

        if ($this->isOwner()) {
            return Branch::where('teacher_id', $this->id)
                ->where('id', $branchId)
                ->exists();
        }

        return $this->branches()->where('branches.id', $branchId)->exists();
    }

    public function hasAccessToTeacher(self $teacher): bool
    {
        if ($teacher->id === $this->id) {
            return true;
        }

        if ($this->isOwner()) {
            return $teacher->id === $this->id || $teacher->owner_id === $this->id;
        }

        if ($teacher->id === $this->getOwnerId()) {
            return true;
        }

        if ($teacher->owner_id !== $this->getOwnerId()) {
            return false;
        }

        $myBranchIds = $this->getAccessibleBranchIds();

        if (empty($myBranchIds)) {
            return false;
        }

        return $teacher->branches()->whereIn('branches.id', $myBranchIds)->exists();
    }

    public function syncBranches(array $branchIds, ?int $createdBy = null): void
    {
        $syncData = [];
        foreach ($branchIds as $branchId) {
            $syncData[$branchId] = [
                'created_by' => $createdBy,
            ];
        }
        $this->branches()->sync($syncData);
    }
}
