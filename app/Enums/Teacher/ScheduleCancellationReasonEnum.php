<?php

namespace App\Enums\Teacher;

enum ScheduleCancellationReasonEnum: string
{
    case TEACHER_ABSENT = 'teacher_absent';
    case TEACHER_EMERGENCY = 'teacher_emergency';
    case BRANCH_EMERGENCY = 'branch_emergency';
    case BAD_WEATHER = 'bad_weather';
    case OFFICIAL_HOLIDAY = 'official_holiday';
    case TECHNICAL_ISSUE = 'technical_issue';
    case OTHER = 'other';

    public function label(): string
    {
        return match ($this) {
            self::TEACHER_ABSENT => 'غياب المدرس',
            self::TEACHER_EMERGENCY => 'ظرف طارئ للمدرس',
            self::BRANCH_EMERGENCY => 'ظرف طارئ بالفرع',
            self::BAD_WEATHER => 'سوء الأحوال الجوية',
            self::OFFICIAL_HOLIDAY => 'إجازة رسمية',
            self::TECHNICAL_ISSUE => 'عطل أو مشكلة فنية',
            self::OTHER => 'سبب آخر',
        };
    }

    public static function options(): array
    {
        $options = [];

        foreach (self::cases() as $reason) {
            $options[$reason->value] = $reason->label();
        }

        return $options;
    }
}
