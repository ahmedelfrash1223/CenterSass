<?php

namespace App\Enums\Teacher;

enum PaymentMethodEnum: string
{
    case CASH          = 'cash';
    case VODAFONE_CASH = 'vodafone_cash';
    case INSTAPAY      = 'instapay';
    case BANK_TRANSFER = 'bank_transfer';
    case CHEQUE        = 'cheque';

    public function label(): string
    {
        return match ($this) {
            self::CASH          => 'نقدي',
            self::VODAFONE_CASH => 'فودافون كاش',
            self::INSTAPAY      => 'إنستاباي',
            self::BANK_TRANSFER => 'تحويل بنكي',
            self::CHEQUE        => 'شيك',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::CASH          => 'ph-money',
            self::VODAFONE_CASH => 'ph-device-mobile',
            self::INSTAPAY      => 'ph-lightning',
            self::BANK_TRANSFER => 'ph-bank',
            self::CHEQUE        => 'ph-note',
        };
    }

    public function requiresReference(): bool
    {
        return in_array($this, [
            self::VODAFONE_CASH,
            self::INSTAPAY,
            self::BANK_TRANSFER,
            self::CHEQUE,
        ]);
    }

    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn($c) => [$c->value => $c->label()])
            ->toArray();
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
