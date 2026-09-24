<?php

namespace App\Traits\Group;

use App\Models\Student;

trait ManagesStudents
{
    public function addStudent(Student $student): bool
    {
        $exists = $this->students()
            ->where('student_id', $student->id)
            ->wherePivot('status', 'active')
            ->exists();

        if ($exists) {
            return false;
        }

        if ($this->max_students > 0) {
            $current = $this->students()->wherePivot('status', 'active')->count();
            if ($current >= $this->max_students) {
                return false;
            }
        }

        $this->students()->attach($student->id, [
            'joined_at' => now()->toDateString(),
            'status' => 'active',
            'created_by' => auth('teacher')->id(),
        ]);

        return true;
    }

    public function removeStudent(Student $student, ?string $leftAt = null): void
    {
        $this->students()->updateExistingPivot($student->id, [
            'left_at' => $leftAt ?? now(),
            'status' => 'left',
        ]);
    }
}
