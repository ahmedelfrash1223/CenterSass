<?php

namespace App\Http\Controllers\TeacherPanel\Concerns;

use App\Models\Branch;

trait EnsuresBranchAccess
{
    protected function ensureBranchAccess(Branch $branch): void
    {
        $teacher = $this->currentTeacher();

        abort_unless(
            $branch->teacher_id === $teacher->getOwnerId(),
            404
        );
    }
}
