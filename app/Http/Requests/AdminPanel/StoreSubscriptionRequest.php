<?php

namespace App\Http\Requests\AdminPanel;

use App\Enums\Admin\DiscountTypeEnum;
use App\Enums\Admin\SubscriptionStatusEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreSubscriptionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'teacher_id' => ['required', 'integer', 'exists:teachers,id'],
            'package_id' => ['required', 'integer', 'exists:packages,id'],
            'starts_at' => ['required', 'date'],
            'ends_at' => ['required', 'date', 'after_or_equal:starts_at'],
            'price' => ['required', 'numeric', 'min:0'],
            'discount_type' => ['nullable', 'integer', Rule::in(array_column(DiscountTypeEnum::cases(), 'value'))],
            'discount_value' => ['required', 'numeric', 'min:0'],
            'price_after_discount' => ['required', 'numeric', 'min:0'],
            'status' => ['required', 'integer', Rule::in(array_column(SubscriptionStatusEnum::cases(), 'value'))],
            'is_paid' => ['required', 'boolean'],
            'paid_at' => ['nullable', 'date'],
            'parent_id' => ['nullable', 'integer', 'exists:subscriptions,id'],
            'features' => ['nullable', 'array'],
            'features.*.key' => ['required', 'integer'],
            'features.*.value' => ['required'],
        ];
    }

    public function messages(): array
    {
        return [
            'teacher_id.required' => 'المدرس مطلوب.',
            'teacher_id.exists' => 'المدرس المحدد غير موجود.',
            'package_id.required' => 'الباقة مطلوبة.',
            'package_id.exists' => 'الباقة المحددة غير موجودة.',
            'starts_at.required' => 'تاريخ البداية مطلوب.',
            'ends_at.required' => 'تاريخ النهاية مطلوب.',
            'ends_at.after_or_equal' => 'تاريخ النهاية يجب أن يكون بعد تاريخ البداية.',
            'price.required' => 'السعر مطلوب.',
            'price.numeric' => 'السعر يجب أن يكون رقمًا.',
            'discount_value.numeric' => 'قيمة الخصم يجب أن تكون رقمًا.',
            'price_after_discount.required' => 'السعر بعد الخصم مطلوب.',
            'status.required' => 'حالة الاشتراك مطلوبة.',
            'status.in' => 'حالة الاشتراك غير صالحة.',
            'is_paid.required' => 'حالة الدفع مطلوبة.',
            'paid_at.date' => 'تاريخ الدفع غير صالح.',
            'parent_id.exists' => 'الاشتراك الأساسي غير موجود.',
        ];
    }
}