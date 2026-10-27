<?php

namespace App\Enums\Student;

enum OtpChannelEnum: string
{
    case EMAIL = 'email';
    case SMS = 'sms';

    public function label(): string
    {
        return match ($this) {
            self::EMAIL => 'البريد الإلكتروني',
            self::SMS => 'رسالة نصية',
        };
    }

    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn (self $case) => [$case->value => $case->label()])
            ->toArray();
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
