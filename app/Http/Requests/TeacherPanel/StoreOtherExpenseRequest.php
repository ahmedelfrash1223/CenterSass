<?php

namespace App\Http\Requests\TeacherPanel;

use App\Enums\Teacher\ExpenseSourceEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StoreOtherExpenseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::guard('teacher')->check();
    }

    public function rules(): array
    {
        $user = Auth::guard('teacher')->user();
        $accessibleBranches = $user?->getAccessibleBranchIds() ?? [];
        $allowedSources = implode(',', array_keys(ExpenseSourceEnum::otherOptions()));
        $allowedBranches = implode(',', $accessibleBranches ?: [0]);

        return [
            'otherSource' => ['required', 'in:'.$allowedSources],
            'otherReason' => ['required', 'string', 'min:3', 'max:2000'],
            'otherDescription' => ['nullable', 'string', 'max:1000'],
            'otherAmount' => ['required', 'numeric', 'min:0.01'],
            'otherBranchId' => ['required', 'integer', 'in:'.$allowedBranches],
            'otherOccurredAt' => ['required', 'date'],
        ];
    }

    public function messages(): array
    {
        return [
            'otherSource.required' => 'اختر نوع المصروف.',
            'otherSource.in' => 'نوع المصروف غير صالح.',
            'otherReason.required' => 'سبب/ملاحظات المصروف مطلوب.',
            'otherReason.min' => 'السبب قصير جداً (3 أحرف على الأقل).',
            'otherReason.max' => 'السبب طويل جداً (2000 حرف كحد أقصى).',
            'otherDescription.max' => 'الوصف طويل جداً (1000 حرف كحد أقصى).',
            'otherAmount.required' => 'المبلغ مطلوب.',
            'otherAmount.numeric' => 'المبلغ يجب أن يكون رقماً.',
            'otherAmount.min' => 'المبلغ يجب أن يكون أكبر من صفر.',
            'otherBranchId.in' => 'الفرع المختار غير متاح لك.',
            'otherBranchId.required' => 'تاريخ المصروف مطلوب.',
            'otherOccurredAt.required' => 'تاريخ المصروف مطلوب.',
            'otherOccurredAt.date' => 'تاريخ المصروف غير صالح.',
        ];
    }

    public function attributes(): array
    {
        return [
            'otherSource' => 'نوع المصروف',
            'otherReason' => 'السبب',
            'otherDescription' => 'الوصف',
            'otherAmount' => 'المبلغ',
            'otherBranchId' => 'الفرع',
            'otherOccurredAt' => 'تاريخ المصروف',
        ];
    }
}
