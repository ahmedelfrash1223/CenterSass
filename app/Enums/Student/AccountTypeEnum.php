<?php

namespace App\Enums\Student;

enum AccountTypeEnum: string
{
    case STUDENT = 'student';
    case PARENT = 'parent';

    public function label(): string
    {
        return match ($this) {
            self::STUDENT => 'طالب',
            self::PARENT => 'ولي أمر',
        };
    }

    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn (self $case) => [$case->value => $case->label()])
            ->toArray();
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
