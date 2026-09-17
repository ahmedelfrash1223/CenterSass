<?php

namespace App\Enums\Teacher;

enum PricingTypeEnum: string
{
    case FIXED_MONTHLY = 'fixed_monthly';
    case PER_SESSION   = 'per_session';
    case PER_SUBJECT   = 'per_subject';
    case PER_GRADE     = 'per_grade';

    public function label(): string
    {
        return match ($this) {
            self::FIXED_MONTHLY => 'شهري ثابت',
            self::PER_SESSION   => 'لكل حصة',
            self::PER_SUBJECT   => 'لكل مادة',
            self::PER_GRADE     => 'لكل صف',
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::FIXED_MONTHLY => 'مبلغ ثابت يدفعه الطالب شهرياً',
            self::PER_SESSION   => 'مبلغ مضروب في عدد حصص الشهر',
            self::PER_SUBJECT   => 'مبلغ مضروب في عدد المواد المسجل فيها',
            self::PER_GRADE     => 'مبلغ ثابت حسب المرحلة الدراسية',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::FIXED_MONTHLY => 'ph-calendar',
            self::PER_SESSION   => 'ph-clock',
            self::PER_SUBJECT   => 'ph-notebook',
            self::PER_GRADE     => 'ph-graduation-cap',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::FIXED_MONTHLY => 'primary',
            self::PER_SESSION   => 'success',
            self::PER_SUBJECT   => 'info',
            self::PER_GRADE     => 'warning',
        };
    }

    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn (self $type) => [$type->value => $type->label()])
            ->toArray();
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
