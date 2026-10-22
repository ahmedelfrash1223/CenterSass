<?php

namespace App\Http\Requests\AdminPanel;

use Illuminate\Foundation\Http\FormRequest;

class ReviewCardRequestRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'action' => ['required', 'in:approve,reject'],
            'admin_notes' => ['nullable', 'string', 'max:2000', 'required_if:action,reject'],
        ];
    }

    public function messages(): array
    {
        return [
            'action.required' => 'الإجراء مطلوب.',
            'action.in' => 'الإجراء يجب أن يكون قبول أو رفض.',
            'admin_notes.required_if' => 'سبب الرفض مطلوب.',
            'admin_notes.max' => 'الملاحظات طويلة جدًا.',
        ];
    }
}
