<?php

namespace App\Http\Requests\AdminPanel;

use App\Enums\Teacher\TeacherRoleEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateTeacherRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $teacherId = $this->input('teacher_id');

        return [
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255', Rule::unique('teachers', 'username')->ignore($teacherId)],
            'email' => ['required', 'email', 'max:255', Rule::unique('teachers', 'email')->ignore($teacherId)],
            'phone' => ['nullable', 'string', 'max:20', Rule::unique('teachers', 'phone')->ignore($teacherId)],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'role' => ['required', 'integer', Rule::in(TeacherRoleEnum::values())],
            'owner_id' => [
                'nullable',
                'integer',
                'exists:teachers,id',
                Rule::requiredIf(fn() => (int) $this->input('role') !== TeacherRoleEnum::OWNER->value),
            ],
            'is_active' => ['required', 'integer', 'in:0,1'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'اسم المدرس مطلوب.',
            'name.string' => 'اسم المدرس يجب أن يكون نصًا.',
            'name.max' => 'اسم المدرس يجب ألا يتجاوز 255 حرفًا.',

            'username.required' => 'اسم المستخدم مطلوب.',
            'username.string' => 'اسم المستخدم يجب أن يكون نصًا.',
            'username.max' => 'اسم المستخدم يجب ألا يتجاوز 255 حرفًا.',
            'username.unique' => 'اسم المستخدم مستخدم بالفعل.',

            'email.required' => 'البريد الإلكتروني مطلوب.',
            'email.email' => 'البريد الإلكتروني يجب أن يكون عنوان بريد صحيحًا.',
            'email.max' => 'البريد الإلكتروني يجب ألا يتجاوز 255 حرفًا.',
            'email.unique' => 'البريد الإلكتروني مستخدم بالفعل.',

            'phone.string' => 'رقم الهاتف يجب أن يكون نصًا.',
            'phone.max' => 'رقم الهاتف يجب ألا يتجاوز 20 حرفًا.',
            'phone.unique' => 'رقم الهاتف مستخدم بالفعل.',

            'password.string' => 'كلمة المرور يجب أن تكون نصًا.',
            'password.min' => 'كلمة المرور يجب أن تكون 8 أحرف على الأقل.',
            'password.confirmed' => 'تأكيد كلمة المرور غير متطابق.',

            'role.required' => 'نوع الحساب مطلوب.',
            'role.integer' => 'نوع الحساب غير صالح.',
            'role.in' => 'نوع الحساب المحدد غير صالح.',

            'owner_id.required_if' => 'يجب اختيار المسئول التابع له الحساب.',
            'owner_id.integer' => 'المسئول غير صالح.',
            'owner_id.exists' => 'المسئول المحدد غير موجود.',

            'is_active.required' => 'حالة المدرس مطلوبة.',
            'is_active.integer' => 'حالة المدرس غير صالحة.',
            'is_active.in' => 'حالة المدرس يجب أن تكون 0 أو 1.',
        ];
    }
}
