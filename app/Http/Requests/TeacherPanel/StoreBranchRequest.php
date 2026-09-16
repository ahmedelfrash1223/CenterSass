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
        // Use owner id so staff/assistant share the same uniqueness scope
        $ownerId = auth('teacher')->user()->getOwnerId();

        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('branches', 'name')->where('teacher_id', $ownerId),
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
            'name.string' => 'اسم الفرع يجب أن يكون نصًا.',
            'name.max' => 'اسم الفرع يجب ألا يتجاوز 255 حرفًا.',
            'name.unique' => 'لديك فرع آخر بنفس الاسم.',
            'address.required' => 'عنوان الفرع مطلوب.',
            'address.string' => 'العنوان يجب أن يكون نصًا.',
            'phone.string' => 'رقم الهاتف يجب أن يكون نصًا.',
            'phone.max' => 'رقم الهاتف يجب ألا يتجاوز 20 حرفًا.',
            'email.email' => 'البريد الإلكتروني غير صالح.',
            'email.max' => 'البريد الإلكتروني يجب ألا يتجاوز 255 حرفًا.',
            'is_active.required' => 'حالة الفرع مطلوبة.',
            'is_active.in' => 'حالة الفرع يجب أن تكون 0 أو 1.',
        ];
    }
}
