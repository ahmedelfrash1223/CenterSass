<?php

namespace App\Enums\Student;

enum AuthProviderEnum: string
{
    case EMAIL = 'email';
    case GOOGLE = 'google';

    public function label(): string
    {
        return match ($this) {
            self::EMAIL => 'البريد الإلكتروني',
            self::GOOGLE => 'جوجل',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::EMAIL => 'ph-envelope',
            self::GOOGLE => 'ph-google-logo',
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
