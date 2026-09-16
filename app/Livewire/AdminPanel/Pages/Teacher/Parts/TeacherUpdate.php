<?php

namespace App\Livewire\AdminPanel\Pages\Teacher\Parts;

use App\Enums\Teacher\TeacherPermissionEnum;
use App\Enums\Teacher\TeacherRoleEnum;
use App\Models\Teacher;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;

class TeacherUpdate extends Component
{
    public Teacher $teacher;
    public string $name = '';
    public string $username = '';
    public string $email = '';
    public string $phone = '';
    public string $password = '';
    public string $password_confirmation = '';
    public int $is_active = 1;
    public int $role = 1;
    public ?int $owner_id = null;

    public array $selectedPermissions = [];

    public function mount(Teacher $teacher): void
    {
        $this->teacher = $teacher;
        $this->name = $teacher->name;
        $this->username = $teacher->username;
        $this->email = $teacher->email;
        $this->phone = $teacher->phone ?? '';
        $this->is_active = (int) $teacher->is_active;
        $this->role = (int) $teacher->role->value;
        $this->owner_id = $teacher->owner_id;
        $this->selectedPermissions = $teacher->getAllPermissions();
    }

    public function updatedRole(): void
    {
        if ((int) $this->role === TeacherRoleEnum::OWNER->value) {
            $this->owner_id = null;
        }

        $this->selectedPermissions = TeacherPermissionEnum::forRole(
            TeacherRoleEnum::from($this->role)
        );
    }

    public function toggleGroup(string $groupName): void
    {
        $groupPermissions = collect(TeacherPermissionEnum::grouped()[$groupName] ?? [])
            ->pluck('value')
            ->toArray();

        $allSelected = !empty($groupPermissions) && empty(array_diff($groupPermissions, $this->selectedPermissions));

        if ($allSelected) {
            $this->selectedPermissions = array_values(
                array_diff($this->selectedPermissions, $groupPermissions)
            );
        } else {
            $this->selectedPermissions = array_values(
                array_unique(array_merge($this->selectedPermissions, $groupPermissions))
            );
        }
    }

    public function selectAll(): void
    {
        $this->selectedPermissions = TeacherPermissionEnum::values();
    }

    public function deselectAll(): void
    {
        $this->selectedPermissions = [];
    }

    public function update(): void
    {
        $isOwner = (int) $this->role === TeacherRoleEnum::OWNER->value;

        $this->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:teachers,username,' . $this->teacher->id,
            'email' => 'required|email|max:255|unique:teachers,email,' . $this->teacher->id,
            'phone' => 'nullable|string|max:20|unique:teachers,phone,' . $this->teacher->id,
            'password' => 'nullable|string|min:8|confirmed',
            'role' => 'required|integer|in:1,2,3',
            'owner_id' => $isOwner ? 'nullable' : 'required|integer|exists:teachers,id',
            'is_active' => 'required|integer|in:0,1',
        ], [
            'name.required' => 'اسم المدرس مطلوب.',
            'username.required' => 'اسم المستخدم مطلوب.',
            'username.unique' => 'اسم المستخدم مستخدم بالفعل.',
            'email.required' => 'البريد الإلكتروني مطلوب.',
            'email.unique' => 'البريد الإلكتروني مستخدم بالفعل.',
            'phone.unique' => 'رقم الهاتف مستخدم بالفعل.',
            'password.min' => 'كلمة المرور يجب أن تكون 8 أحرف على الأقل.',
            'password.confirmed' => 'تأكيد كلمة المرور غير متطابق.',
            'role.required' => 'نوع الحساب مطلوب.',
            'owner_id.required' => 'يجب اختيار المسئول التابع له الحساب.',
            'owner_id.exists' => 'المسئول المحدد غير موجود.',
        ]);

        $data = [
            'name' => $this->name,
            'username' => $this->username,
            'email' => $this->email,
            'phone' => $this->phone ?: null,
            'role' => (int) $this->role,
            'owner_id' => $isOwner ? null : (int) $this->owner_id,
            'is_active' => (int) $this->is_active,
        ];

        if (!empty($this->password)) {
            $data['password'] = Hash::make($this->password);
        }

        $this->teacher->update($data);

        if (!$this->teacher->isOwner()) {
            $this->teacher->syncPermissions($this->selectedPermissions);
        }

        session()->flash('teacher-success', 'تم تحديث بيانات المدرس والصلاحيات بنجاح.');
        $this->redirect(route('admin.teacher.index'), navigate: true);
    }

    public function render()
    {
        return view('admin-panel.pages.teacher.parts.teacher-update', [
            'roles' => TeacherRoleEnum::options(),
            'owners' => Teacher::where('role', TeacherRoleEnum::OWNER->value)
                ->where('id', '!=', $this->teacher->id)
                ->orderBy('name')
                ->get(),
            'groupedPermissions' => TeacherPermissionEnum::grouped(),
        ]);
    }
}
