<?php

namespace App\Services\Api\Parent;

use App\Models\Student;
use App\Models\StudentAccount;

class ParentService
{
    public function getChildren(StudentAccount $parent)
    {
        return $parent->children()->with(['teacher', 'branch', 'profile'])->get();
    }

    public function ensureChildBelongsToParent(StudentAccount $parent, int $studentId): Student
    {
        $child = $parent->children()->find($studentId);

        if (! $child) {
            abort(404, 'هذا الطالب غير مرتبط بحسابك.');
        }

        return $child;
    }
}
