<?php

namespace App\Http\Requests\Api\Student\Attendance;

use Illuminate\Foundation\Http\FormRequest;

class ListAttendanceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth('student_account')->check();
    }

    public function rules(): array
    {
        return [
            'month' => ['nullable', 'date_format:Y-m'],
            'date_from' => ['nullable', 'date'],
            'date_to' => ['nullable', 'date', 'after_or_equal:date_from'],
            'group_id' => ['nullable', 'integer', 'exists:groups,id'],
            'status' => ['nullable', 'in:present,late,absent,excused'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
            'page' => ['nullable', 'integer', 'min:1'],
        ];
    }

    public function messages(): array
    {
        return [
            'month.date_format' => 'صيغة الشهر يجب أن تكون Y-m.',
            'date_from.date' => 'تاريخ البداية غير صالح.',
            'date_to.date' => 'تاريخ النهاية غير صالح.',
            'date_to.after_or_equal' => 'تاريخ النهاية يجب أن يكون بعد تاريخ البداية.',
            'group_id.exists' => 'المجموعة غير موجودة.',
            'status.in' => 'حالة الحضور غير صالحة.',
        ];
    }
}
