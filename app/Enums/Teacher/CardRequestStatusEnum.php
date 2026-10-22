<?php

namespace App\Enums\Teacher;

enum CardRequestStatusEnum: string
{
    case PENDING = 'pending';
    case APPROVED = 'approved';
    case IN_PROGRESS = 'in_progress';
    case COMPLETED = 'completed';
    case SHIPPED = 'shipped';
    case DELIVERED = 'delivered';
    case REJECTED = 'rejected';
    case CANCELLED = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::PENDING => 'في انتظار المراجعة',
            self::APPROVED => 'تم القبول',
            self::IN_PROGRESS => 'جاري العمل',
            self::COMPLETED => 'تم الانتهاء',
            self::SHIPPED => 'تم الشحن',
            self::DELIVERED => 'اكتمل',
            self::REJECTED => 'مرفوض',
            self::CANCELLED => 'ملغي',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::PENDING => 'warning',
            self::APPROVED => 'info',
            self::IN_PROGRESS => 'primary',
            self::COMPLETED => 'success',
            self::SHIPPED => 'info',
            self::DELIVERED => 'success',
            self::REJECTED => 'danger',
            self::CANCELLED => 'secondary',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::PENDING => 'ph-hourglass',
            self::APPROVED => 'ph-check-circle',
            self::IN_PROGRESS => 'ph-spinner',
            self::COMPLETED => 'ph-check-square',
            self::SHIPPED => 'ph-truck',
            self::DELIVERED => 'ph-package',
            self::REJECTED => 'ph-x-circle',
            self::CANCELLED => 'ph-x-circle',
        };
    }

    public static function teacherVisible(): array
    {
        return [
            self::PENDING,
            self::APPROVED,
            self::IN_PROGRESS,
            self::COMPLETED,
            self::SHIPPED,
            self::DELIVERED,
            self::REJECTED,
        ];
    }

    public function isApproved(): bool
    {
        return $this === self::APPROVED;
    }

    public function canTransitionToInProgress(): bool
    {
        return $this === self::APPROVED;
    }

    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn (self $s) => [$s->value => $s->label()])
            ->toArray();
    }
}
