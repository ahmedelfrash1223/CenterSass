<?php

namespace App\Livewire\AdminPanel\Pages\Teacher\Parts;

use App\Enums\Teacher\TeacherPermissionEnum;
use App\Enums\Teacher\TeacherRoleEnum;
use App\Models\Teacher;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;

class TeacherCreate extends Component
{
    private const DEFAULT_PASSWORD = '12345678';

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

    public function mount(): void
    {
        $this->loadDefaultPermissions();
    }

    public function loadDefaultPermissions(): void
    {
        $this->selectedPermissions = TeacherPermissionEnum::forRole(
            TeacherRoleEnum::from($this->role)
        );
    }

    public function updatedRole(): void
    {
        if ((int) $this->role === TeacherRoleEnum::OWNER->value) {
            $this->owner_id = null;
        }

        $this->loadDefaultPermissions();
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

    public function save(bool $createNew = false): void
    {
        if (empty($this->password)) {
            $this->password = self::DEFAULT_PASSWORD;
            $this->password_confirmation = self::DEFAULT_PASSWORD;
        }

        $isOwner = (int) $this->role === TeacherRoleEnum::OWNER->value;

        $this->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:teachers,username',
            'email' => 'required|email|max:255|unique:teachers,email',
            'phone' => 'nullable|string|max:20|unique:teachers,phone',
            'password' => 'nullable|string|min:8|confirmed',
            'role' => 'required|integer|in:1,2,3',
            'owner_id' => $isOwner ? 'nullable' : 'required|integer|exists:teachers,id',
            'is_active' => 'required|integer|in:0,1',
        ], [
            'name.required' => 'اسم المدرس مطلوب.',
            'name.string' => 'اسم المدرس يجب أن يكون نصًا.',
            'name.max' => 'اسم المدرس يجب ألا يتجاوز 255 حرفًا.',
            'username.required' => 'اسم المستخدم مطلوب.',
            'username.unique' => 'اسم المستخدم مستخدم بالفعل.',
            'email.required' => 'البريد الإلكتروني مطلوب.',
            'email.email' => 'البريد الإلكتروني غير صالح.',
            'email.unique' => 'البريد الإلكتروني مستخدم بالفعل.',
            'phone.unique' => 'رقم الهاتف مستخدم بالفعل.',
            'password.min' => 'كلمة المرور يجب أن تكون 8 أحرف على الأقل.',
            'password.confirmed' => 'تأكيد كلمة المرور غير متطابق.',
            'role.required' => 'نوع الحساب مطلوب.',
            'role.in' => 'نوع الحساب غير صالح.',
            'owner_id.required' => 'يجب اختيار المسئول التابع له الحساب.',
            'owner_id.exists' => 'المسئول المحدد غير موجود.',
            'is_active.required' => 'الحالة مطلوبة.',
            'is_active.in' => 'الحالة غير صالحة.',
        ]);

        $teacher = Teacher::create([
            'name' => $this->name,
            'username' => $this->username,
            'email' => $this->email,
            'phone' => $this->phone ?: null,
            'password' => Hash::make($this->password),
            'role' => (int) $this->role,
            'owner_id' => $isOwner ? null : (int) $this->owner_id,
            'is_active' => (int) $this->is_active,
        ]);

        if ($teacher && !$teacher->isOwner()) {
            $teacher->syncPermissions($this->selectedPermissions);
        }

        if ($createNew) {
            $this->reset(['name', 'username', 'email', 'phone', 'password', 'password_confirmation', 'owner_id']);
            $this->is_active = 1;
            $this->role = TeacherRoleEnum::OWNER->value;
            $this->loadDefaultPermissions();
            session()->flash('teacher-success', 'تم إنشاء الحساب بنجاح. يمكنك إضافة حساب آخر.');
            return;
        }

        session()->flash('teacher-success', 'تم إنشاء الحساب بنجاح.');
        $this->redirect(route('admin.teacher.index'), navigate: true);
    }

    public function render()
    {
        return view('admin-panel.pages.teacher.parts.teacher-create', [
            'roles' => TeacherRoleEnum::options(),
            'owners' => Teacher::where('role', TeacherRoleEnum::OWNER->value)
                ->orderBy('name')
                ->get(),
            'groupedPermissions' => TeacherPermissionEnum::grouped(),
        ]);
    }
}
