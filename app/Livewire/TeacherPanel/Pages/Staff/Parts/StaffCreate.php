<?php

namespace App\Livewire\TeacherPanel\Pages\Staff\Parts;

use App\Enums\Admin\PackageFeatureKeyEnum;
use App\Enums\Teacher\TeacherPermissionEnum;
use App\Enums\Teacher\TeacherRoleEnum;
use App\Http\Requests\TeacherPanel\StoreStaffRequest;
use App\Livewire\TeacherPanel\Concerns\HasStaffBranchSync;
use App\Livewire\TeacherPanel\Concerns\HasSubscriptionLimitCheck;
use App\Livewire\TeacherPanel\Concerns\LoadsStaffFormData;
use App\Models\Teacher;
use App\Traits\Teacher\HasPermissionSelection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;

class StaffCreate extends Component
{
    use HasPermissionSelection;
    use HasSubscriptionLimitCheck;
    use HasStaffBranchSync;
    use LoadsStaffFormData;

    private const DEFAULT_PASSWORD = '12345678';

    public string $name = '';
    public string $username = '';
    public string $email = '';
    public string $phone = '';
    public string $password = '';
    public string $password_confirmation = '';
    public int $is_active = 1;
    public int $role = 2;

    public array $branch_ids = [];

    public function mount(): void
    {
        $user = Auth::guard('teacher')->user();

        abort_unless(
            $user->hasPermission(TeacherPermissionEnum::STAFF_CREATE),
            403
        );

        // Redirect if plan limit is already reached
        $count = Teacher::where('owner_id', $user->getOwnerId())->count();

        if (!$this->checkLimitOrRedirect(
            PackageFeatureKeyEnum::MAX_EMPLOYEES->value,
            $count,
            'staff-error',
            'teacher.staff.index'
        )) {
            return;
        }

        $this->groupedPermissions = TeacherPermissionEnum::grouped();
        $this->initializePermissions();
    }

    public function updatedRole(): void
    {
        // Reset permissions when role changes
        $this->initializePermissions();
    }

    public function save(bool $createNew = false): void
    {
        $user = Auth::guard('teacher')->user();
        $ownerId = $user->getOwnerId();

        // Re-check plan limit on submit
        $count = Teacher::where('owner_id', $ownerId)->count();

        if (!$this->checkLimitOrFail(PackageFeatureKeyEnum::MAX_EMPLOYEES->value, $count)) {
            return;
        }

        // Apply default password if empty
        if (empty($this->password)) {
            $this->password = self::DEFAULT_PASSWORD;
            $this->password_confirmation = self::DEFAULT_PASSWORD;
        }

        $request = new StoreStaffRequest();
        $this->validate($request->rules(), $request->messages());

        // Validate selected branches against scope
        if (!$this->ensureBranchesAuthorized($this->branch_ids)) {
            return;
        }

        $teacher = Teacher::create([
            'name' => $this->name,
            'username' => $this->username,
            'email' => $this->email,
            'phone' => $this->phone ?: null,
            'password' => Hash::make($this->password),
            'role' => (int) $this->role,
            'owner_id' => $ownerId,
            'created_by' => $user->id,
            'is_active' => (int) $this->is_active,
        ]);

        if (!$teacher->isOwner()) {
            $teacher->syncPermissions($this->getSelectedPermissions());
            $this->syncStaffBranches($teacher, $this->branch_ids);
        }

        if ($createNew) {
            $this->reset(['name', 'username', 'email', 'phone', 'password', 'password_confirmation', 'branch_ids']);
            $this->is_active = 1;
            $this->role = TeacherRoleEnum::STAFF->value;
            $this->initializePermissions();

            // If the new count hits the plan limit, bounce to index
            $newCount = Teacher::where('owner_id', $ownerId)->count();

            if (!$this->checkLimitOrRedirect(
                PackageFeatureKeyEnum::MAX_EMPLOYEES->value,
                $newCount,
                'staff-error',
                'teacher.staff.index'
            )) {
                session()->flash('staff-success', 'تم إضافة عضو الفريق بنجاح.');
                return;
            }

            $this->dispatch('show-success-message', message: 'تم إضافة عضو الفريق بنجاح. يمكنك إضافة عضو آخر.');
            return;
        }

        session()->flash('staff-success', 'تم إضافة عضو الفريق بنجاح.');
        $this->redirect(route('teacher.staff.index'), navigate: true);
    }

    public function render()
    {
        return view('teacher-panel.pages.staff.parts.staff-create',
            $this->loadStaffFormData()
        );
    }
}
