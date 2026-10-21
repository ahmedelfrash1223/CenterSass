<?php

namespace App\Http\Requests\AdminPanel;

use Illuminate\Foundation\Http\FormRequest;

class ReplySupportTicketRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'newMessage' => ['required', 'string', 'min:1', 'max:5000'],
        ];
    }

    public function messages(): array
    {
        return [
            'newMessage.required' => 'اكتب رسالتك أولاً.',
            'newMessage.string' => 'يجب أن تكون الرسالة نصاً صحيحاً.',
            'newMessage.min' => 'الرسالة قصيرة جدًا.',
            'newMessage.max' => 'الرسالة طويلة جدًا (الحد الأقصى 5000 حرف).',
        ];
    }
}
