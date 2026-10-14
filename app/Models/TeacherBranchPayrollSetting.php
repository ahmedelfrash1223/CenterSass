<?php

namespace App\Models;

use App\Enums\Teacher\TeacherPaymentTypeEnum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TeacherBranchPayrollSetting extends Model
{
    protected $fillable = [
        'teacher_id',
        'branch_id',
        'payment_type',
        'monthly_salary',
        'per_session_rate',
        'hourly_rate',
        'session_deduction_rate',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'payment_type' => TeacherPaymentTypeEnum::class,
            'monthly_salary' => 'decimal:2',
            'per_session_rate' => 'decimal:2',
            'hourly_rate' => 'decimal:2',
            'session_deduction_rate' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(Teacher::class, 'teacher_id');
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }
}
