<?php

namespace App\Models;

use App\Traits\InvoiceItem\HasPricingAndDiscountPresentation;
use App\Traits\InvoiceItem\RecalculatesAmounts;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class InvoiceItem extends Model
{
    use HasPricingAndDiscountPresentation, RecalculatesAmounts;

    protected $fillable = [
        'invoice_id', 'group_id', 'description', 'pricing_type',
        'quantity', 'unit_price', 'total',
        'discount_type', 'discount_value', 'discount_amount',
        'discount_reason', 'discounted_total',
        'paid_amount', 'remaining_amount',
        'details',
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'unit_price' => 'decimal:2',
        'total' => 'decimal:2',
        'discount_value' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'discounted_total' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'remaining_amount' => 'decimal:2',
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

    public function allocations(): HasMany
    {
        return $this->hasMany(PaymentAllocation::class);
    }
}
