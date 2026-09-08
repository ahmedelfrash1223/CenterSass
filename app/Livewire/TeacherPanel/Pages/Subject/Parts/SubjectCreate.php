<?php

namespace App\Livewire\TeacherPanel\Pages\Subject\Parts;

use App\Enums\Teacher\GradeEnum;
use App\Enums\Teacher\SubjectEnum;
use App\Http\Requests\TeacherPanel\StoreTeacherSubjectRequest;
use App\Models\TeacherSubject;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class SubjectCreate extends Component
{
    public string $grade = '';
    public string $subject = '';
    public int $is_active = 1;

    public bool $saving = false;
    public bool $savingAndNew = false;

    public function save(bool $createNew = false): void
    {
        $createNew ? $this->savingAndNew = true : $this->saving = true;

        $request = new StoreTeacherSubjectRequest();
        $this->validate($request->rules(), $request->messages());

        $duplicate = TeacherSubject::where('teacher_id', Auth::guard('teacher')->id())
            ->where('grade', $this->grade)
            ->where('subject', $this->subject)
            ->exists();

        if ($duplicate) {
            $this->addError('subject', 'هذه المادة مضافة بالفعل لهذه المرحلة.');
            $this->saving = false;
            $this->savingAndNew = false;
            return;
        }

        TeacherSubject::create([
            'teacher_id' => Auth::guard('teacher')->id(),
            'grade' => $this->grade,
            'subject' => $this->subject,
            'is_active' => $this->is_active,
        ]);

        $this->saving = false;
        $this->savingAndNew = false;

        if ($createNew) {
            $this->reset(['grade', 'subject', 'is_active']);
            session()->flash('subject-success', 'تمت إضافة المادة بنجاح. يمكنك إضافة مادة أخرى.');
            return;
        }

        session()->flash('subject-success', 'تمت إضافة المادة بنجاح.');
        $this->redirectRoute('teacher.subjects.index', navigate: true);
    }

    public function render()
    {
        return view('teacher-panel.pages.subject.parts.subject-create', [
            'grades' => GradeEnum::options(),
            'subjectOptions' => SubjectEnum::options(),
        ]);
    }
}