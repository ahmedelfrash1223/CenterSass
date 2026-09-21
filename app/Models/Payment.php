<?php

namespace App\Models;

use App\Enums\Teacher\PaymentMethodEnum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Payment extends Model
{
    protected $fillable = [
        'receipt_number', 'invoice_id', 'student_id', 'teacher_id',
        'amount', 'payment_method', 'reference', 'paid_at', 'paid_time',
        'notes', 'created_by',
    ];

    protected $casts = [
        'amount'    => 'decimal:2',
        'paid_at'   => 'date',
        'paid_time' => 'datetime:H:i',
    ];

    // ============ Relations ============
    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(Teacher::class, 'teacher_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(Teacher::class, 'created_by');
    }

    public function allocations(): HasMany
    {
        return $this->hasMany(PaymentAllocation::class);
    }

    // ============ Accessors ============
    public function getPaymentMethodLabelAttribute(): string
    {
        return PaymentMethodEnum::tryFrom($this->payment_method)?->label() ?? $this->payment_method;
    }

    public function getPaymentMethodIconAttribute(): string
    {
        return PaymentMethodEnum::tryFrom($this->payment_method)?->icon() ?? 'ph-money';
    }
}
