<?php

namespace App\Livewire\TeacherPanel\Concerns;

use App\Models\Student;

trait HasStudentGroupSync
{
    /**
     * Sync student groups with joined_at, status and created_by tracking.
     */
    protected function syncStudentGroups(Student $student, array $groupIds): void
    {
        $syncData = [];

        foreach ($groupIds as $groupId) {
            $syncData[$groupId] = [
                'joined_at' => now()->toDateString(),
                'status' => 'active',
                'created_by' => auth('teacher')->id(),
            ];
        }

        $student->groups()->sync($syncData);
    }
}
