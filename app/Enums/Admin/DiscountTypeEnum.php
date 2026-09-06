<?php

namespace App\Enums\Admin;

enum DiscountTypeEnum: int
{
    case PERCENTAGE = 1;
    case VALUE = 2;

    public function label(): string
    {
        return match ($this) {
            self::PERCENTAGE => 'نسبة مئوية',
            self::VALUE => 'قيمة ثابتة',
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
