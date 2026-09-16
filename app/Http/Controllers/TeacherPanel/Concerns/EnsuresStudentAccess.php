<?php

namespace App\Http\Controllers\TeacherPanel\Concerns;

use App\Models\Student;

trait EnsuresStudentAccess
{
    protected function ensureStudentAccess(Student $student): void
    {
        $teacher = $this->currentTeacher();

        abort_unless(
            $student->teacher_id === $teacher->getOwnerId(),
            404
        );

        if ($teacher->isOwner()) {
            return;
        }

        $accessibleBranchIds = $teacher->getAccessibleBranchIds();

        abort_unless(
            !empty($accessibleBranchIds) && in_array($student->branch_id, $accessibleBranchIds),
            404
        );
    }
}
