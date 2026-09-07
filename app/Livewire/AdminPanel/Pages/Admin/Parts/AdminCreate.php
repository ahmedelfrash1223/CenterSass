<?php

namespace App\Livewire\AdminPanel\Pages\Admin\Parts;

use App\Http\Requests\AdminPanel\StoreAdminRequest;
use App\Models\Admin;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;

class AdminCreate extends Component
{
    // Default password constant
    private const DEFAULT_PASSWORD = '12345678';

    public string $name = '';
    public string $username = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';
    public int $is_active = 1;

    public bool $saving = false;
    public bool $savingAndNew = false;

    public function save(bool $createNew = false): void
    {
        if ($createNew) {
            $this->savingAndNew = true;
        } else {
            $this->saving = true;
        }

        // Set default password if empty
        if (empty($this->password)) {
            $this->password = self::DEFAULT_PASSWORD;
            $this->password_confirmation = self::DEFAULT_PASSWORD;
        }

        $request = new StoreAdminRequest();
        $this->validate($request->rules(), $request->messages());

        Admin::create([
            'name' => $this->name,
            'username' => $this->username,
            'email' => $this->email,
            'password' => Hash::make($this->password),
            'is_active' => $this->is_active,
        ]);

        $this->saving = false;
        $this->savingAndNew = false;

        if ($createNew) {
            $this->reset(['name', 'username', 'email', 'password', 'password_confirmation', 'is_active']);
            session()->flash('admin-success', 'تم إنشاء المدير بنجاح. يمكنك إضافة مدير آخر.');
            return;
        }

        session()->flash('admin-success', 'تم إنشاء المدير بنجاح.');
        $this->redirectRoute('admin.admins.index', navigate: true);
    }

    public function render()
    {
        return view('admin-panel.pages.admin.parts.admin-create');
    }
}
