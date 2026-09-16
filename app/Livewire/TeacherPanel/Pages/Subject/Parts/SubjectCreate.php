<?php

namespace App\Livewire\TeacherPanel\Pages\Subject\Parts;

use App\Enums\Teacher\GradeEnum;
use App\Enums\Teacher\SubjectEnum;
use App\Enums\Teacher\TeacherPermissionEnum;
use App\Http\Requests\TeacherPanel\StoreTeacherSubjectRequest;
use App\Models\TeacherSubject;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class SubjectCreate extends Component
{
    public array $grades = [];
    public array $subjects = [];
    public int $is_active = 1;

    public function mount(): void
    {
        abort_unless(
            Auth::guard('teacher')->user()->hasPermission(TeacherPermissionEnum::SUBJECT_CREATE),
            403
        );
    }

    public function save(bool $createNew = false): void
    {
        $request = new StoreTeacherSubjectRequest();
        $validated = $this->validate($request->rules(), $request->messages());

        $currentUser = Auth::guard('teacher')->user();
        $ownerId = $currentUser->getOwnerId();
        $createdCount = 0;
        $duplicates = [];

        foreach ($this->grades as $grade) {
            foreach ($this->subjects as $subject) {
                $exists = TeacherSubject::where('teacher_id', $ownerId)
                    ->where('grade', $grade)
                    ->where('subject', $subject)
                    ->exists();

                if ($exists) {
                    $duplicates[] = $grade . ' - ' . $subject;
                    continue;
                }

                TeacherSubject::create([
                    'teacher_id' => $ownerId,
                    'created_by' => $currentUser->id,
                    'grade' => $grade,
                    'subject' => $subject,
                    'is_active' => (int) $this->is_active,
                ]);

                $createdCount++;
            }
        }

        if ($createdCount === 0) {
            $this->dispatch('show-error-message', message: 'جميع المواد المختارة موجودة بالفعل.');
            return;
        }

        if (!empty($duplicates)) {
            $this->dispatch('show-warning-message', message: 'تم تجاهل ' . count($duplicates) . ' إضافة مكررة.');
        }

        if ($createNew) {
            $this->reset(['grades', 'subjects']);
            $this->is_active = 1;

            $this->dispatch('show-success-message', message: "تمت إضافة {$createdCount} مادة بنجاح.");
            return;
        }

        session()->flash('subject-success', "تمت إضافة {$createdCount} مادة بنجاح.");
        $this->redirect(route('teacher.subjects.index'), navigate: true);
    }

    public function render()
    {
        return view('teacher-panel.pages.subject.parts.subject-create', [
            'gradeOptions' => GradeEnum::options(),
            'subjectOptions' => SubjectEnum::options(),
        ]);
    }
}
