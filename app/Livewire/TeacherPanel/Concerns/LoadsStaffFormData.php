<?php

namespace App\Livewire\TeacherPanel\Concerns;

use App\Enums\Admin\PackageFeatureKeyEnum;
use App\Enums\Teacher\TeacherPermissionEnum;
use App\Enums\Teacher\TeacherRoleEnum;
use App\Models\Branch;
use App\Models\Teacher;

trait LoadsStaffFormData
{
    /**
     * Load shared data for create/update staff forms:
     * branches (scoped), roles, grouped permissions, and plan limit.
     */
    protected function loadStaffFormData(bool $includeLimit = true): array
    {
        $user = auth('teacher')->user();
        $ownerId = $user->getOwnerId();
        $accessibleBranchIds = $user->getAccessibleBranchIds();

        $data = [
            'branches' => Branch::whereIn('id', $accessibleBranchIds)
                ->where('is_active', 1)
                ->orderBy('name')
                ->get(),
            'roles' => [
                TeacherRoleEnum::STAFF->value => TeacherRoleEnum::STAFF->label(),
                TeacherRoleEnum::ASSISTANT->value => TeacherRoleEnum::ASSISTANT->label(),
            ],
            'groupedPermissions' => TeacherPermissionEnum::grouped(),
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
