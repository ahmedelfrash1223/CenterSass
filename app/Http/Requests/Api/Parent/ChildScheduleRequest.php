<?php

namespace App\Http\Requests\Api\Parent;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ChildScheduleRequest extends FormRequest
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
}
