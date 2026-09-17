<?php

namespace App\Models;

use App\Enums\Teacher\InvoiceStatusEnum;
use App\Traits\Teacher\HasCreator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Invoice extends Model
{
    use HasCreator;

    protected $fillable = [
        'invoice_number',
        'student_id',
        'teacher_id',
        'branch_id',
        'period_start',
        'period_end',
        'period_label',
        'subtotal',
        'discount',
        'extra',
        'total',
        'paid_amount',
        'remaining_amount',
        'status',
        'breakdown',
        'notes',
        'created_by',
        'cancelled_at',
        'cancelled_by',
        'cancellation_reason',
    ];

    protected $casts = [
        'period_start' => 'date',
        'period_end' => 'date',
        'subtotal' => 'decimal:2',
        'discount' => 'decimal:2',
        'extra' => 'decimal:2',
        'total' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'remaining_amount' => 'decimal:2',
        'breakdown' => 'array',
        'cancelled_at' => 'datetime',
    ];

    // =============================================
    // 📌 RELATIONS
    // =============================================

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

    public function canceller(): BelongsTo
    {
        return $this->belongsTo(Teacher::class, 'cancelled_by');
    }

    public function items(): HasMany
    {
        return $this->hasMany(InvoiceItem::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    // =============================================
    // 📌 HELPERS
    // =============================================

    public function getStatusEnumAttribute(): ?InvoiceStatusEnum
    {
        return InvoiceStatusEnum::tryFrom($this->status);
    }

    public function getStatusLabelAttribute(): string
    {
        return $this->status_enum?->label() ?? $this->status;
    }

    public function getStatusColorAttribute(): string
    {
        return $this->status_enum?->color() ?? 'secondary';
    }

    public function getStatusIconAttribute(): string
    {
        return $this->status_enum?->icon() ?? 'ph-circle';
    }

    public function isPaid(): bool
    {
        return $this->status === InvoiceStatusEnum::PAID->value;
    }

    public function isCancelled(): bool
    {
        return $this->status === InvoiceStatusEnum::CANCELLED->value;
    }

    public function isUnpaid(): bool
    {
        return $this->status === InvoiceStatusEnum::UNPAID->value;
    }

    public function hasRemaining(): bool
    {
        return $this->remaining_amount > 0 && !$this->isCancelled();
    }

    /**
     * Recalculate totals based on payments.
     */
    public function recalculate(): void
    {
        $paid = (float) $this->payments()->sum('amount');

        $this->paid_amount = $paid;
        $this->remaining_amount = max(0, (float) $this->total - $paid);

        if ($this->cancelled_at) {
            $this->status = InvoiceStatusEnum::CANCELLED->value;
        } elseif ($paid <= 0) {
            $this->status = InvoiceStatusEnum::UNPAID->value;
        } elseif ($paid < $this->total) {
            $this->status = InvoiceStatusEnum::PARTIAL->value;
        } else {
            $this->status = InvoiceStatusEnum::PAID->value;
        }

        $this->save();
    }
}
