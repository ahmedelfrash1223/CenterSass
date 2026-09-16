<?php

namespace App\Http\Controllers\TeacherPanel\Concerns;

use App\Models\Teacher;

trait EnsuresTeacherAccess
{
    protected function ensureTeacherAccess(Teacher $staff): void
    {
        abort_unless(
            $this->currentTeacher()->hasAccessToTeacher($staff),
            404
        );
    }
}
