<?php

namespace App\Enums\Student;

enum OtpPurposeEnum: string
{
    case REGISTER = 'register';
    case LOGIN = 'login';
    case RESET_PASSWORD = 'reset_password';
    case VERIFY_PHONE = 'verify_phone';
    case VERIFY_EMAIL = 'verify_email';

    public function label(): string
    {
        return match ($this) {
            self::REGISTER => 'تفعيل الحساب',
            self::LOGIN => 'تسجيل الدخول',
            self::RESET_PASSWORD => 'استعادة كلمة المرور',
            self::VERIFY_PHONE => 'تفعيل رقم الهاتف',
            self::VERIFY_EMAIL => 'تفعيل البريد الإلكتروني',
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
