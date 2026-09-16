<?php

namespace App\Http\Requests\AdminPanel;

use App\Enums\Teacher\TeacherRoleEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreTeacherRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $isOwner = (int) $this->input('role') === TeacherRoleEnum::OWNER->value;

        return [
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255', 'unique:teachers,username'],
            'email' => ['required', 'email', 'max:255', 'unique:teachers,email'],
            'phone' => ['nullable', 'string', 'max:20', 'unique:teachers,phone'],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'role' => ['required', 'integer', Rule::in(TeacherRoleEnum::values())],
            'owner_id' => $isOwner
                ? ['nullable']
                : ['required', 'integer', 'exists:teachers,id'],
            'is_active' => ['required', 'integer', 'in:0,1'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'اسم المدرس مطلوب.',
            'username.required' => 'اسم المستخدم مطلوب.',
            'username.unique' => 'اسم المستخدم مستخدم بالفعل.',
            'email.required' => 'البريد الإلكتروني مطلوب.',
            'email.email' => 'البريد الإلكتروني غير صالح.',
            'email.unique' => 'البريد الإلكتروني مستخدم بالفعل.',
            'phone.unique' => 'رقم الهاتف مستخدم بالفعل.',
            'password.min' => 'كلمة المرور يجب أن تكون 8 أحرف على الأقل.',
            'password.confirmed' => 'تأكيد كلمة المرور غير متطابق.',
            'role.required' => 'نوع الحساب مطلوب.',
            'role.in' => 'نوع الحساب المحدد غير صالح.',
            'owner_id.required' => 'يجب اختيار المسئول التابع له الحساب.',
            'owner_id.exists' => 'المسئول المحدد غير موجود.',
            'is_active.required' => 'الحالة مطلوبة.',
            'is_active.in' => 'الحالة يجب أن تكون 0 أو 1.',
        ];
    }
}
