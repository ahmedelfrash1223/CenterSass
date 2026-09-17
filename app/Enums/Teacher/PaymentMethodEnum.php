<?php

namespace App\Enums\Teacher;

enum PaymentMethodEnum: string
{
    case CASH          = 'cash';
    case VODAFONE_CASH = 'vodafone_cash';
    case INSTAPAY      = 'instapay';
    case BANK_TRANSFER = 'bank_transfer';
    case CHEQUE        = 'cheque';
    case OTHER         = 'other';

    public function label(): string
    {
        return match ($this) {
            self::CASH          => 'كاش',
            self::VODAFONE_CASH => 'فودافون كاش',
            self::INSTAPAY      => 'انستاباي',
            self::BANK_TRANSFER => 'تحويل بنكي',
            self::CHEQUE        => 'شيك',
            self::OTHER         => 'أخرى',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::CASH          => 'ph-money',
            self::VODAFONE_CASH => 'ph-device-mobile',
            self::INSTAPAY      => 'ph-lightning',
            self::BANK_TRANSFER => 'ph-bank',
            self::CHEQUE        => 'ph-receipt',
            self::OTHER         => 'ph-dots-three-circle',
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
