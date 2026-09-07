<?php

namespace App\Livewire\AdminPanel\Pages\Admin\Parts;

use App\Http\Requests\AdminPanel\UpdateAdminRequest;
use App\Models\Admin;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;

class AdminUpdate extends Component
{
    public Admin $admin;
    public string $name = '';
    public string $username = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';
    public int $is_active = 1;

    public bool $updating = false;

    public function mount(Admin $admin): void
    {
        $this->admin = $admin;
        $this->name = $admin->name;
        $this->username = $admin->username;
        $this->email = $admin->email;
        $this->is_active = $admin->is_active;
    }

    public function update(): void
    {
        $this->updating = true;

        $request = new UpdateAdminRequest();
        $request->merge(['admin_id' => $this->admin->id]);
        $this->validate($request->rules(), $request->messages());

        $data = [
            'name' => $this->name,
            'username' => $this->username,
            'email' => $this->email,
            'is_active' => $this->is_active,
        ];

        if (! empty($this->password)) {
            $data['password'] = Hash::make($this->password);
        }

        $this->admin->update($data);
        $this->updating = false;

        session()->flash('admin-success', 'تم تحديث بيانات المدير بنجاح.');
        $this->redirectRoute('admin.admins.index', navigate: true);
    }

    public function render()
    {
        return view('admin-panel.pages.admin.parts.admin-update');
    }
}