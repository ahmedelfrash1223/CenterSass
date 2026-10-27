<?php

namespace App\Http\Requests\Api\Student\Link;

use Illuminate\Foundation\Http\FormRequest;

class ScanStudentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth('student_account')->check();
    }

    public function rules(): array
    {
        return [
            'barcode' => ['required', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'barcode.required' => 'الباركود مطلوب.',
            'barcode.string'   => 'الباركود غير صالح.',
        ];
    }
}
