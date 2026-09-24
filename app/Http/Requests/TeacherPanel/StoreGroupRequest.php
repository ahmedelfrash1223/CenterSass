<?php

namespace App\Http\Requests\TeacherPanel;

use App\Enums\Teacher\GradeEnum;
use App\Enums\Teacher\GroupStatusEnum;
use App\Enums\Teacher\PricingTypeEnum;
use App\Models\TeacherSubject;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class StoreGroupRequest extends FormRequest
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
            // ============================================
            // Main Data
            // ============================================
            'name'          => ['required', 'string', 'max:255'],
            'branch_id'     => ['required', 'exists:branches,id'],
            'grade'         => ['required', 'string', Rule::in(array_keys(GradeEnum::options()))],
            'description'   => ['nullable', 'string'],
            'max_students'  => ['nullable', 'integer', 'min:0'],
            'status'        => ['required', Rule::in(GroupStatusEnum::values())],
            'start_date'    => ['nullable', 'date'],
            'end_date'      => ['nullable', 'date', 'after_or_equal:start_date'],

            // ============================================
            // Pricing
            // ============================================
            'has_pricing'    => ['boolean'],
            'pricing_type'   => ['required_if:has_pricing,true', 'in:' . implode(',', PricingTypeEnum::values())],
            'pricing_amount' => ['required_if:has_pricing,true', 'numeric', 'min:0'],

            // ============================================
            // Schedules (مع دعم التكرار)
            // ============================================
            'schedules'                => ['nullable', 'array'],
            'schedules.*.days'         => ['nullable', 'array'],
            'schedules.*.days.*'       => ['string', 'in:Saturday,Sunday,Monday,Tuesday,Wednesday,Thursday,Friday'],
            'schedules.*.start_time'   => ['nullable', 'date_format:H:i'],
            'schedules.*.end_time'     => ['nullable', 'date_format:H:i'],
            'schedules.*.subject_id'   => [
                'nullable',
                'exists:teacher_subjects,id',
                function ($attribute, $value, $fail) use ($ownerId) {
                    if (empty($value)) return;

                    $subject = TeacherSubject::where('id', $value)
                        ->where('teacher_id', $ownerId)
                        ->exists();
                    if (!$subject) {
                        $fail('المادة غير صالحة أو لا تنتمي لك.');
                    }
                },
            ],
            'schedules.*.teacher_id'   => [
                'nullable',
                'exists:teachers,id',
                function ($attribute, $value, $fail) use ($ownerId) {
                    if (empty($value)) return;

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
            'name.string'   => 'اسم المجموعة يجب أن يكون نصًا.',
            'name.max'      => 'اسم المجموعة يجب ألا يتجاوز 255 حرفًا.',

            'branch_id.required' => 'الفرع مطلوب.',
            'branch_id.exists'   => 'الفرع غير صالح.',

            'grade.required' => 'المرحلة الدراسية مطلوبة.',
            'grade.in'       => 'المرحلة الدراسية غير صالحة.',

            'status.required' => 'الحالة مطلوبة.',
            'status.in'       => 'الحالة غير صالحة.',

            'start_date.date'         => 'تاريخ البداية غير صالح.',
            'end_date.date'           => 'تاريخ النهاية غير صالح.',
            'end_date.after_or_equal' => 'تاريخ النهاية يجب أن يكون بعد تاريخ البداية.',

            // Pricing
            'pricing_type.required_if'   => 'نوع التسعير مطلوب.',
            'pricing_type.in'            => 'نوع التسعير غير صالح.',
            'pricing_amount.required_if' => 'المبلغ مطلوب.',
            'pricing_amount.numeric'     => 'المبلغ يجب أن يكون رقمًا.',
            'pricing_amount.min'         => 'المبلغ لا يمكن أن يكون سالبًا.',

            // Schedules
            'schedules.array'         => 'المواعيد يجب أن تكون مصفوفة.',
            'schedules.*.days.array'  => 'الأيام يجب أن تكون مصفوفة.',
            'schedules.*.days.*.in'   => 'اليوم غير صالح.',
            'schedules.*.start_time.date_format' => 'وقت البداية غير صالح.',
            'schedules.*.end_time.date_format'   => 'وقت النهاية غير صالح.',
            'schedules.*.subject_id.exists'      => 'المادة غير صالحة.',
            'schedules.*.teacher_id.exists'      => 'المدرس غير صالح.',
        ];
    }
}
