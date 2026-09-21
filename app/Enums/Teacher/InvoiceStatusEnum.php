<?php

namespace App\Enums\Teacher;

enum InvoiceStatusEnum: string
{
    case UNPAID    = 'unpaid';
    case PARTIAL   = 'partial';
    case PAID      = 'paid';
    case CANCELLED = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::UNPAID    => 'مستحق',
            self::PARTIAL   => 'مدفوع جزئياً',
            self::PAID      => 'مدفوع',
            self::CANCELLED => 'ملغي',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::UNPAID    => 'danger',
            self::PARTIAL   => 'warning',
            self::PAID      => 'success',
            self::CANCELLED => 'secondary',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::UNPAID    => 'ph-warning-circle',
            self::PARTIAL   => 'ph-clock',
            self::PAID      => 'ph-check-circle',
            self::CANCELLED => 'ph-x-circle',
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
