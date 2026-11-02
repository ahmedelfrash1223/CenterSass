<?php

namespace App\Http\Requests\Api\Student\Schedule;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ScheduleByDayRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth('student_account')->check();
    }

    public function rules(): array
    {
        return [
            'day' => [
                'nullable',
                'string',
                Rule::in(['Saturday', 'Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'])
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'day.in' => 'اليوم غير صالح (استخدم Saturday, Sunday, ...).',
        ];
    }
}
