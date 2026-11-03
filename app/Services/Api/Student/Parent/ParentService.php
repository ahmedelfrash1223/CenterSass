<?php

namespace App\Services\Api\Student\Parent;

use App\Models\Student;
use App\Models\StudentAccount;

class ParentService
{

    public function prepareChildAccount(StudentAccount $parent, int $studentId): StudentAccount
    {
        $child = $parent->children()->find($studentId);

        if (! $child) {
            abort(404, 'هذا الطالب غير مرتبط بحسابك.');
        }

        $fakeAccount = new StudentAccount();
        $fakeAccount->id = $parent->id;
        $fakeAccount->setRelation('students', collect([$child]));

        return $fakeAccount;
    }
}
