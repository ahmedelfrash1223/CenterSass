<?php

namespace App\Livewire\TeacherPanel\Pages\Subject\Parts;

use App\Enums\Teacher\GradeEnum;
use App\Enums\Teacher\SubjectEnum;
use App\Models\TeacherSubject;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class SubjectData extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public string $search = '';
    public ?int $deleteId = null;

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function confirmDelete(int $id): void
    {
        $this->deleteId = $id;
    }

    public function cancelDelete(): void
    {
        $this->deleteId = null;
    }

    public function delete(): void
    {
        if (! $this->deleteId) {
            return;
        }

        TeacherSubject::where('teacher_id', Auth::guard('teacher')->id())
            ->findOrFail($this->deleteId)
            ->delete();

        $this->deleteId = null;
        session()->flash('subject-success', 'تم حذف المادة بنجاح.');
    }

    public function render()
    {
        $subjects = TeacherSubject::query()
            ->where('teacher_id', Auth::guard('teacher')->id())
            ->when($this->search !== '', function ($query) {
                $query->where(function ($query) {
                    $query->where('grade', 'like', '%' . $this->search . '%')
                        ->orWhere('subject', 'like', '%' . $this->search . '%');
                });
            })
            ->latest()
            ->paginate(10);

        return view('teacher-panel.pages.subject.parts.subject-data', [
            'subjects' => $subjects,
            'grades' => GradeEnum::options(),
            'subjectOptions' => SubjectEnum::options(),
        ]);
    }
}