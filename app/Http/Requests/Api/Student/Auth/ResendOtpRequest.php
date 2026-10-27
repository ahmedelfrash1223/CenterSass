<?php

namespace App\Http\Requests\Api\Student\Auth;

use App\Enums\Student\OtpPurposeEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ResendOtpRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'identifier' => ['required', 'string'],
            'purpose' => ['required', Rule::enum(OtpPurposeEnum::class)],
        ];
    }

    public function messages(): array
    {
        return [
            'identifier.required' => 'البريد الإلكتروني أو رقم الهاتف مطلوب.',
            'purpose.required' => 'الغرض من الكود مطلوب.',
        ];
    }
}
