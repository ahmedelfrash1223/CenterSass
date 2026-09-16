<?php

namespace App\Livewire\TeacherPanel\Concerns;

use App\Enums\Teacher\GradeEnum;
use App\Enums\Teacher\GroupStatusEnum;
use App\Models\Branch;
use App\Models\Teacher;
use App\Models\TeacherSubject;

trait LoadsGroupFormData
{
    /**
     * Load shared data for both create and update group forms:
     * branches, teachers (scoped by access), subjects, grades, statuses, days.
     */
    protected function loadGroupFormData(): array
    {
        $user = auth('teacher')->user();
        $ownerId = $user->getOwnerId();
        $accessibleBranchIds = $user->getAccessibleBranchIds();

        // Subjects scoped to the selected grade
        $teacherSubjects = collect();
        if (!empty($this->grade)) {
            $teacherSubjects = TeacherSubject::where('teacher_id', $ownerId)
                ->where('grade', $this->grade)
                ->get();
        }

        // Teachers visible: owner + staff in shared branches (for non-owner)
        $teachersQuery = Teacher::where(function ($q) use ($ownerId) {
            $q->where('id', $ownerId)->orWhere('owner_id', $ownerId);
        })->where('is_active', 1);

        if (!$user->isOwner()) {
            $teachersQuery->where(function ($q) use ($user, $accessibleBranchIds) {
                $q->where('id', $user->getOwnerId())
                    ->orWhereHas('branches', function ($sub) use ($accessibleBranchIds) {
                        $sub->whereIn('branches.id', $accessibleBranchIds);
                    });
            });
        }

        return [
            'branches' => Branch::whereIn('id', $accessibleBranchIds)
                ->where('is_active', 1)
                ->orderBy('name')
                ->get(),
            'teachers' => $teachersQuery->orderBy('role')->orderBy('name')->get(),
            'teacherSubjects' => $teacherSubjects,
            'grades' => GradeEnum::options(),
            'statuses' => GroupStatusEnum::options(),
            'days' => [
                'Saturday' => 'السبت',
                'Sunday' => 'الأحد',
                'Monday' => 'الإثنين',
                'Tuesday' => 'الثلاثاء',
                'Wednesday' => 'الأربعاء',
                'Thursday' => 'الخميس',
                'Friday' => 'الجمعة',
            ],
        ];
    }
}
