<?php

namespace App\Http\Requests\AdminPanel;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateAdminRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $adminId = $this->input('admin_id') ?: $this->route('admin');
        $adminId = is_object($adminId) ? $adminId->id : $adminId;

        return [
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255', Rule::unique('admins', 'username')->ignore($adminId)],
            'email' => ['required', 'email', 'max:255', Rule::unique('admins', 'email')->ignore($adminId)],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'is_active' => ['required', 'integer', 'in:0,1'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'اسم المدير مطلوب.',
            'username.required' => 'اسم المستخدم مطلوب.',
            'username.unique' => 'اسم المستخدم مستخدم بالفعل.',
            'email.required' => 'البريد الإلكتروني مطلوب.',
            'email.email' => 'البريد الإلكتروني يجب أن يكون عنوان بريد صحيحًا.',
            'email.unique' => 'البريد الإلكتروني مستخدم بالفعل.',
            'password.min' => 'كلمة المرور يجب أن تكون 8 أحرف على الأقل.',
            'password.confirmed' => 'تأكيد كلمة المرور غير متطابق.',
            'is_active.required' => 'حالة المدير مطلوبة.',
            'is_active.in' => 'حالة المدير يجب أن تكون 0 أو 1.',
        ];
    }
}