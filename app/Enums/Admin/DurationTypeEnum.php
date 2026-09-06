<?php

namespace App\Enums\Admin;

enum DurationTypeEnum: int
{
    case DAY = 1;
    case WEEK = 2;
    case MONTH = 3;
    case YEAR = 4;

    public function label(): string
    {
        return match ($this) {
            self::DAY => 'يوم',
            self::WEEK => 'أسبوع',
            self::MONTH => 'شهر',
            self::YEAR => 'سنة',
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
