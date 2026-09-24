<?php

namespace App\Livewire\TeacherPanel\Concerns\Payment;

use App\Enums\Teacher\PaymentMethodEnum;

trait LoadsPaymentFormData
{
    /**
     * Payment methods for select inputs.
     */
    protected function loadPaymentMethods(): array
    {
        return PaymentMethodEnum::options();
    }
}
