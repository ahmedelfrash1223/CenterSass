<?php

namespace App\Models;

use App\Traits\Invoice\HasStatusAndDiscountPresentation;
use App\Traits\Invoice\RecalculatesTotals;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Invoice extends Model
{
    use HasStatusAndDiscountPresentation, RecalculatesTotals;

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
}
