<?php

namespace App\Enums\Teacher;

enum ExpenseSourceEnum: string
{
    case TEACHER_SALARY = 'teacher_salary';
    case TEACHER_BONUS = 'teacher_bonus';
    case BRANCH_RENT = 'branch_rent';
    case UTILITIES = 'utilities';
    case SUPPLIES = 'supplies';
    case MAINTENANCE = 'maintenance';
    case MARKETING = 'marketing';
    case OTHER = 'other';

    public function label(): string
    {
        return match ($this) {
            self::TEACHER_SALARY => 'راتب مدرس',
            self::TEACHER_BONUS => 'بونص مدرس',
            self::BRANCH_RENT => 'إيجار فرع',
            self::UTILITIES => 'مرافق',
            self::SUPPLIES => 'مستلزمات',
            self::MAINTENANCE => 'صيانة',
            self::MARKETING => 'تسويق',
            self::OTHER => 'أخرى',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::TEACHER_SALARY => 'primary',
            self::TEACHER_BONUS => 'success',
            self::BRANCH_RENT => 'danger',
            self::UTILITIES => 'info',
            self::SUPPLIES => 'secondary',
            self::MAINTENANCE => 'warning',
            self::MARKETING => 'info',
            self::OTHER => 'secondary',
        };
    }

    public function textColor(): string
    {
        return in_array($this->color(), ['warning', 'info']) ? 'text-dark' : 'text-white';
    }

    public function icon(): string
    {
        return match ($this) {
            self::TEACHER_SALARY => 'ph-user-circle',
            self::TEACHER_BONUS => 'ph-gift',
            self::BRANCH_RENT => 'ph-buildings',
            self::UTILITIES => 'ph-lightning',
            self::SUPPLIES => 'ph-package',
            self::MAINTENANCE => 'ph-wrench',
            self::MARKETING => 'ph-megaphone',
            self::OTHER => 'ph-dots-three-circle',
        };
    }

    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn (self $s) => [$s->value => $s->label()])
            ->toArray();
    }
}
