<?php

namespace App\Http\Requests\TeacherPanel;

use App\Enums\Teacher\GradeEnum;
use App\Enums\Teacher\SubjectEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreTeacherSubjectRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'grade' => ['required', 'string', Rule::in(array_keys(GradeEnum::options()))],
            'subject' => ['required', 'string', Rule::in(array_keys(SubjectEnum::options()))],
            'is_active' => ['required', 'integer', 'in:0,1'],
        ];
    }

    public function messages(): array
    {
        return [
            'grade.required' => 'يرجى اختيار المرحلة.',
            'grade.in' => 'المرحلة المحددة غير صالحة.',
            'subject.required' => 'يرجى اختيار المادة.',
            'subject.in' => 'المادة المحددة غير صالحة.',
            'is_active.required' => 'حالة المادة مطلوبة.',
            'is_active.in' => 'حالة المادة يجب أن تكون 0 أو 1.',
        ];
    }
}