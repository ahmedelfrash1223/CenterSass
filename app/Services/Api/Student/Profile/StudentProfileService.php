<?php

namespace App\Services\Api\Student\Profile;

use App\Models\Student;
use App\Models\StudentAccount;
use Illuminate\Database\Eloquent\Collection;

class StudentProfileService
{

    public function allForAccount(StudentAccount $account): Collection
    {
        return $account->students()
            ->with(['teacher', 'branch', 'profile'])
            ->orderByDesc('student_account_student.linked_at')
            ->get();
    }


    public function findForAccount(StudentAccount $account, int $studentId): Student
    {
        $isLinked = $account->students()
            ->where('students.id', $studentId)
            ->exists();

        if (! $isLinked) {
            abort(404, 'هذا الطالب غير مربوط بحسابك.');
        }

        return Student::query()
            ->with(['teacher', 'branch', 'profile'])
            ->findOrFail($studentId);
    }
}
