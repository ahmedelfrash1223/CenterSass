<?php

namespace App\Models;

use App\Enums\Teacher\DiscountTypeEnum;
use App\Enums\Teacher\PricingTypeEnum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class InvoiceItem extends Model
{
    protected $fillable = [
        'invoice_id', 'group_id', 'description', 'pricing_type',
        'quantity', 'unit_price', 'total',
        'discount_type', 'discount_value', 'discount_amount',
        'discount_reason', 'discounted_total',
        'paid_amount', 'remaining_amount',
        'details',
    ];

    protected $casts = [
        'quantity'         => 'decimal:2',
        'unit_price'       => 'decimal:2',
        'total'            => 'decimal:2',
        'discount_value'   => 'decimal:2',
        'discount_amount'  => 'decimal:2',
        'discounted_total' => 'decimal:2',
        'paid_amount'      => 'decimal:2',
        'remaining_amount' => 'decimal:2',
        'details'          => 'array',
    ];

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class);
    }

    public function allocations(): HasMany
    {
        return $this->hasMany(PaymentAllocation::class);
    }

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


    public function recalculateDiscount(): float
    {
        $total = (float) $this->total;
        $value = (float) $this->discount_value;
        $type = DiscountTypeEnum::tryFrom($this->discount_type);

        $discount = match ($type) {
            DiscountTypeEnum::PERCENTAGE => round($total * ($value / 100), 2),
            DiscountTypeEnum::FIXED      => round($value, 2),
            default                      => 0,
        };

        $discount = min($discount, $total);

        $this->update([
            'discount_amount'  => $discount,
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
            'paid_amount'      => $paid,
            'remaining_amount' => $remaining,
        ]);
    }
}
