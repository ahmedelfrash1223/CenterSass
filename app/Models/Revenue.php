<?php

namespace App\Models;

use App\Enums\Teacher\RevenueSourceEnum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Revenue extends Model
{
    protected $fillable = [
        'reference',
        'teacher_id',
        'source',
        'source_id',
        'recorded_by',
        'recorded_by_admin_id',
        'amount',
        'currency',
        'branch_id',
        'student_id',
        'description',
        'meta',
        'occurred_at',
    ];

    protected function casts(): array
    {
        return [
            'source' => RevenueSourceEnum::class,
            'amount' => 'decimal:2',
            'meta' => 'array',
            'occurred_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (self $revenue) {
            if (empty($revenue->reference)) {
                $revenue->reference = 'REV-'.strtoupper(Str::random(8));
            }
        });
    }

    // =============================================
    // Relations
    // =============================================

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(Teacher::class, 'teacher_id');
    }

    public function recordedBy(): BelongsTo
    {
        return $this->belongsTo(Teacher::class, 'recorded_by');
    }

    public function recordedByAdmin(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'recorded_by_admin_id');
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }
}
