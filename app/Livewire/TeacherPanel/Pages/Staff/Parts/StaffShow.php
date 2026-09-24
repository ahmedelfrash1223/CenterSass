<?php

namespace App\Livewire\TeacherPanel\Pages\Staff\Parts;

use App\Enums\Teacher\TeacherPermissionEnum;
use App\Livewire\TeacherPanel\Concerns\Shared\HasStaffScheduleView;
use App\Models\Teacher;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class StaffShow extends Component
{
    use HasStaffScheduleView;

    public Teacher $teacher;
    public bool $confirmingDelete = false;

    public array $groupedPermissions = [];
    public array $userPermissions = [];

    public function mount(Teacher $teacher): void
    {
        $user = Auth::guard('teacher')->user();

        abort_unless(
            $user->hasAccessToTeacher($teacher),
            404
        );

        $teacher->load(['students', 'branches', 'subjects', 'groups', 'creator']);

        $this->teacher = $teacher;
        $this->userPermissions = $teacher->getAllPermissions();
        $this->groupedPermissions = TeacherPermissionEnum::grouped();
    }

    public function confirmDelete(): void
    {
        $user = Auth::guard('teacher')->user();

        if (!$user->hasPermission(TeacherPermissionEnum::STAFF_DELETE)) {
            session()->flash('staff-error', 'ليس لديك صلاحية لحذف أعضاء الفريق.');
            return;
        }

        if ($this->teacher->id === $user->id) {
            session()->flash('staff-error', 'لا يمكن حذف حسابك الشخصي.');
            return;
        }

        if (!$user->hasAccessToTeacher($this->teacher)) {
            session()->flash('staff-error', 'ليس لديك صلاحية على هذا العضو.');
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
        $user = Auth::guard('teacher')->user();

        if (!$user->hasPermission(TeacherPermissionEnum::STAFF_DELETE)) {
            session()->flash('staff-error', 'ليس لديك صلاحية لحذف أعضاء الفريق.');
            return;
        }

        if (!$user->hasAccessToTeacher($this->teacher)) {
            session()->flash('staff-error', 'ليس لديك صلاحية على هذا العضو.');
            return;
        }

        if ($this->teacher->id === $user->id) {
            session()->flash('staff-error', 'لا يمكن حذف حسابك الشخصي.');
            return;
        }

        $this->teacher->delete();
        session()->flash('staff-success', 'تم حذف عضو الفريق بنجاح.');
        $this->redirect(route('teacher.staff.index'), navigate: true);
    }

    public function render()
    {
        $user = Auth::guard('teacher')->user();
        $ownerId = $user->getOwnerId();

        // Owner can modify any staff except themselves; staff can modify peers in shared branches
        $canModify = ($user->isOwner() && $this->teacher->id !== $ownerId)
            || (!$user->isOwner()
                && $user->hasAccessToTeacher($this->teacher)
                && $this->teacher->id !== $user->id);

        return view('teacher-panel.pages.staff.parts.staff-show', [
            'canUpdate' => $user->hasPermission(TeacherPermissionEnum::STAFF_UPDATE) && $canModify,
            'canDelete' => $user->hasPermission(TeacherPermissionEnum::STAFF_DELETE) && $canModify,
            'ownerId' => $ownerId,
            'schedules' => $this->loadStaffSchedules($this->teacher),
        ]);
    }
}
