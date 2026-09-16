<?php

namespace App\Livewire\AdminPanel\Pages\Teacher\Parts;

use App\Enums\Teacher\TeacherPermissionEnum;
use App\Models\Teacher;
use Livewire\Component;

class TeacherShow extends Component
{
    public Teacher $teacher;
    public bool $confirmingDelete = false;

    public function mount(Teacher $teacher): void
    {
        $this->teacher = $teacher->load(['students', 'owner', 'staff', 'subscription', 'permissions']);
    }

    public function confirmDelete(): void
    {
        if ($this->teacher->isOwner() && $this->teacher->staff()->count() > 0) {
            session()->flash('teacher-error', 'لا يمكن حذف المدرس المسئول لأنه يمتلك مدرسين أو مساعدين تابعين له. يرجى حذفهم أولاً.');
            return;
        }

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
        return view('admin-panel.pages.teacher.parts.teacher-show', [
            'groupedPermissions' => TeacherPermissionEnum::grouped(),
            'userPermissions' => $this->teacher->getAllPermissions(),
        ]);
    }
}
