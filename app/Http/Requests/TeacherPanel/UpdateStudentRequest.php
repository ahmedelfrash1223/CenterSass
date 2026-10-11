<?php

namespace App\Http\Requests\TeacherPanel;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class UpdateStudentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $currentTeacher = Auth::guard('teacher')->user();
        $accessibleBranchIds = $currentTeacher->getAccessibleBranchIds();

        return [
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
            'grade' => ['required', 'string'],
            'branch_id' => [
                'required',
                Rule::in($accessibleBranchIds),
            ],
            'is_active' => ['required', 'in:0,1'],
            'group_ids' => ['nullable', 'array'],
            'group_ids.*' => [
                Rule::exists('groups', 'id')->where('teacher_id', $currentTeacher->getOwnerId()),
            ],

            'guardian_name' => ['nullable', 'string', 'max:255'],
            'guardian_phone' => ['nullable', 'string', 'max:20'],
            'guardian_relation' => ['nullable', 'string', 'max:30'],
            'school_name' => ['nullable', 'string', 'max:255'],
            'address' => ['nullable', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'اسم الطالب مطلوب.',
            'grade.required' => 'المرحلة الدراسية مطلوبة.',
            'branch_id.required' => 'الفرع مطلوب.',
            'branch_id.in' => 'الفرع غير متاح لك.',
            'group_ids.*.exists' => 'إحدى المجموعات غير صالحة.',
        ];
    }
}
