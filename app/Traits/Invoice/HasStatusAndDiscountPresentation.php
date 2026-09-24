<?php

namespace App\Traits\Invoice;

use App\Enums\Teacher\DiscountTypeEnum;
use App\Enums\Teacher\InvoiceStatusEnum;

trait HasStatusAndDiscountPresentation
{
    public function getStatusEnumAttribute(): InvoiceStatusEnum
    {
        return InvoiceStatusEnum::tryFrom($this->status) ?? InvoiceStatusEnum::UNPAID;
    }

    public function getStatusLabelAttribute(): string
    {
        return $this->status_enum->label();
    }

    public function getStatusColorAttribute(): string
    {
        return $this->status_enum->color();
    }

    public function getStatusIconAttribute(): string
    {
        return $this->status_enum->icon();
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
        return (float) $this->discount > 0;
    }

    public function getHasRemainingAttribute(): bool
    {
        return (float) $this->remaining_amount > 0;
    }

    public function getIsPaidAttribute(): bool
    {
        return $this->status === InvoiceStatusEnum::PAID->value;
    }

    public function getIsCancelledAttribute(): bool
    {
        return $this->status === InvoiceStatusEnum::CANCELLED->value;
    }

    public function isPaid(): bool
    {
        return $this->is_paid;
    }

    public function isCancelled(): bool
    {
        return $this->is_cancelled;
    }
}
