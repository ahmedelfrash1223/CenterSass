<?php

namespace App\Models;

use App\Traits\Payment\HasPaymentMethodPresentation;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Payment extends Model
{
    use HasPaymentMethodPresentation;

    protected $fillable = [
        'receipt_number', 'invoice_id', 'student_id', 'teacher_id',
        'amount', 'payment_method', 'reference', 'paid_at', 'paid_time',
        'notes', 'created_by',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'paid_at' => 'date',
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

    // ============ Helpers ============

    public static function generateReceiptNumber(): string
    {
        $year = now()->format('Y');
        $month = now()->format('m');

        $lastPayment = static::whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->orderByDesc('id')
            ->first();

        $nextNumber = 1;

        if ($lastPayment && $lastPayment->receipt_number) {
            $parts = explode('-', $lastPayment->receipt_number);
            $lastSeq = (int) end($parts);
            $nextNumber = $lastSeq + 1;
        }

        return sprintf('RCP-%s%s-%05d', $year, $month, $nextNumber);
    }
}
