<?php

namespace App\Livewire\TeacherPanel\Pages\Subject\Parts;

use App\Enums\Teacher\GradeEnum;
use App\Enums\Teacher\SubjectEnum;
use App\Enums\Teacher\TeacherPermissionEnum;
use App\Http\Requests\TeacherPanel\UpdateTeacherSubjectRequest;
use App\Models\TeacherSubject;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class SubjectUpdate extends Component
{
    public TeacherSubject $teacherSubject;
    public array $grades = [];
    public array $subjects = [];
    public int $is_active = 1;

    public function mount(TeacherSubject $subject): void
    {
        $user = Auth::guard('teacher')->user();

        abort_unless(
            $user->hasPermission(TeacherPermissionEnum::SUBJECT_UPDATE),
            403
        );

        abort_unless(
            $subject->teacher_id === $user->getOwnerId(),
            404
        );

        $subject->load('creator');

        $this->teacherSubject = $subject;
        $this->grades = [$subject->grade];
        $this->subjects = [$subject->subject];
        $this->is_active = (int) $subject->is_active;
    }

    public function update(): void
    {
        $user = Auth::guard('teacher')->user();

        abort_unless(
            $user->hasPermission(TeacherPermissionEnum::SUBJECT_UPDATE),
            403
        );

        $request = new UpdateTeacherSubjectRequest();
        $validated = $this->validate($request->rules(), $request->messages());

        $grade = $this->grades[0] ?? $this->teacherSubject->grade;
        $subject = $this->subjects[0] ?? $this->teacherSubject->subject;

        $duplicate = TeacherSubject::where('teacher_id', $user->getOwnerId())
            ->where('grade', $grade)
            ->where('subject', $subject)
            ->where('id', '!=', $this->teacherSubject->id)
            ->exists();

        if ($duplicate) {
            $this->addError('subjects', 'هذه المادة مضافة بالفعل لهذه المرحلة.');
            return;
        }

        $this->teacherSubject->update([
            'grade' => $grade,
            'subject' => $subject,
            'is_active' => (int) $this->is_active,
        ]);

        session()->flash('subject-success', 'تم تحديث المادة بنجاح.');
        $this->redirect(route('teacher.subjects.index'), navigate: true);
    }

    public function render()
    {
        return view('teacher-panel.pages.subject.parts.subject-update', [
            'gradeOptions' => GradeEnum::options(),
            'subjectOptions' => SubjectEnum::options(),
        ]);
    }
}
