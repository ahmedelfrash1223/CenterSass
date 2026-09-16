<?php

namespace App\Http\Requests\TeacherPanel;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class UpdateStaffRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $ownerId = Auth::guard('teacher')->user()->getOwnerId();
        $teacherId = $this->input('teacher_id');

        return [
            'name' => ['required', 'string', 'max:255'],
            'username' => [
                'required', 'string', 'max:255', 'alpha_dash',
                Rule::unique('teachers', 'username')->ignore($teacherId),
            ],
            'email' => [
                'required', 'email', 'max:255',
                Rule::unique('teachers', 'email')->ignore($teacherId),
            ],
            'phone' => ['nullable', 'string', 'max:20'],
            'password' => ['nullable', 'string', 'min:6', 'confirmed'],
            'role' => ['required', 'in:2,3'],
            'is_active' => ['required', 'in:0,1'],

            'branch_ids' => ['nullable', 'array'],
            'branch_ids.*' => [
                Rule::exists('branches', 'id')->where('teacher_id', $ownerId),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'الاسم مطلوب.',
            'username.required' => 'اسم المستخدم مطلوب.',
            'username.unique' => 'اسم المستخدم مستخدم بالفعل.',
            'email.required' => 'البريد الإلكتروني مطلوب.',
            'email.unique' => 'البريد الإلكتروني مستخدم بالفعل.',
            'password.confirmed' => 'تأكيد كلمة المرور غير متطابق.',
            'password.min' => 'كلمة المرور يجب أن تكون 6 أحرف على الأقل.',
            'role.required' => 'نوع الحساب مطلوب.',
            'branch_ids.*.exists' => 'إحدى الفروع غير صالحة.',
        ];
    }
}
