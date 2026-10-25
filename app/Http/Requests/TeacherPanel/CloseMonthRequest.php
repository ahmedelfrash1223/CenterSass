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
            'closeMonth' => ['required', 'date_format:Y-m'],
            'closeBranchId' => ['required', 'integer', 'in:'.implode(',', $branches ?: [0])],
            'closeNotes' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'closeMonth.required' => 'الشهر مطلوب.',
            'closeMonth.date_format' => 'صيغة الشهر غير صحيحة.',
            'closeBranchId.required' => 'يجب اختيار الفرع.',
            'closeBranchId.integer' => 'الفرع غير صالح.',
            'closeBranchId.in' => 'الفرع المختار غير متاح لك.',
            'closeNotes.max' => 'الملاحظات طويلة جداً.',
        ];
    }

    public function attributes(): array
    {
        return [
            'closeMonth' => 'الشهر',
            'closeBranchId' => 'الفرع',
            'closeNotes' => 'الملاحظات',
        ];
    }
}
