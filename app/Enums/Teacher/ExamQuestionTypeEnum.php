<?php

namespace App\Enums\Teacher;

enum ExamQuestionTypeEnum: string
{
    case TRUE_FALSE = 'true_false';
    case MULTIPLE_CHOICE = 'multiple_choice';
    case MULTIPLE_SELECT = 'multiple_select';
    case SHORT_ANSWER = 'short_answer';
    case PARAGRAPH = 'paragraph';

    public function label(): string
    {
        return match ($this) {
            self::TRUE_FALSE => 'صح أو خطأ',
            self::MULTIPLE_CHOICE => 'اختيار من متعدد',
            self::MULTIPLE_SELECT => 'اختيارات متعددة',
            self::SHORT_ANSWER => 'إجابة قصيرة',
            self::PARAGRAPH => 'مقالي',
        };
    }

    public function hasOptions(): bool
    {
        return match ($this) {
            self::TRUE_FALSE,
            self::MULTIPLE_CHOICE,
            self::MULTIPLE_SELECT => true,
            default => false,
        };
    }

    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn (self $type) => [$type->value => $type->label()])
            ->toArray();
    }
}
