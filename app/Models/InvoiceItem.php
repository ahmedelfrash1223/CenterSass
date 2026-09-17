<?php

namespace App\Models;

use App\Enums\Teacher\PricingTypeEnum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InvoiceItem extends Model
{
    protected $fillable = [
        'invoice_id',
        'group_id',
        'description',
        'pricing_type',
        'quantity',
        'unit_price',
        'total',
        'details',
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'unit_price' => 'decimal:2',
        'total' => 'decimal:2',
        'details' => 'array',
    ];

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class);
    }

    public function getPricingTypeEnumAttribute(): ?PricingTypeEnum
    {
        return PricingTypeEnum::tryFrom($this->pricing_type);
    }

    public function getPricingTypeLabelAttribute(): string
    {
        return $this->pricing_type_enum?->label() ?? $this->pricing_type;
    }
}
