<?php

namespace App\Livewire\TeacherPanel\Pages\Group\Parts;

use App\Models\Group;
use App\Models\Student;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class GroupShow extends Component
{
    public Group $group;
    public string $searchStudent = '';
    public ?int $selectedStudentId = null;
    public bool $showAddStudent = false;

    public function mount(Group $group): void
    {
        abort_unless($group->teacher_id === auth('teacher')->id(), 404);
        $this->group = $group->load(['branch', 'schedules.subject', 'students' => function ($query) {
            $query->wherePivot('status', 'active');
        }]);
    }

    public function toggleAddStudent(): void
    {
        $this->showAddStudent = !$this->showAddStudent;
        $this->searchStudent = '';
        $this->selectedStudentId = null;
    }

    public function addStudent(): void
    {
        if (!$this->selectedStudentId) {
            session()->flash('error', 'يرجى اختيار طالب.');
            return;
        }

        $student = Student::where('teacher_id', Auth::guard('teacher')->id())
            ->findOrFail($this->selectedStudentId);

        if ($this->group->addStudent($student)) {
            $this->showAddStudent = false;
            $this->searchStudent = '';
            $this->selectedStudentId = null;
            $this->group->refresh();
            $this->group->load(['students' => function ($query) {
                $query->wherePivot('status', 'active');
            }]);
            session()->flash('success', 'تم إضافة الطالب للمجموعة بنجاح.');
        } else {
            session()->flash('error', 'لا يمكن إضافة الطالب. المجموعة ممتلئة أو الطالب موجود بالفعل.');
        }
    }

    public function removeStudent($studentId): void
    {
        $student = Student::findOrFail($studentId);
        $this->group->removeStudent($student);

        $this->group->refresh();
        $this->group->load(['students' => function ($query) {
            $query->wherePivot('status', 'active');
        }]);

        session()->flash('success', 'تم إزالة الطالب من المجموعة.');
    }

    public function render()
    {
        $availableStudents = Student::where('teacher_id', Auth::guard('teacher')->id())
            ->when($this->searchStudent !== '', function ($query) {
                $query->where('name', 'like', '%' . $this->searchStudent . '%')
                    ->orWhere('email', 'like', '%' . $this->searchStudent . '%');
            })
            ->whereDoesntHave('groups', function ($query) {
                $query->where('group_id', $this->group->id)
                    ->where('group_student.status', 'active');
            })
            ->limit(10)
            ->get();

        return view('teacher-panel.pages.group.parts.group-show', [
            'availableStudents' => $availableStudents,
            'activeStudentsCount' => $this->group->students->count(),
        ]);
    }
}
