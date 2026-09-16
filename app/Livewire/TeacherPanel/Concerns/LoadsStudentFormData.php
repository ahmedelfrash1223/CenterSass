<?php

namespace App\Livewire\TeacherPanel\Concerns;

use App\Enums\Teacher\GradeEnum;
use App\Models\Branch;
use App\Models\Group;

trait LoadsStudentFormData
{
    /**
     * Load shared data for create/update student forms:
     * branches, groups (with capacity flags), grades.
     */
    protected function loadStudentFormData(): array
    {
        $user = auth('teacher')->user();
        $ownerId = $user->getOwnerId();
        $accessibleBranchIds = $user->getAccessibleBranchIds();

        $groups = Group::where('teacher_id', $ownerId)
            ->when(!$user->isOwner(), fn($q) => $q->whereIn('branch_id', $accessibleBranchIds))
            ->withCount(['students' => fn($q) => $q->where('group_student.status', 'active')])
            ->orderBy('name')
            ->get()
            ->map(function ($group) {
                $group->is_full = $group->max_students > 0
                    && $group->students_count >= $group->max_students;
                return $group;
            });

        return [
            'grades' => GradeEnum::options(),
            'branches' => Branch::whereIn('id', $accessibleBranchIds)
                ->where('is_active', 1)
                ->orderBy('name')
                ->get(),
            'groups' => $groups,
        ];
    }
}
