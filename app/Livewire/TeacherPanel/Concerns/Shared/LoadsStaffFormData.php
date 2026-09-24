<?php

namespace App\Livewire\TeacherPanel\Concerns\Shared;

use App\Enums\Admin\PackageFeatureKeyEnum;
use App\Enums\Teacher\GradeEnum;
use App\Enums\Teacher\TeacherPermissionEnum;
use App\Enums\Teacher\TeacherRoleEnum;
use App\Models\Branch;
use App\Models\Teacher;
use App\Models\TeacherSubject;

trait LoadsStaffFormData
{
    protected function loadStaffFormData(bool $includeLimit = true): array
    {
        $user = auth('teacher')->user();
        $ownerId = $user->getOwnerId();
        $accessibleBranchIds = $user->getAccessibleBranchIds();

        $availableGrades = TeacherSubject::query()
            ->where('teacher_id', $ownerId)
            ->where('is_active', 1)
            ->whereNotNull('grade')
            ->distinct()
            ->pluck('grade')
            ->filter()
            ->values();

        $availableSubjects = TeacherSubject::query()
            ->where('teacher_id', $ownerId)
            ->where('is_active', 1)
            ->get()
            ->map(fn($s) => [
                'id'            => $s->id,
                'grade'         => $s->grade,
                'grade_label'   => GradeEnum::tryFrom($s->grade)?->label() ?? $s->grade,
                'subject_label' => $s->subject_label,
            ])
            ->values();

        $data = [
            'branches' => Branch::whereIn('id', $accessibleBranchIds)
                ->where('is_active', 1)
                ->orderBy('name')
                ->get(),

            'roles' => [
                TeacherRoleEnum::STAFF->value     => TeacherRoleEnum::STAFF->label(),
                TeacherRoleEnum::ASSISTANT->value => TeacherRoleEnum::ASSISTANT->label(),
            ],

            'groupedPermissions' => TeacherPermissionEnum::grouped(),

            'availableGrades'   => $availableGrades,
            'availableSubjects' => $availableSubjects,
            'gradeOptions'      => GradeEnum::options(),
        ];

        if ($includeLimit) {
            $total = Teacher::where('owner_id', $ownerId)->count();

            $data['limit'] = $user->checkFeatureLimit(
                PackageFeatureKeyEnum::MAX_EMPLOYEES->value,
                $total
            );
        }

        return $data;
    }
}
