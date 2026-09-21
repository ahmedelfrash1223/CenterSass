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
            self::PER_SESSION   => 'بالحصة',
            self::PER_SUBJECT   => 'بالمادة',
            self::PER_GRADE     => 'بالصف',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::FIXED_MONTHLY => 'ph-calendar-check',
            self::PER_SESSION   => 'ph-clock',
            self::PER_SUBJECT   => 'ph-books',
            self::PER_GRADE     => 'ph-graduation-cap',
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::FIXED_MONTHLY => 'مبلغ ثابت كل شهر مهما حضر الطالب.',
            self::PER_SESSION   => 'المبلغ مضروب في عدد الحصص اللي الطالب حضرها.',
            self::PER_SUBJECT   => 'المبلغ مضروب في عدد المواد اللي الطالب مشترك فيها.',
            self::PER_GRADE     => 'مبلغ ثابت حسب المرحلة الدراسية للطالب.',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::FIXED_MONTHLY => 'primary',
            self::PER_SESSION   => 'info',
            self::PER_SUBJECT   => 'success',
            self::PER_GRADE     => 'warning',
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
