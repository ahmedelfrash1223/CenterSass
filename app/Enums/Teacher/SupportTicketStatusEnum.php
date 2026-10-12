<?php

namespace App\Enums\Teacher;

enum SupportTicketStatusEnum: string
{
    case OPEN = 'open';
    case IN_PROGRESS = 'in_progress';
    case WAITING = 'waiting';
    case RESOLVED = 'resolved';
    case CLOSED = 'closed';

    public function label(): string
    {
        return match ($this) {
            self::OPEN => 'مفتوحة',
            self::IN_PROGRESS => 'جاري المعالجة',
            self::WAITING => 'في انتظار ردك',
            self::RESOLVED => 'تم الحل',
            self::CLOSED => 'مغلقة',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::OPEN => 'primary',
            self::IN_PROGRESS => 'info',
            self::WAITING => 'warning',
            self::RESOLVED => 'success',
            self::CLOSED => 'secondary',
        };
    }

    public function textColor(): string
    {
        return in_array($this->color(), ['warning', 'info']) ? 'text-dark' : 'text-white';
    }

    public function icon(): string
    {
        return match ($this) {
            self::OPEN => 'ph-ticket',
            self::IN_PROGRESS => 'ph-spinner',
            self::WAITING => 'ph-hourglass',
            self::RESOLVED => 'ph-check-circle',
            self::CLOSED => 'ph-lock',
        };
    }

    public function isActive(): bool
    {
        return in_array($this, [self::OPEN, self::IN_PROGRESS, self::WAITING]);
    }

    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn (self $s) => [$s->value => $s->label()])
            ->toArray();
    }
}
