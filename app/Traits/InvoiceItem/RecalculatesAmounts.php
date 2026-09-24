<?php

namespace App\Traits\InvoiceItem;

use App\Enums\Teacher\DiscountTypeEnum;

trait RecalculatesAmounts
{
    public function recalculateDiscount(): float
    {
        $total = (float) $this->total;
        $value = (float) $this->discount_value;
        $type = DiscountTypeEnum::tryFrom($this->discount_type);

        $discount = match ($type) {
            DiscountTypeEnum::PERCENTAGE => round($total * ($value / 100), 2),
            DiscountTypeEnum::FIXED => round($value, 2),
            default => 0,
        };

        $discount = min($discount, $total);

        $this->update([
            'discount_amount' => $discount,
            'discounted_total' => max(0, $total - $discount),
        ]);

        return $discount;
    }

    public function recalculate(): void
    {
        $rawPaid = (float) $this->allocations()->sum('amount');
        $net = $this->net_total;
        $paid = min($rawPaid, $net);
        $remaining = max(0, $net - $paid);

        $this->update([
            'paid_amount' => $paid,
            'remaining_amount' => $remaining,
        ]);
    }
}
