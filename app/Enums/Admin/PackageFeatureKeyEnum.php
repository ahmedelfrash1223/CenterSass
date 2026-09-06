<?php

namespace App\Enums\Admin;

enum PackageFeatureKeyEnum: int
{
    case MAX_STUDENTS = 1;
    case MAX_GROUPS = 2;
    case MAX_EMPLOYEES = 3;


    public function label(): string
    {
        return match ($this) {
            self::MAX_STUDENTS => 'عدد الطلاب',
            self::MAX_GROUPS => 'عدد المجموعات',
            self::MAX_EMPLOYEES => 'عدد الموظفين',

        };
    }

    public function unit(): string
    {
        return match ($this) {
            self::MAX_STUDENTS => 'طالب',
            self::MAX_GROUPS => 'مجموعة',
            self::MAX_EMPLOYEES => 'موظف',

        };
    }

    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn (self $case) => [
                $case->value => $case->label(),
            ])
            ->toArray();
    }
}
