<?php

namespace App\Services\Api\Student\Link;

use App\Models\Student;
use App\Models\StudentAccount;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Validation\ValidationException;

class StudentLinkService
{

    public function linkByBarcode(StudentAccount $account, string $barcode): array
    {
        $barcode = trim($barcode);

        if ($barcode === '') {
            throw ValidationException::withMessages([
                'barcode' => ['الباركود مطلوب.'],
            ]);
        }

        $student = Student::query()
            ->where('barcode_digits', $barcode)
            ->orWhere('code', $barcode)
            ->first();

        if (! $student) {
            throw ValidationException::withMessages([
                'barcode' => ['لم يتم العثور على طالب بهذا الباركود.'],
            ]);
        }

        if (! $student->is_active) {
            throw ValidationException::withMessages([
                'barcode' => ['حساب الطالب غير نشط. تواصل مع المدرس.'],
            ]);
        }

        $alreadyLinked = $account->students()
            ->where('students.id', $student->id)
            ->exists();

        if (! $alreadyLinked) {
            $account->students()->attach($student->id, [
                'linked_at' => now(),
            ]);
        }

        $student = $account->students()
            ->with(['teacher', 'branch'])
            ->where('students.id', $student->id)
            ->first();

        return [
            'student'        => $student,
            'already_linked' => $alreadyLinked,
        ];
    }


    public function unlink(StudentAccount $account, int $studentId): void
    {
        $exists = $account->students()
            ->where('students.id', $studentId)
            ->exists();

        if (! $exists) {
            throw ValidationException::withMessages([
                'student_id' => ['هذا الطالب غير مربوط بحسابك.'],
            ]);
        }

        $account->students()->detach($studentId);
    }


    public function linkedStudents(StudentAccount $account): Collection
    {
        return $account->students()
            ->with(['teacher', 'branch'])
            ->orderByDesc('student_account_student.linked_at')
            ->get();
    }
}
