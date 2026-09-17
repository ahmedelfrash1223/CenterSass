<?php

namespace App\Models;

use App\Enums\Teacher\PaymentMethodEnum;
use App\Traits\Teacher\HasCreator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    use HasCreator;

    protected $fillable = [
        'receipt_number',
        'invoice_id',
        'student_id',
        'teacher_id',
        'amount',
        'payment_method',
        'reference',
        'paid_at',
        'paid_time',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'paid_at' => 'date',
        'paid_time' => 'datetime:H:i',
    ];

    // =============================================
    // 📌 RELATIONS
    // =============================================

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

    // =============================================
    // 📌 HELPERS
    // =============================================

    public function getPaymentMethodEnumAttribute(): ?PaymentMethodEnum
    {
        return PaymentMethodEnum::tryFrom($this->payment_method);
    }

    public function getPaymentMethodLabelAttribute(): string
    {
        return $this->payment_method_enum?->label() ?? $this->payment_method;
    }

    public function getPaymentMethodIconAttribute(): string
    {
        return $this->payment_method_enum?->icon() ?? 'ph-money';
    }
}
