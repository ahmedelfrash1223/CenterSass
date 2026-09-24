<?php

namespace App\Traits\Group;

use App\Enums\Teacher\PricingTypeEnum;

trait HasPricingPresentation
{
    public function getPricingTypeEnumAttribute(): ?PricingTypeEnum
    {
        return PricingTypeEnum::tryFrom($this->pricing_type);
    }

    public function getPricingTypeLabelAttribute(): string
    {
        return $this->pricing_type_enum?->label() ?? $this->pricing_type;
    }

    public function getFormattedPriceAttribute(): string
    {
        if (! $this->has_pricing || $this->pricing_amount <= 0) {
            return 'مجاني';
        }

        return number_format((float) $this->pricing_amount, 2).' ج.م';
    }
}
