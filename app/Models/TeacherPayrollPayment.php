<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class TeacherPayrollPayment extends Model
{
    protected $fillable = [
        'reference',
        'payroll_id',
        'teacher_id',
        'branch_id',
        'amount',
        'payment_method',
        'reference_number',
        'notes',
        'paid_at',
        'paid_by',
        'expense_id',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'paid_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (self $payment) {
            if (empty($payment->reference)) {
                $payment->reference = 'TPP-'.strtoupper(Str::random(8));
            }
        });
    }

    public function payroll(): BelongsTo
    {
        return $this->belongsTo(TeacherPayroll::class, 'payroll_id');
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(Teacher::class, 'teacher_id');
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function paidBy(): BelongsTo
    {
        return $this->belongsTo(Teacher::class, 'paid_by');
    }

    public function expense(): BelongsTo
    {
        return $this->belongsTo(Expense::class);
    }
}
