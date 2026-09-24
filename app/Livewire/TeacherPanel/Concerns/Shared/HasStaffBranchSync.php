<?php

namespace App\Livewire\TeacherPanel\Concerns\Shared;

trait HasStaffBranchSync
{
    /**
     * Ensure every selected branch id belongs to the current user's scope.
     */
    protected function ensureBranchesAuthorized(array $branchIds, string $errorField = 'branch_ids'): bool
    {
        if (empty($branchIds)) {
            return true;
        }

        $accessible = array_map('strval', auth('teacher')->user()->getAccessibleBranchIds());
        $unauthorized = array_diff($branchIds, $accessible);

        if (!empty($unauthorized)) {
            $this->addError($errorField, 'إحدى الفروع غير متاحة لك.');
            return false;
        }

        return true;
    }

    /**
     * Sync staff branches with created_by tracking.
     */
    protected function syncStaffBranches(\App\Models\Teacher $teacher, array $branchIds): void
    {
        if (empty($branchIds)) {
            return;
        }

        $teacher->syncBranches($branchIds, auth('teacher')->id());
    }
}
