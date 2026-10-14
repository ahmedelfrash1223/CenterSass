<?php

namespace App\Enums\Teacher;

enum TeacherPayrollStatusEnum: string
{
    case PENDING = 'pending';
    case PARTIAL = 'partial';
    case PAID = 'paid';
    case HOLD = 'hold';

    public function label(): string
    {
        return match ($this) {
            self::PENDING => 'لم يُحاسب',
            self::PARTIAL => 'مدفوع جزئي',
            self::PAID => 'تم الدفع',
            self::HOLD => 'معلّق',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::PENDING => 'warning',
            self::PARTIAL => 'info',
            self::PAID => 'success',
            self::HOLD => 'secondary',
        };
    }

    public function textColor(): string
    {
        return in_array($this->color(), ['warning', 'info']) ? 'text-dark' : 'text-white';
    }

    public function icon(): string
    {
        return match ($this) {
            self::PENDING => 'ph-hourglass',
            self::PARTIAL => 'ph-clock',
            self::PAID => 'ph-check-circle',
            self::HOLD => 'ph-pause-circle',
        };
    }

    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn (self $s) => [$s->value => $s->label()])
            ->toArray();
    }
}
