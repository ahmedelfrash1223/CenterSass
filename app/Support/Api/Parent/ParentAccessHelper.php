<?php

namespace App\Support\Api\Parent;

use App\Models\Student;
use App\Models\StudentAccount;

class ParentAccessHelper
{

    public static function ensureChildBelongsToParent(StudentAccount $parent, int $studentId): Student
    {
        $child = $parent->children()->find($studentId);

        if (! $child) {
            abort(404, 'هذا الطالب غير مرتبط بحسابك.');
        }

        return $child;
    }
}
