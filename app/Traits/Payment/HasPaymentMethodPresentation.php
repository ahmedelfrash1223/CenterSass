<?php

namespace App\Traits\Payment;

use App\Enums\Teacher\PaymentMethodEnum;

trait HasPaymentMethodPresentation
{
    public function getPaymentMethodLabelAttribute(): string
    {
        return PaymentMethodEnum::tryFrom($this->payment_method)?->label() ?? $this->payment_method;
    }

    public function getPaymentMethodIconAttribute(): string
    {
        return PaymentMethodEnum::tryFrom($this->payment_method)?->icon() ?? 'ph-money';
    }
}
