<?php

namespace App\Http\Requests\TeacherPanel;

use App\Enums\Teacher\AssignmentStatusEnum;
use App\Enums\Teacher\AssignmentTypeEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class StoreAssignmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $currentTeacher = Auth::guard('teacher')->user();
        $ownerId = $currentTeacher->getOwnerId();

        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'type' => ['required', Rule::enum(AssignmentTypeEnum::class)],
            'status' => ['required', Rule::enum(AssignmentStatusEnum::class)],
            'max_score' => ['required', 'numeric', 'min:0.5', 'max:1000'],
            'due_date' => ['nullable', 'date'],

            'group_id' => [
                'required',
                Rule::exists('groups', 'id')->where('teacher_id', $ownerId),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'عنوان المتابعة مطلوب.',
            'type.required' => 'نوع المتابعة مطلوب.',
            'status.required' => 'حالة المتابعة مطلوبة.',
            'max_score.required' => 'الدرجة العظمى مطلوبة.',
            'max_score.min' => 'الدرجة العظمى يجب أن تكون 0.5 على الأقل.',
            'group_id.required' => 'المجموعة مطلوبة.',
            'group_id.exists' => 'المجموعة غير صالحة.',
        ];
    }
}
