<?php

namespace App\Http\Controllers\TeacherPanel\Concerns;

use App\Models\Group;

trait EnsuresGroupAccess
{
    protected function ensureGroupAccess(Group $group): void
    {
        $teacher = $this->currentTeacher();

        abort_unless(
            $group->teacher_id === $teacher->getOwnerId(),
            404
        );

        if ($teacher->isOwner()) {
            return;
        }

        $accessibleBranchIds = $teacher->getAccessibleBranchIds();

        abort_unless(
            !empty($accessibleBranchIds) && in_array($group->branch_id, $accessibleBranchIds),
            404
        );
    }
}
