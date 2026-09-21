<?php

namespace App\Services\Teacher;

use App\Enums\Teacher\DiscountTypeEnum;
use App\Models\Invoice;
use App\Models\Teacher;

class DiscountService
{

    public function apply(
        Invoice $invoice,
        string $discountType,
        float $discountValue,
        ?string $reason,
        Teacher $user
    ): bool {
        if ($invoice->teacher_id !== $user->getOwnerId()) {
            return false;
        }

        if ($invoice->isCancelled()) {
            return false;
        }

        if ((float) $invoice->paid_amount > 0) {
            return false;
        }

        $type = DiscountTypeEnum::tryFrom($discountType);
        if (!$type) {
            return false;
        }

        if ($discountValue < 0) {
            return false;
        }

        if ($type === DiscountTypeEnum::PERCENTAGE && $discountValue > 100) {
            return false;
        }

        $invoice->update([
            'discount_type'   => $type->value,
            'discount_value'  => $discountValue,
            'discount_reason' => $reason ?: null,
        ]);

        $invoice->refresh();
        $invoice->recalculate();

        return true;
    }


    public function remove(Invoice $invoice, Teacher $user): bool
    {
        if ($invoice->teacher_id !== $user->getOwnerId()) {
            return false;
        }

        if ($invoice->isCancelled()) {
            return false;
        }

        if ((float) $invoice->paid_amount > 0) {
            return false;
        }

        $invoice->update([
            'discount_type'   => DiscountTypeEnum::FIXED->value,
            'discount_value'  => 0,
            'discount_reason' => null,
        ]);

        $invoice->refresh();
        $invoice->recalculate();

        return true;
    }
}
