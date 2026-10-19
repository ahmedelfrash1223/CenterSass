<?php

namespace App\Models;

use App\Enums\Teacher\TeacherPaymentTypeEnum;
use App\Enums\Teacher\TeacherPayrollStatusEnum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class TeacherPayroll extends Model
{
    protected $fillable = [
        'reference', 'teacher_id', 'branch_id',
        'period_start', 'period_end', 'period_label',
        'payment_type',
        'total_sessions', 'cancelled_sessions', 'payable_sessions', 'total_hours',
        'base_salary', 'sessions_amount', 'hours_amount',
        'bonus', 'deductions', 'net_amount',
        'paid_amount', 'status',
        'paid_at', 'paid_by',
        'notes',
        'generated_by',
        'is_reviewed', 'reviewed_at', 'reviewed_by', 'review_notes',
    ];

    protected function casts(): array
    {
        return [
            'period_start' => 'date',
            'period_end' => 'date',
            'payment_type' => TeacherPaymentTypeEnum::class,
            'status' => TeacherPayrollStatusEnum::class,
            'base_salary' => 'decimal:2',
            'sessions_amount' => 'decimal:2',
            'hours_amount' => 'decimal:2',
            'bonus' => 'decimal:2',
            'deductions' => 'decimal:2',
            'net_amount' => 'decimal:2',
            'paid_amount' => 'decimal:2',
            'total_hours' => 'decimal:2',
            'paid_at' => 'datetime',
            'is_reviewed' => 'boolean',
            'reviewed_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (self $payroll) {
            if (empty($payroll->reference)) {
                $payroll->reference = 'PR-'.strtoupper(Str::random(8));
            }
        });
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

    public function generatedBy(): BelongsTo
    {
        return $this->belongsTo(Teacher::class, 'generated_by');
    }

    public function reviewedBy(): BelongsTo
    {
        return $this->belongsTo(Teacher::class, 'reviewed_by');
    }

    public function bonuses(): HasMany
    {
        return $this->hasMany(TeacherPayrollBonus::class, 'payroll_id');
    }

    public function deductionsItems(): HasMany
    {
        return $this->hasMany(TeacherPayrollDeduction::class, 'payroll_id');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(TeacherPayrollPayment::class, 'payroll_id');
    }

    public function expenses(): HasMany
    {
        return $this->hasMany(Expense::class, 'teacher_payroll_id');
    }

    public function remainingAmount(): float
    {
        return max(0, (float) $this->net_amount - (float) $this->paid_amount);
    }

    public function isFullyPaid(): bool
    {
        return $this->paid_amount >= $this->net_amount;
    }
}
