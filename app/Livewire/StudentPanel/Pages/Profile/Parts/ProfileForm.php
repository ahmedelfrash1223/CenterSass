<?php

namespace App\Livewire\StudentPanel\Pages\Profile\Parts;

use App\Enums\Teacher\GradeEnum;
use App\Models\Student;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Livewire\Component;

class ProfileForm extends Component
{
    public ?string $editing = null;

    public string $name = '';

    public string $username = '';

    public string $email = '';

    public string $phone = '';

    public string $current_password = '';

    public string $new_password = '';

    public string $new_password_confirmation = '';

    public function mount(): void
    {
        $this->loadStudentData();
    }

    private function authenticatedStudent(): Student
    {
        $student = Auth::guard('student')->user();
        abort_unless($student instanceof Student && $student->is_active, 403);

        return $student;
    }

    private function loadStudentData(): void
    {
        $student = $this->authenticatedStudent();
        $this->name = $student->name;
        $this->username = $student->username;
        $this->email = $student->email;
        $this->phone = $student->phone ?? '';
    }

    public function startEditing(string $field): void
    {
        $this->authenticatedStudent();

        if (! in_array($field, ['name', 'username', 'email', 'phone'], true)) {
            return;
        }

        $this->editing = $field;
        $this->resetValidation();
    }

    public function cancelEditing(): void
    {
        $this->loadStudentData();
        $this->editing = null;
        $this->resetValidation();
    }

    public function save(): void
    {
        $student = $this->authenticatedStudent();

        $this->validate([
            'name' => ['required', 'string', 'min:2', 'max:191'],
            'username' => ['required', 'string', 'min:3', 'max:191', Rule::unique('students', 'username')->ignore($student)],
            'email' => ['required', 'email', 'max:191', Rule::unique('students', 'email')->ignore($student)],
            'phone' => ['nullable', 'string', 'max:20', Rule::unique('students', 'phone')->ignore($student)],
        ], [
            'required' => 'هذا الحقل مطلوب.',
            'min' => 'هذا الحقل أقصر من الحد المسموح.',
            'max' => 'هذا الحقل أطول من الحد المسموح.',
            'email.email' => 'البريد الإلكتروني غير صالح.',
            'username.unique' => 'اسم المستخدم مستخدم بالفعل.',
            'email.unique' => 'البريد الإلكتروني مستخدم بالفعل.',
            'phone.unique' => 'رقم الهاتف مستخدم بالفعل.',
        ]);

        $student->update([
            'name' => $this->name,
            'username' => $this->username,
            'email' => $this->email,
            'phone' => $this->phone ?: null,
        ]);

        $this->loadStudentData();
        $this->editing = null;
        $this->dispatch('student-profile-updated');
        session()->flash('profile-success', 'تم تحديث بيانات الحساب بنجاح.');
    }

    public function changePassword(): void
    {
        $student = $this->authenticatedStudent();

        $this->validate([
            'current_password' => ['required', 'current_password:student'],
            'new_password' => ['required', 'string', 'min:8', 'max:255', 'confirmed'],
        ], [
            'current_password.required' => 'يرجى إدخال كلمة المرور الحالية.',
            'current_password.current_password' => 'كلمة المرور الحالية غير صحيحة.',
            'new_password.required' => 'يرجى إدخال كلمة المرور الجديدة.',
            'new_password.min' => 'يجب أن تكون كلمة المرور الجديدة 8 أحرف على الأقل.',
            'new_password.max' => 'كلمة المرور أطول من الحد المسموح.',
            'new_password.confirmed' => 'تأكيد كلمة المرور غير متطابق.',
        ]);

        $student->update(['password' => Hash::make($this->new_password)]);
        $this->clearPasswordFields();
        $this->dispatch('student-password-updated');
        session()->flash('password-success', 'تم تغيير كلمة المرور بنجاح.');
    }

    public function clearPasswordFields(): void
    {
        $this->reset('current_password', 'new_password', 'new_password_confirmation');
        $this->resetValidation();
    }

    public function render(): View
    {
        $student = $this->authenticatedStudent();
        $student->load([
            'profile',
            'branch' => fn ($query) => $query->where('teacher_id', $student->teacher_id),
        ]);
        $gradeLabel = GradeEnum::tryFrom($student->grade ?? '')?->label() ?? ($student->grade ?: 'غير محدد');

        return view('student-panel.pages.profile.parts.profile-form', compact('student', 'gradeLabel'));
    }
}
