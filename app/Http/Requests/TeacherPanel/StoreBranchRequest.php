<?php

namespace App\Http\Requests\TeacherPanel;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreBranchRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('branches', 'name')->where('teacher_id', auth('teacher')->id()),
            ],
            'address' => ['required', 'string'],
            'phone' => ['nullable', 'string', 'max:20'],
            'email' => ['nullable', 'email', 'max:255'],
            'is_active' => ['required', 'integer', 'in:0,1'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'اسم الفرع مطلوب.',
            'name.unique' => 'لديك فرع آخر بنفس الاسم.',
            'address.required' => 'عنوان الفرع مطلوب.',
            'phone.max' => 'رقم الهاتف يجب ألا يتجاوز 20 حرفًا.',
            'email.email' => 'البريد الإلكتروني غير صالح.',
            'is_active.required' => 'حالة الفرع مطلوبة.',
            'is_active.in' => 'حالة الفرع يجب أن تكون 0 أو 1.',
        ];
    }
}