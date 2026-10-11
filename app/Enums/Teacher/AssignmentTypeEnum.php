<?php

namespace App\Enums\Teacher;

enum AssignmentTypeEnum: string
{
    case ORAL_RECITATION = 'oral_recitation';
    case WRITTEN_RECITATION = 'written_recitation';
    case HOMEWORK = 'homework';
    case QUIZ = 'quiz';
    case PROJECT = 'project';
    case OTHER = 'other';

    public function label(): string
    {
        return match ($this) {
            self::ORAL_RECITATION => 'تسميع شفوي',
            self::WRITTEN_RECITATION => 'تسميع تحريري',
            self::HOMEWORK => 'واجب',
            self::QUIZ => 'اختبار قصير',
            self::PROJECT => 'مشروع',
            self::OTHER => 'أخرى',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::ORAL_RECITATION => 'info',
            self::WRITTEN_RECITATION => 'primary',
            self::HOMEWORK => 'warning',
            self::QUIZ => 'danger',
            self::PROJECT => 'success',
            self::OTHER => 'secondary',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::ORAL_RECITATION => 'ph-microphone',
            self::WRITTEN_RECITATION => 'ph-pencil-line',
            self::HOMEWORK => 'ph-notebook',
            self::QUIZ => 'ph-exam',
            self::PROJECT => 'ph-briefcase',
            self::OTHER => 'ph-dots-three-circle',
        };
    }

    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn (self $case) => [$case->value => $case->label()])
            ->toArray();
    }
}
