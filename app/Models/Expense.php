<?php

namespace App\Models;

use App\Enums\Teacher\ExpenseSourceEnum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Expense extends Model
{
    protected $fillable = [
        'reference',
        'teacher_id',
        'source',
        'source_id',
        'recorded_by',
        'amount',
        'currency',
        'branch_id',
        'teacher_payee_id',
        'teacher_payroll_id',
        'description',
        'meta',
        'occurred_at',
    ];

    protected function casts(): array
    {
        return [
            'source' => ExpenseSourceEnum::class,
            'amount' => 'decimal:2',
            'meta' => 'array',
            'occurred_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (self $expense) {
            if (empty($expense->reference)) {
                $expense->reference = 'EXP-'.strtoupper(Str::random(8));
            }
        });
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(Teacher::class, 'teacher_id');
    }

    public function recordedBy(): BelongsTo
    {
        return $this->belongsTo(Teacher::class, 'recorded_by');
    }

    public function teacherPayee(): BelongsTo
    {
        return $this->belongsTo(Teacher::class, 'teacher_payee_id');
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function teacherPayroll(): BelongsTo
    {
        return $this->belongsTo(TeacherPayroll::class, 'teacher_payroll_id');
    }
}
