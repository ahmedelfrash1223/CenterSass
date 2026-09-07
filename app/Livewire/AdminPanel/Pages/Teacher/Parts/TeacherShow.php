<?php

namespace App\Livewire\AdminPanel\Pages\Teacher\Parts;

use App\Models\Teacher;
use Livewire\Component;

class TeacherShow extends Component
{
    public Teacher $teacher;
    public bool $confirmingDelete = false;

    public function mount(Teacher $teacher): void
    {
        $this->teacher = $teacher->load('students');
    }

    public function confirmDelete(): void
    {
        $this->confirmingDelete = true;
    }

    public function cancelDelete(): void
    {
        $this->confirmingDelete = false;
    }

    public function delete(): void
    {
        $this->teacher->delete();
        session()->flash('teacher-success', 'تم حذف المدرس بنجاح.');
        $this->redirectRoute('admin.teacher.index', navigate: true);
    }

    public function render()
    {
        return view('admin-panel.pages.teacher.parts.teacher-show');
    }
}