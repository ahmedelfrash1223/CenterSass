<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TeacherPayrollDeduction extends Model
{
    protected $fillable = [
        'payroll_id',
        'amount',
        'reason',
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

    public function addedBy(): BelongsTo
    {
        return $this->belongsTo(Teacher::class, 'added_by');
    }
}
