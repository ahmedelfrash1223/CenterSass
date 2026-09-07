<?php

namespace App\Livewire\AdminPanel\Pages\Teacher\Parts;

use App\Http\Requests\AdminPanel\StoreTeacherRequest;
use App\Models\Teacher;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;

class TeacherCreate extends Component
{
    // Default password constant
    private const DEFAULT_PASSWORD = '12345678';

    public string $name = '';
    public string $username = '';
    public string $email = '';
    public string $phone = '';
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

        $request = new StoreTeacherRequest();
        $this->validate($request->rules(), $request->messages());

        $teacher = Teacher::create([
            'name' => $this->name,
            'username' => $this->username,
            'email' => $this->email,
            'phone' => $this->phone,
            'password' => Hash::make($this->password),
            'is_active' => $this->is_active,
        ]);

        $this->saving = false;
        $this->savingAndNew = false;

        if ($createNew) {
            $this->reset(['name', 'username', 'email', 'phone', 'password', 'password_confirmation', 'is_active']);
            session()->flash('teacher-success', 'تم إنشاء المدرس بنجاح. يمكنك إضافة مدرس آخر.');
            return;
        }

        session()->flash('teacher-success', 'تم إنشاء المدرس بنجاح.');
        $this->redirectRoute('admin.teacher.index', navigate: true);
    }

    public function render()
    {
        return view('admin-panel.pages.teacher.parts.teacher-create');
    }
}
