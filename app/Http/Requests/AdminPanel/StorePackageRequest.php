<?php

namespace App\Http\Requests\AdminPanel;

use App\Enums\Admin\DiscountTypeEnum;
use App\Enums\Admin\DurationTypeEnum;
use App\Enums\Admin\PackageFeatureKeyEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePackageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'price' => ['required', 'numeric', 'min:0'],
            'discount_type' => ['required', 'integer', Rule::enum(DiscountTypeEnum::class)],
            'discount_value' => ['required', 'numeric', 'min:0'],
            'duration' => ['required', 'integer', 'min:1'],
            'duration_type' => ['required', 'integer', Rule::enum(DurationTypeEnum::class)],
            'features' => ['required', 'array'],
            'features.*.key' => ['required', 'integer', 'distinct', Rule::enum(PackageFeatureKeyEnum::class)],
            'features.*.value' => ['required', 'numeric', 'min:0'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'اسم الباقة مطلوب.',
            'title.string' => 'اسم الباقة يجب أن يكون نصًا.',
            'title.max' => 'اسم الباقة يجب ألا يتجاوز 255 حرفًا.',

            'price.required' => 'سعر الباقة مطلوب.',
            'price.numeric' => 'سعر الباقة يجب أن يكون رقمًا.',
            'price.min' => 'سعر الباقة لا يمكن أن يكون أقل من صفر.',

            'discount_type.required' => 'نوع الخصم مطلوب.',
            'discount_type.integer' => 'نوع الخصم غير صالح.',
            'discount_type.enum' => 'نوع الخصم المحدد غير صالح.',

            'discount_value.required' => 'قيمة الخصم مطلوبة.',
            'discount_value.numeric' => 'قيمة الخصم يجب أن تكون رقمًا.',
            'discount_value.min' => 'قيمة الخصم لا يمكن أن تكون أقل من صفر.',

            'duration.required' => 'مدة الباقة مطلوبة.',
            'duration.integer' => 'مدة الباقة يجب أن تكون رقمًا صحيحًا.',
            'duration.min' => 'مدة الباقة يجب أن تكون يومًا واحدًا على الأقل.',

            'duration_type.required' => 'نوع مدة الباقة مطلوب.',
            'duration_type.integer' => 'نوع مدة الباقة غير صالح.',
            'duration_type.enum' => 'نوع مدة الباقة المحدد غير صالح.',

            'features.required' => 'خصائص الباقة مطلوبة.',
            'features.array' => 'خصائص الباقة يجب أن تكون قائمة صحيحة.',

            'features.*.key.required' => 'خاصية الباقة مطلوبة.',
            'features.*.key.integer' => 'خاصية الباقة غير صالحة.',
            'features.*.key.distinct' => 'لا يمكن تكرار خاصية الباقة.',
            'features.*.key.enum' => 'خاصية الباقة المحددة غير صالحة.',

            'features.*.value.required' => 'قيمة الخاصية مطلوبة.',
            'features.*.value.numeric' => 'قيمة الخاصية يجب أن تكون رقمًا.',
            'features.*.value.min' => 'قيمة الخاصية لا يمكن أن تكون أقل من صفر.',
        ];
    }
}
