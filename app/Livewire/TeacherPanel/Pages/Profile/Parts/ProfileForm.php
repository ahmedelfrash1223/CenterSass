<?php

namespace App\Livewire\TeacherPanel\Pages\Profile\Parts;

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
        'phone' => ['required', 'string', 'max:255'],
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
        $this->phone = $this->teacher->phone;
    }

    public function startEditing(string $field): void
    {
        if (!in_array($field, ['name', 'username', 'email','phone'], true)) {
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
        $this->validate();

        $this->teacher->update([
            'name' => $this->name,
            'username' => $this->username,
            'email' => $this->email,
            'phone' => $this->phone,
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
