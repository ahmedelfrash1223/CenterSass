<?php

namespace App\Enums\Admin;

enum SupportTicketStatusEnum: string
{
    case OPEN = 'open';
    case IN_PROGRESS = 'in_progress';
    case WAITING = 'waiting';
    case CLOSED = 'closed';

    public function label(): string
    {
        return match ($this) {
            self::OPEN => 'مفتوحة',
            self::IN_PROGRESS => 'جاري المعالجة',
            self::WAITING => 'بانتظار الرد',
            self::CLOSED => 'مغلقة',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::OPEN => 'primary',
            self::IN_PROGRESS => 'info',
            self::WAITING => 'warning',
            self::CLOSED => 'secondary',
        };
    }

    public function textColor(): string
    {
        return in_array($this->color(), ['warning', 'info'], true) ? 'text-dark' : 'text-white';
    }

    public function icon(): string
    {
        return match ($this) {
            self::OPEN => 'ph-ticket',
            self::IN_PROGRESS => 'ph-spinner',
            self::WAITING => 'ph-hourglass',
            self::CLOSED => 'ph-lock',
        };
    }

    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn (self $status) => [$status->value => $status->label()])
            ->toArray();
    }
}
