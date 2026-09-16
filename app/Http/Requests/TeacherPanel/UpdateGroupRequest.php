<?php

namespace App\Http\Requests\TeacherPanel;

use App\Enums\Teacher\GroupStatusEnum;
use App\Enums\Teacher\GradeEnum;
use App\Models\TeacherSubject;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateGroupRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $groupId = $this->route('group')?->id;

        return [
            'name' => ['required', 'string', 'max:255'],
            'branch_id' => ['required', 'exists:branches,id'],
            'grade' => ['required', 'string', Rule::in(array_keys(GradeEnum::options()))],
            'description' => ['nullable', 'string'],
            'max_students' => ['nullable', 'integer', 'min:0'],
            'status' => ['required', Rule::in(GroupStatusEnum::values())],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'schedules' => ['nullable', 'array'],
            'schedules.*.day' => ['required', 'string', 'in:Saturday,Sunday,Monday,Tuesday,Wednesday,Thursday,Friday'],
            'schedules.*.start_time' => ['required', 'date_format:H:i'],
            'schedules.*.end_time' => ['required', 'date_format:H:i', 'after:schedules.*.start_time'],
            'schedules.*.subject_id' => [
                'required',
                'exists:teacher_subjects,id',
                function ($attribute, $value, $fail) {
                    $subject = TeacherSubject::where('id', $value)
                        ->where('teacher_id', auth('teacher')->id())
                        ->exists();
                    if (!$subject) {
                        $fail('المادة غير صالحة أو لا تنتمي لك.');
                    }
                },
            ],
            'schedules.*.teacher_id' => [
                'required',
                'exists:teachers,id',
                function ($attribute, $value, $fail) {
                    $currentTeacher = auth('teacher')->user();
                    $ownerId = $currentTeacher->getOwnerId();

                    $isValid = \App\Models\Teacher::where('id', $value)
                        ->where(function ($query) use ($ownerId) {
                            $query->where('id', $ownerId)
                                ->orWhere('owner_id', $ownerId);
                        })
                        ->exists();

                    if (!$isValid) {
                        $fail('المدرس غير صالح أو لا ينتمي لحسابك.');
                    }
                },
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'اسم المجموعة مطلوب.',
            'name.string' => 'اسم المجموعة يجب أن يكون نصًا.',
            'name.max' => 'اسم المجموعة يجب ألا يتجاوز 255 حرفًا.',
            'name.unique' => 'اسم المجموعة مستخدم بالفعل.',

            'branch_id.required' => 'الفرع مطلوب.',
            'branch_id.exists' => 'الفرع غير صالح.',

            'grade.required' => 'المرحلة الدراسية مطلوبة.',
            'grade.in' => 'المرحلة الدراسية غير صالحة.',

            'status.required' => 'الحالة مطلوبة.',
            'status.in' => 'الحالة غير صالحة.',

            'start_date.date' => 'تاريخ البداية غير صالح.',
            'end_date.date' => 'تاريخ النهاية غير صالح.',
            'end_date.after_or_equal' => 'تاريخ النهاية يجب أن يكون بعد تاريخ البداية.',

            'schedules.array' => 'المواعيد يجب أن تكون مصفوفة.',

            'schedules.*.day.required' => 'اليوم مطلوب.',
            'schedules.*.day.in' => 'اليوم غير صالح.',

            'schedules.*.start_time.required' => 'وقت البداية مطلوب.',
            'schedules.*.start_time.date_format' => 'وقت البداية غير صالح.',

            'schedules.*.end_time.required' => 'وقت النهاية مطلوب.',
            'schedules.*.end_time.date_format' => 'وقت النهاية غير صالح.',
            'schedules.*.end_time.after' => 'وقت النهاية يجب أن يكون بعد وقت البداية.',

            'schedules.*.subject_id.required' => 'المادة مطلوبة.',
            'schedules.*.subject_id.exists' => 'المادة غير صالحة.',

            'schedules.*.teacher_id.required' => 'المدرس مطلوب.',
            'schedules.*.teacher_id.exists' => 'المدرس غير صالح.',
        ];
    }
}
