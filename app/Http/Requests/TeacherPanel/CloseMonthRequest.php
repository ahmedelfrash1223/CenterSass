<?php

namespace App\Http\Requests\TeacherPanel;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class CloseMonthRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::guard('teacher')->check();
    }

    public function rules(): array
    {
        $branches = Auth::guard('teacher')->user()?->getAccessibleBranchIds() ?? [];

        return [
            'month' => ['required', 'date_format:Y-m'],
            'branchId' => ['nullable', 'integer', 'in:'.implode(',', $branches ?: [0])],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'month.required' => 'الشهر مطلوب.',
            'month.date_format' => 'صيغة الشهر غير صحيحة.',
            'branchId.in' => 'الفرع المختار غير متاح لك.',
            'notes.max' => 'الملاحظات طويلة جداً.',
        ];
    }

    public function attributes(): array
    {
        return [
            'month' => 'الشهر',
            'branchId' => 'الفرع',
            'notes' => 'الملاحظات',
        ];
    }
}
