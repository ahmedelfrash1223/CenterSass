<?php

namespace App\Http\Requests\Api\Student\Link;

use Illuminate\Foundation\Http\FormRequest;

class UnlinkStudentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth('student_account')->check();
    }

    public function rules(): array
    {
        return [
            'student_id' => ['required', 'integer', 'exists:students,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'student_id.required' => 'رقم الطالب مطلوب.',
            'student_id.exists'   => 'الطالب غير موجود.',
        ];
    }
}
