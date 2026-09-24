<?php

namespace App\Traits\InvoiceItem;

use App\Enums\Teacher\DiscountTypeEnum;
use App\Enums\Teacher\PricingTypeEnum;

trait HasPricingAndDiscountPresentation
{
    public function getPricingTypeLabelAttribute(): string
    {
        return PricingTypeEnum::tryFrom($this->pricing_type)?->label() ?? $this->pricing_type;
    }

    public function getDiscountTypeEnumAttribute(): ?DiscountTypeEnum
    {
        return $this->discount_type
            ? DiscountTypeEnum::tryFrom($this->discount_type)
            : null;
    }

    public function getDiscountTypeLabelAttribute(): string
    {
        return $this->discount_type_enum?->label() ?? '—';
    }

    public function getHasDiscountAttribute(): bool
    {
        return (float) $this->discount_amount > 0;
    }

    public function getHasRemainingAttribute(): bool
    {
        return (float) $this->remaining_amount > 0;
    }

    public function getNetTotalAttribute(): float
    {
        return (float) ($this->discounted_total ?: $this->total);
    }
}
