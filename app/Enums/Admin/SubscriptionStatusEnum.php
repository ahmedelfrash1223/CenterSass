<?php

namespace App\Enums\Admin;

enum SubscriptionStatusEnum: int
{
    case ACTIVE = 1;
    case EXPIRED = 2;
    case CANCELLED = 3;
    case PENDING = 4;

    public function label(): string
    {
        return match ($this) {
            self::ACTIVE => 'نشط',
            self::EXPIRED => 'منتهي',
            self::CANCELLED => 'ملغي',
            self::PENDING => 'قيد الانتظار',
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
