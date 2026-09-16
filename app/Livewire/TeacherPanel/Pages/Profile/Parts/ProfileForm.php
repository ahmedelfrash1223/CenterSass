<?php

namespace App\Livewire\TeacherPanel\Pages\Profile\Parts;

use App\Enums\Teacher\TeacherRoleEnum;
use App\Models\Teacher;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;

class ProfileForm extends Component
{
    public ?Teacher $teacher = null;
    public ?string $editing = null;

    public string $name = '';
    public string $username = '';
    public string $email = '';
    public string $phone = '';

    public string $current_password = '';
    public string $new_password = '';
    public string $new_password_confirmation = '';

    protected array $rules = [
        'name' => ['required', 'string', 'min:2', 'max:255'],
        'username' => ['required', 'string', 'min:3', 'max:255'],
        'email' => ['required', 'email', 'max:255'],
        'phone' => ['nullable', 'string', 'max:20'],
    ];

    public function mount(): void
    {
        $this->teacher = Auth::guard('teacher')->user();
        $this->loadTeacherData();
    }

    private function loadTeacherData(): void
    {
        $this->name = $this->teacher->name;
        $this->username = $this->teacher->username;
        $this->email = $this->teacher->email;
        $this->phone = $this->teacher->phone ?? '';
    }

    public function startEditing(string $field): void
    {
        if (!in_array($field, ['name', 'username', 'email', 'phone'], true)) {
            return;
        }

        $this->editing = $field;
        $this->resetValidation();
    }

    public function cancelEditing(): void
    {
        $this->loadTeacherData();
        $this->editing = null;
        $this->resetValidation();
    }

    public function save(): void
    {
        // Validate with unique rules ignoring current teacher
        $this->validate([
            'name' => ['required', 'string', 'min:2', 'max:255'],
            'username' => ['required', 'string', 'min:3', 'max:255', 'unique:teachers,username,' . $this->teacher->id],
            'email' => ['required', 'email', 'max:255', 'unique:teachers,email,' . $this->teacher->id],
            'phone' => ['nullable', 'string', 'max:20', 'unique:teachers,phone,' . $this->teacher->id],
        ], [
            'name.required' => 'الاسم مطلوب.',
            'name.min' => 'الاسم يجب أن يكون حرفين على الأقل.',
            'name.max' => 'الاسم يجب ألا يتجاوز 255 حرفًا.',
            'username.required' => 'اسم المستخدم مطلوب.',
            'username.min' => 'اسم المستخدم يجب أن يكون 3 أحرف على الأقل.',
            'username.max' => 'اسم المستخدم يجب ألا يتجاوز 255 حرفًا.',
            'username.unique' => 'اسم المستخدم مستخدم بالفعل.',
            'email.required' => 'البريد الإلكتروني مطلوب.',
            'email.email' => 'البريد الإلكتروني غير صالح.',
            'email.unique' => 'البريد الإلكتروني مستخدم بالفعل.',
            'phone.unique' => 'رقم الهاتف مستخدم بالفعل.',
        ]);

        $this->teacher->update([
            'name' => $this->name,
            'username' => $this->username,
            'email' => $this->email,
            'phone' => $this->phone ?: null,
        ]);

        $this->teacher->refresh();
        $this->loadTeacherData();
        $this->editing = null;

        $this->dispatch('teacher-profile-updated');

        session()->flash('profile-success', 'تم تحديث بيانات الحساب بنجاح.');
    }

    public function changePassword(): void
    {
        $this->validate([
            'current_password' => ['required', 'current_password:teacher'],
            'new_password' => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'current_password.required' => 'يرجى إدخال كلمة المرور الحالية.',
            'current_password.current_password' => 'كلمة المرور الحالية غير صحيحة.',
            'new_password.required' => 'يرجى إدخال كلمة المرور الجديدة.',
            'new_password.min' => 'يجب أن تكون كلمة المرور الجديدة 8 أحرف على الأقل.',
            'new_password.confirmed' => 'تأكيد كلمة المرور غير متطابق.',
        ]);

        $this->teacher->update([
            'password' => Hash::make($this->new_password),
        ]);

        $this->reset([
            'current_password',
            'new_password',
            'new_password_confirmation',
        ]);

        $this->resetValidation();

        $this->dispatch('teacher-password-updated');

        session()->flash('password-success', 'تم تغيير كلمة المرور بنجاح.');
    }

    public function clearPasswordFields(): void
    {
        $this->reset([
            'current_password',
            'new_password',
            'new_password_confirmation',
        ]);

        $this->resetValidation();
    }

    public function render()
    {
        return view('teacher-panel.pages.profile.parts.profile-form');
    }
}
