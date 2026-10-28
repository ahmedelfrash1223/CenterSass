<?php

namespace App\Http\Requests\Api\Student\Assignment;

use Illuminate\Foundation\Http\FormRequest;

class ListAssignmentGradeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth('student_account')->check();
    }

    public function rules(): array
    {
        return [
            'group_id' => ['nullable', 'integer', 'exists:groups,id'],
            'type' => ['nullable', 'string', 'in:homework,recitation,exam,project,other'],
            'month' => ['nullable', 'date_format:Y-m'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
            'page' => ['nullable', 'integer', 'min:1'],
        ];
    }

    public function messages(): array
    {
        return [
            'group_id.exists' => 'المجموعة غير موجودة.',
            'type.in' => 'نوع التقييم غير صالح.',
            'month.date_format' => 'صيغة الشهر يجب أن تكون Y-m.',
        ];
    }
}
