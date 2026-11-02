<?php

namespace App\Http\Requests\Api\Student\Auth;

use Illuminate\Foundation\Http\FormRequest;

class GoogleCallbackRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'id_token' => ['required', 'string'],
            'account_type' => ['required', 'string', 'in:student,parent'],
            'device_name' => ['nullable', 'string', 'max:100'],
        ];
    }

    public function messages(): array
    {
        return [
            'id_token.required' => 'Google ID token مطلوب.',
            'account_type.required' => 'يجب تحديد نوع الحساب (طالب أو ولي أمر).',
            'account_type.in' => 'نوع الحساب غير صالح.',
        ];
    }
}
