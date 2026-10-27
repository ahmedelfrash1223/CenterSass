<?php

namespace App\Http\Requests\Api\Student\Auth;

use App\Enums\Student\OtpPurposeEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class VerifyOtpRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'identifier' => ['required', 'string'],
            'code' => ['required', 'string', 'digits:6'],
            'purpose' => ['required', Rule::enum(OtpPurposeEnum::class)],
            'device_name' => ['nullable', 'string', 'max:100'],
        ];
    }

    public function messages(): array
    {
        return [
            'identifier.required' => 'البريد الإلكتروني أو رقم الهاتف مطلوب.',
            'code.required' => 'كود التحقق مطلوب.',
            'code.digits' => 'كود التحقق يجب أن يكون 6 أرقام.',
            'purpose.required' => 'الغرض من الكود مطلوب.',
        ];
    }
}
