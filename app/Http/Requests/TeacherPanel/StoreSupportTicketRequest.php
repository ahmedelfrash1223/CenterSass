<?php

namespace App\Http\Requests\TeacherPanel;

use App\Enums\Teacher\SupportTicketCategoryEnum;
use App\Enums\Teacher\SupportTicketPriorityEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreSupportTicketRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'subject' => ['required', 'string', 'max:255'],
            'category' => ['required', Rule::enum(SupportTicketCategoryEnum::class)],
            'priority' => ['required', Rule::enum(SupportTicketPriorityEnum::class)],
            'message' => ['required', 'string', 'min:10', 'max:5000'],
        ];
    }

    public function messages(): array
    {
        return [
            'subject.required' => 'عنوان التذكرة مطلوب.',
            'subject.max' => 'عنوان التذكرة طويل جدًا.',
            'category.required' => 'التصنيف مطلوب.',
            'category.enum' => 'التصنيف غير صالح.',
            'priority.required' => 'الأولوية مطلوبة.',
            'priority.enum' => 'الأولوية غير صالحة.',
            'message.required' => 'نص الرسالة مطلوب.',
            'message.min' => 'الرسالة لازم تكون 10 أحرف على الأقل.',
            'message.max' => 'الرسالة طويلة جدًا (الحد الأقصى 5000 حرف).',
        ];
    }
}
