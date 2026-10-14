<?php

namespace App\Enums\Teacher;

enum TeacherPaymentTypeEnum: string
{
    case MONTHLY = 'monthly';
    case PER_SESSION = 'per_session';
    case MONTHLY_PLUS_PER_SESSION = 'monthly_plus_per_session';
    case HOURLY = 'hourly';

    public function label(): string
    {
        return match ($this) {
            self::MONTHLY => 'شهري ثابت',
            self::PER_SESSION => 'بالحصة',
            self::MONTHLY_PLUS_PER_SESSION => 'شهري + بالحصة',
            self::HOURLY => 'بالساعة',
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::MONTHLY => 'مبلغ ثابت كل شهر',
            self::PER_SESSION => 'مبلغ لكل حصة',
            self::MONTHLY_PLUS_PER_SESSION => 'مبلغ ثابت + مبلغ لكل حصة',
            self::HOURLY => 'مبلغ لكل ساعة',
        };
    }

    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn (self $s) => [$s->value => $s->label()])
            ->toArray();
    }
}
