<?php

namespace App\Models;

use App\Enums\Teacher\DiscountTypeEnum;
use App\Enums\Teacher\InvoiceStatusEnum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Invoice extends Model
{
    protected $fillable = [
        'invoice_number', 'student_id', 'teacher_id', 'branch_id',
        'period_start', 'period_end', 'period_label',
        'subtotal', 'discount_type', 'discount_value', 'discount', 'discount_reason',
        'extra', 'total',
        'paid_amount', 'remaining_amount', 'status',
        'breakdown', 'notes', 'created_by',
        'cancelled_at', 'cancelled_by', 'cancellation_reason',
    ];

    protected $casts = [
        'period_start' => 'date',
        'period_end' => 'date',
        'subtotal' => 'decimal:2',
        'discount_value' => 'decimal:2',
        'discount' => 'decimal:2',
        'extra' => 'decimal:2',
        'total' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'remaining_amount' => 'decimal:2',
        'breakdown' => 'array',
        'cancelled_at' => 'datetime',
    ];

    // ============ Relations ============
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(Teacher::class, 'teacher_id');
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(InvoiceItem::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(Teacher::class, 'created_by');
    }

    // ============ Accessors ============
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
        return (float)$this->discount > 0;
    }

    public function getHasRemainingAttribute(): bool
    {
        return (float)$this->remaining_amount > 0;
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

    // ============ Methods ============
    public function recalculateTotals(): void
    {
        $subtotal = (float)$this->subtotal;
        $discountValue = (float)$this->discount_value;
        $discountType = DiscountTypeEnum::tryFrom($this->discount_type);

        $discountAmount = match ($discountType) {
            DiscountTypeEnum::PERCENTAGE => round($subtotal * ($discountValue / 100), 2),
            DiscountTypeEnum::FIXED => round($discountValue, 2),
            default => 0,
        };

        $discountAmount = min($discountAmount, $subtotal);
        $total = max(0, $subtotal - $discountAmount + (float)$this->extra);

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

        $subtotal = (float)$this->items()->sum('total');

        $discount = (float)$this->items()->sum('discount_amount');

        $total = max(0, $subtotal - $discount + (float)$this->extra);

        $rawPaid = (float)$this->payments()->sum('amount');

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
