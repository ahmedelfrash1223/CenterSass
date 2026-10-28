<?php

namespace App\Http\Requests\Api\Student\Invoice;

use App\Enums\Teacher\InvoiceStatusEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ListInvoiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth('student_account')->check();
    }

    public function rules(): array
    {
        return [
            'status' => ['nullable', Rule::enum(InvoiceStatusEnum::class)],
            'month' => ['nullable', 'date_format:Y-m'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
            'page' => ['nullable', 'integer', 'min:1'],
        ];
    }

    public function messages(): array
    {
        return [
            'status.enum' => 'حالة الفاتورة غير صالحة.',
            'month.date_format' => 'صيغة الشهر يجب أن تكون Y-m.',
        ];
    }
}
