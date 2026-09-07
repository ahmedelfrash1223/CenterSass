<?php

namespace App\Enums\Admin;

enum PaymentStatusEnum: int
{
    case PAID = 1;
    case UNPAID = 0;
    case PENDING = 2;

    public function label(): string
    {
        return match ($this) {
            self::PAID => 'مدفوع',
            self::UNPAID => 'غير مدفوع',
            self::PENDING => 'قيد الانتظار',
        };
    }

    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn (self $case) => [$case->value => $case->label()])
            ->toArray();
    }
}