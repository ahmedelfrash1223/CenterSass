<?php

namespace App\Livewire\AdminPanel\Pages\Profile\Parts;

use App\Models\Admin;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;

class ProfileForm extends Component
{
    public ?Admin $admin = null;
    public ?string $editing = null;

    public string $name = '';
    public string $username = '';
    public string $email = '';

    public string $current_password = '';
    public string $new_password = '';
    public string $new_password_confirmation = '';

    protected array $rules = [
        'name' => ['required', 'string', 'min:2', 'max:255'],
        'username' => ['required', 'string', 'min:3', 'max:255'],
        'email' => ['required', 'email', 'max:255'],
    ];

    public function mount(): void
    {
        $this->admin = Auth::guard('admin')->user();
        $this->loadAdminData();
    }

    private function loadAdminData(): void
    {
        $this->name = $this->admin->name;
        $this->username = $this->admin->username;
        $this->email = $this->admin->email;
    }

    public function startEditing(string $field): void
    {
        if (!in_array($field, ['name', 'username', 'email'], true)) {
            return;
        }

        $this->editing = $field;
        $this->resetValidation();
    }

    public function cancelEditing(): void
    {
        $this->loadAdminData();
        $this->editing = null;
        $this->resetValidation();
    }

    public function save(): void
    {
        $this->validate();

        $this->admin->update([
            'name' => $this->name,
            'username' => $this->username,
            'email' => $this->email,
        ]);

        $this->admin->refresh();
        $this->loadAdminData();
        $this->editing = null;

        $this->dispatch('admin-profile-updated');

        session()->flash('profile-success', 'تم تحديث بيانات الحساب بنجاح.');
    }

    public function changePassword(): void
    {
        $this->validate([
            'current_password' => ['required', 'current_password:admin'],
            'new_password' => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'current_password.required' => 'يرجى إدخال كلمة المرور الحالية.',
            'current_password.current_password' => 'كلمة المرور الحالية غير صحيحة.',
            'new_password.required' => 'يرجى إدخال كلمة المرور الجديدة.',
            'new_password.min' => 'يجب أن تكون كلمة المرور الجديدة 8 أحرف على الأقل.',
            'new_password.confirmed' => 'تأكيد كلمة المرور غير متطابق.',
        ]);

        $this->admin->update([
            'password' => Hash::make($this->new_password),
        ]);

        $this->reset([
            'current_password',
            'new_password',
            'new_password_confirmation',
        ]);

        $this->resetValidation();

        $this->dispatch('admin-password-updated');

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
        return view('admin-panel.pages.profile.parts.profile-form');
    }
}
