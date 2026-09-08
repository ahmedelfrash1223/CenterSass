<?php

namespace App\Livewire\TeacherPanel\Pages\Subject\Parts;

use App\Enums\Teacher\GradeEnum;
use App\Enums\Teacher\SubjectEnum;
use App\Http\Requests\TeacherPanel\UpdateTeacherSubjectRequest;
use App\Models\TeacherSubject;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class SubjectUpdate extends Component
{
    public TeacherSubject $teacherSubject;
    public string $grade = '';
    public string $subject = '';
    public int $is_active = 1;

    public bool $updating = false;

    public function mount(TeacherSubject $subject): void
    {
        abort_unless($subject->teacher_id === Auth::guard('teacher')->id(), 404);

        $this->teacherSubject = $subject;
        $this->grade = $subject->grade;
        $this->subject = $subject->subject;
        $this->is_active = (int) $subject->is_active;
    }

    public function update(): void
    {
        $this->updating = true;

        $request = new UpdateTeacherSubjectRequest();
        $request->merge(['subject_id' => $this->teacherSubject->id]);
        $this->validate($request->rules(), $request->messages());

        $duplicate = TeacherSubject::where('teacher_id', Auth::guard('teacher')->id())
            ->where('grade', $this->grade)
            ->where('subject', $this->subject)
            ->where('id', '!=', $this->teacherSubject->id)
            ->exists();

        if ($duplicate) {
            $this->addError('subject', 'هذه المادة مضافة بالفعل لهذه المرحلة.');
            $this->updating = false;
            return;
        }

        $this->teacherSubject->update([
            'grade' => $this->grade,
            'subject' => $this->subject,
            'is_active' => $this->is_active,
        ]);

        $this->updating = false;
        session()->flash('subject-success', 'تم تحديث المادة بنجاح.');
        $this->redirectRoute('teacher.subjects.index', navigate: true);
    }

    public function render()
    {
        return view('teacher-panel.pages.subject.parts.subject-update', [
            'grades' => GradeEnum::options(),
            'subjectOptions' => SubjectEnum::options(),
        ]);
    }
}