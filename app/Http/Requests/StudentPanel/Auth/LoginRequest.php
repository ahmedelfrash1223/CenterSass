<?php

namespace App\Http\Requests\StudentPanel\Auth;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, array<int, string>> */
    public function rules(): array
    {
        return [
            'login' => ['required', 'string', 'max:255'],
            'password' => ['required', 'string', 'max:255'],
        ];
    }

    /** @return array<string, string> */
    public function messages(): array
    {
        return [
            'login.required' => 'البريد الإلكتروني أو اسم المستخدم أو رقم الهاتف مطلوب.',
            'login.max' => 'بيانات تسجيل الدخول أطول من الحد المسموح.',
            'password.required' => 'كلمة المرور مطلوبة.',
            'password.max' => 'كلمة المرور أطول من الحد المسموح.',
        ];
    }
}
