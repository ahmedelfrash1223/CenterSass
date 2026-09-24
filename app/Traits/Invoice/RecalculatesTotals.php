<?php

namespace App\Traits\Invoice;

use App\Enums\Teacher\DiscountTypeEnum;
use App\Enums\Teacher\InvoiceStatusEnum;

trait RecalculatesTotals
{
    public function recalculateTotals(): void
    {
        $subtotal = (float) $this->subtotal;
        $discountValue = (float) $this->discount_value;
        $discountType = DiscountTypeEnum::tryFrom($this->discount_type);

        $discountAmount = match ($discountType) {
            DiscountTypeEnum::PERCENTAGE => round($subtotal * ($discountValue / 100), 2),
            DiscountTypeEnum::FIXED => round($discountValue, 2),
            default => 0,
        };

        $discountAmount = min($discountAmount, $subtotal);
        $total = max(0, $subtotal - $discountAmount + (float) $this->extra);

        $this->update([
            'discount' => $discountAmount,
            'total' => $total,
        ]);
    }

    public function recalculate(): void
    {
        foreach ($this->items()->get() as $item) {
            $item->recalculate();
        }

        $subtotal = (float) $this->items()->sum('total');
        $discount = (float) $this->items()->sum('discount_amount');
        $total = max(0, $subtotal - $discount + (float) $this->extra);
        $rawPaid = (float) $this->payments()->sum('amount');
        $paid = min($rawPaid, $total);
        $remaining = max(0, $total - $paid);

        $status = match (true) {
            $remaining <= 0 && $total > 0 => InvoiceStatusEnum::PAID->value,
            $paid > 0 => InvoiceStatusEnum::PARTIAL->value,
            default => InvoiceStatusEnum::UNPAID->value,
        };

        $this->update([
            'subtotal' => $subtotal,
            'discount' => $discount,
            'total' => $total,
            'paid_amount' => $paid,
            'remaining_amount' => $remaining,
            'status' => $status,
        ]);
    }
}
