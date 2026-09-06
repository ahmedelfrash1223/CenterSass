<?php

namespace App\Livewire\AdminPanel\Pages\Teacher\Parts;

use App\Http\Requests\AdminPanel\UpdateTeacherRequest;
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

    public bool $updating = false;

    public function mount(Teacher $teacher): void
    {
        $this->teacher = $teacher;
        $this->name = $teacher->name;
        $this->username = $teacher->username;
        $this->email = $teacher->email;
        $this->phone = $teacher->phone;
        $this->is_active = $teacher->is_active;
    }

    public function update(): void
    {
        $this->updating = true;

        $request = new UpdateTeacherRequest();

        $request->merge(['teacher_id' => $this->teacher->id]);

        $this->validate($request->rules(), $request->messages());

        $data = [
            'name' => $this->name,
            'username' => $this->username,
            'email' => $this->email,
            'phone' => $this->phone,
            'is_active' => $this->is_active,
        ];

        if (! empty($this->password)) {
            $data['password'] = Hash::make($this->password);
        }

        $this->teacher->update($data);

        $this->updating = false;

        session()->flash('teacher-success', 'تم تحديث بيانات المدرس بنجاح.');
        $this->redirectRoute('admin.teacher.index', navigate: true);
    }

    public function render()
    {
        return view('admin-panel.pages.teacher.parts.teacher-update');
    }
}
