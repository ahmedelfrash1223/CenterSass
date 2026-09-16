<?php

namespace App\Livewire\TeacherPanel\Pages\Staff\Parts;

use App\Enums\Teacher\TeacherPermissionEnum;
use App\Http\Requests\TeacherPanel\UpdateStaffRequest;
use App\Livewire\TeacherPanel\Concerns\HasStaffBranchSync;
use App\Livewire\TeacherPanel\Concerns\LoadsStaffFormData;
use App\Models\Teacher;
use App\Traits\Teacher\HasPermissionSelection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;

class StaffUpdate extends Component
{
    use HasPermissionSelection;
    use HasStaffBranchSync;
    use LoadsStaffFormData;

    public Teacher $teacher;
    public string $name = '';
    public string $username = '';
    public string $email = '';
    public string $phone = '';
    public string $password = '';
    public string $password_confirmation = '';
    public int $is_active = 1;
    public int $role = 2;

    public array $branch_ids = [];

    public function mount(Teacher $teacher): void
    {
        $user = Auth::guard('teacher')->user();

        abort_unless(
            $user->hasPermission(TeacherPermissionEnum::STAFF_UPDATE),
            403
        );

        abort_unless(
            $user->hasAccessToTeacher($teacher),
            404
        );

        $teacher->load('creator');

        $this->teacher = $teacher;
        $this->name = $teacher->name;
        $this->username = $teacher->username;
        $this->email = $teacher->email;
        $this->phone = $teacher->phone ?? '';
        $this->is_active = (int) $teacher->is_active;
        $this->role = (int) $teacher->role->value;

        // Load existing branch ids as strings for checkbox binding
        $this->branch_ids = $teacher->branches()
            ->pluck('branches.id')
            ->map(fn($id) => (string) $id)
            ->toArray();

        $this->groupedPermissions = TeacherPermissionEnum::grouped();
        $this->initializePermissions();

        // Pre-select current permissions
        foreach ($teacher->getAllPermissions() as $permission) {
            if (isset($this->permissions[$permission])) {
                $this->permissions[$permission] = true;
            }
        }
    }

    public function updatedRole(): void
    {
        // Reset permissions when role changes
        $this->initializePermissions();
    }

    public function update(): void
    {
        $user = Auth::guard('teacher')->user();

        abort_unless(
            $user->hasPermission(TeacherPermissionEnum::STAFF_UPDATE),
            403
        );

        abort_unless(
            $user->hasAccessToTeacher($this->teacher),
            404
        );

        $request = new UpdateStaffRequest();
        $request->merge(['teacher_id' => $this->teacher->id]);
        $this->validate($request->rules(), $request->messages());

        // Validate selected branches against scope
        if (!$this->ensureBranchesAuthorized($this->branch_ids)) {
            return;
        }

        $data = [
            'name' => $this->name,
            'username' => $this->username,
            'email' => $this->email,
            'phone' => $this->phone ?: null,
            'role' => (int) $this->role,
            'is_active' => (int) $this->is_active,
        ];

        if (!empty($this->password)) {
            $data['password'] = Hash::make($this->password);
        }

        $this->teacher->update($data);

        if (!$this->teacher->isOwner()) {
            $this->teacher->syncPermissions($this->getSelectedPermissions());
            $this->teacher->syncBranches($this->branch_ids, $user->id);
        }

        session()->flash('staff-success', 'تم تحديث بيانات العضو والصلاحيات بنجاح.');
        $this->redirect(route('teacher.staff.index'), navigate: true);
    }

    public function render()
    {
        return view('teacher-panel.pages.staff.parts.staff-update',
            $this->loadStaffFormData(includeLimit: false)
        );
    }
}
