<?php

namespace App\Enums\Admin;

enum SupportTicketPriorityEnum: string
{
    case LOW = 'low';
    case NORMAL = 'normal';
    case HIGH = 'high';
    case URGENT = 'urgent';

    public function label(): string
    {
        return match ($this) {
            self::LOW => 'منخفضة',
            self::NORMAL => 'عادية',
            self::HIGH => 'عالية',
            self::URGENT => 'عاجلة',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::LOW => 'secondary',
            self::NORMAL => 'info',
            self::HIGH => 'warning',
            self::URGENT => 'danger',
        };
    }

    public function textColor(): string
    {
        return in_array($this->color(), ['warning', 'info'], true) ? 'text-dark' : 'text-white';
    }

    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn (self $priority) => [$priority->value => $priority->label()])
            ->toArray();
    }
}
