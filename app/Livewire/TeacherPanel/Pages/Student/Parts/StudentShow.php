<?php

namespace App\Livewire\TeacherPanel\Pages\Student\Parts;

use App\Enums\Teacher\GradeEnum;
use App\Enums\Teacher\TeacherPermissionEnum;
use App\Models\Student;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class StudentShow extends Component
{
    public Student $student;

    public function mount(Student $student): void
    {
        $user = Auth::guard('teacher')->user();

        // Student must belong to owner account
        abort_unless($student->teacher_id === $user->getOwnerId(), 404);

        // Non-owner: student must be in accessible branches
        if (!$user->isOwner()) {
            abort_unless(
                in_array($student->branch_id, $user->getAccessibleBranchIds()),
                404
            );
        }

        $this->student = $student->load([
            'creator',
            'branch',
            'groups' => fn($q) => $q->wherePivot('status', 'active'),
        ]);
    }

    public function render()
    {
        $user = Auth::guard('teacher')->user();

        // Resolve grade label once for the view
        $gradeLabel = $this->student->grade
            ? GradeEnum::tryFrom($this->student->grade)?->label() ?? $this->student->grade
            : null;

        return view('teacher-panel.pages.student.parts.student-show', [
            'canUpdate' => $user->hasPermission(TeacherPermissionEnum::STUDENT_UPDATE),
            'canDelete' => $user->hasPermission(TeacherPermissionEnum::STUDENT_DELETE),
            'ownerId' => $user->getOwnerId(),
            'gradeLabel' => $gradeLabel,
        ]);
    }
}
