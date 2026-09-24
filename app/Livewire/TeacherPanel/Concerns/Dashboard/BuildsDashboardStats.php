<?php

namespace App\Livewire\TeacherPanel\Concerns\Dashboard;

use App\Models\Group;
use App\Models\Student;
use App\Models\TeacherSubject;

trait BuildsDashboardStats
{
    protected function buildStats($ownerId, bool $isOwner, array $accessibleBranchIds): array
    {
        $studentsQuery = Student::where('teacher_id', $ownerId);
        $groupsQuery = Group::where('teacher_id', $ownerId);

        if (!$isOwner && !empty($accessibleBranchIds)) {
            $studentsQuery->whereIn('branch_id', $accessibleBranchIds);
            $groupsQuery->whereIn('branch_id', $accessibleBranchIds);
        } elseif (!$isOwner && empty($accessibleBranchIds)) {
            $studentsQuery->whereRaw('1 = 0');
            $groupsQuery->whereRaw('1 = 0');
        }

        return [
            'students_count' => $studentsQuery->count(),
            'groups_count'   => $groupsQuery->count(),
            'branches_count' => count($accessibleBranchIds),
            'subjects_count' => TeacherSubject::where('teacher_id', $ownerId)->count(),
        ];
    }
}
