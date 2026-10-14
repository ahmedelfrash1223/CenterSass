<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TeacherPayrollBonus extends Model
{
    protected $fillable = [
        'payroll_id',
        'amount',
        'reason',
        'expense_id',
        'added_by',
    ];

    protected function casts(): array
    {
        return ['amount' => 'decimal:2'];
    }

    public function payroll(): BelongsTo
    {
        return $this->belongsTo(TeacherPayroll::class, 'payroll_id');
    }

    public function expense(): BelongsTo
    {
        return $this->belongsTo(Expense::class);
    }

    public function addedBy(): BelongsTo
    {
        return $this->belongsTo(Teacher::class, 'added_by');
    }
}
