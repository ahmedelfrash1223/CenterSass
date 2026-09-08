<?php

namespace App\Http\Requests\TeacherPanel;

use App\Enums\Teacher\GroupStatusEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreGroupRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'branch_id' => ['required', 'exists:branches,id'],
            'description' => ['nullable', 'string'],
            'max_students' => ['nullable', 'integer', 'min:0'],
            'status' => ['required', Rule::in(GroupStatusEnum::values())],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'schedules' => ['nullable', 'array', 'min:1'],
            'schedules.*.day' => ['required', 'string', 'in:Saturday,Sunday,Monday,Tuesday,Wednesday,Thursday,Friday'],
            'schedules.*.start_time' => ['required', 'date_format:H:i'],
            'schedules.*.end_time' => ['required', 'date_format:H:i', 'after:schedules.*.start_time'],
            'schedules.*.subject_id' => [
                'required',
                'exists:teacher_subjects,id',
                function ($attribute, $value, $fail) {
                    $subject = \App\Models\TeacherSubject::where('id', $value)
                        ->where('teacher_id', auth('teacher')->id())
                        ->exists();
                    if (!$subject) {
                        $fail('المادة غير صالحة أو لا تنتمي لك.');
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

            'branch_id.required' => 'الفرع مطلوب.',
            'branch_id.exists' => 'الفرع غير صالح.',

            'status.required' => 'الحالة مطلوبة.',
            'status.in' => 'الحالة غير صالحة.',

            'start_date.date' => 'تاريخ البداية غير صالح.',
            'end_date.date' => 'تاريخ النهاية غير صالح.',
            'end_date.after_or_equal' => 'تاريخ النهاية يجب أن يكون بعد تاريخ البداية.',

            'schedules.required' => 'يجب إضافة موعد واحد على الأقل.',
            'schedules.array' => 'المواعيد يجب أن تكون مصفوفة.',
            'schedules.min' => 'يجب إضافة موعد واحد على الأقل.',

            'schedules.*.day.required' => 'اليوم مطلوب.',
            'schedules.*.day.in' => 'اليوم غير صالح.',

            'schedules.*.start_time.required' => 'وقت البداية مطلوب.',
            'schedules.*.start_time.date_format' => 'وقت البداية غير صالح.',

            'schedules.*.end_time.required' => 'وقت النهاية مطلوب.',
            'schedules.*.end_time.date_format' => 'وقت النهاية غير صالح.',
            'schedules.*.end_time.after' => 'وقت النهاية يجب أن يكون بعد وقت البداية.',

            'schedules.*.subject_id.required' => 'المادة مطلوبة.',
            'schedules.*.subject_id.exists' => 'المادة غير صالحة.',
        ];
    }
}
