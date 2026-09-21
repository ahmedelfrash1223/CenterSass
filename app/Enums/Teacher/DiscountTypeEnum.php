<?php

namespace App\Enums\Teacher;

enum DiscountTypeEnum: string
{
    case FIXED      = 'fixed';
    case PERCENTAGE = 'percentage';

    public function label(): string
    {
        return match ($this) {
            self::FIXED      => 'مبلغ ثابت',
            self::PERCENTAGE => 'نسبة مئوية',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::FIXED      => 'ph-currency-circle-dollar',
            self::PERCENTAGE => 'ph-percent',
        };
    }

    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn(self $c) => [$c->value => $c->label()])
            ->toArray();
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
