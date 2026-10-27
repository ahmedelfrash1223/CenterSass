<?php

namespace App\Http\Requests\Api\Student\Auth;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'login' => ['required', 'string'],       // email OR phone
            'password' => ['required', 'string'],
            'device_name' => ['nullable', 'string', 'max:100'],
        ];
    }

    public function messages(): array
    {
        return [
            'login.required' => 'البريد الإلكتروني أو رقم الهاتف مطلوب.',
            'password.required' => 'كلمة المرور مطلوبة.',
        ];
    }
}
