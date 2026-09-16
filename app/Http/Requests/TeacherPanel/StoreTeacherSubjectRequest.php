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
            'grades' => ['required', 'array', 'min:1'],
            'grades.*' => ['required', 'string', Rule::in(array_keys(GradeEnum::options()))],
            'subjects' => ['required', 'array', 'min:1'],
            'subjects.*' => ['required', 'string', Rule::in(array_keys(SubjectEnum::options()))],
            'is_active' => ['required', 'integer', 'in:0,1'],
        ];
    }

    public function messages(): array
    {
        return [
            'grades.required' => 'يرجى اختيار مرحلة دراسية واحدة على الأقل.',
            'grades.array' => 'المراحل يجب أن تكون قائمة صحيحة.',
            'grades.min' => 'يرجى اختيار مرحلة دراسية واحدة على الأقل.',
            'grades.*.required' => 'المرحلة مطلوبة.',
            'grades.*.in' => 'المرحلة المحددة غير صالحة.',

            'subjects.required' => 'يرجى اختيار مادة واحدة على الأقل.',
            'subjects.array' => 'المواد يجب أن تكون قائمة صحيحة.',
            'subjects.min' => 'يرجى اختيار مادة واحدة على الأقل.',
            'subjects.*.required' => 'المادة مطلوبة.',
            'subjects.*.in' => 'المادة المحددة غير صالحة.',

            'is_active.required' => 'حالة المادة مطلوبة.',
            'is_active.integer' => 'حالة المادة غير صالحة.',
            'is_active.in' => 'حالة المادة يجب أن تكون 0 أو 1.',
        ];
    }
}
