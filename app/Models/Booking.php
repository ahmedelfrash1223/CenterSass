<?php

namespace App\Models;

use App\Traits\Teacher\HasCreator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Booking extends Model
{
    use HasCreator;

    protected $fillable = [
        'uuid',
        'name',
        'phone',
        'grade',
        'branch_id',
        'teacher_id',
        'created_by',
        'guardian_name',
        'guardian_phone',
        'guardian_relation',
        'school_name',
        'address',
        'city',
        'notes',
        'group_ids',
        'status',
        'confirmed_student_id',
        'confirmed_at',
    ];

    protected function casts(): array
    {
        return [
            'group_ids' => 'array',
            'confirmed_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (self $booking) {
            if (empty($booking->uuid)) {
                $booking->uuid = (string) Str::uuid();
            }
        });
    }

    // =============================================
    // RELATIONS
    // =============================================

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(Teacher::class, 'teacher_id');
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function confirmedStudent(): BelongsTo
    {
        return $this->belongsTo(Student::class, 'confirmed_student_id');
    }

    // =============================================
    // SCOPES
    // =============================================

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeConfirmed($query)
    {
        return $query->where('status', 'confirmed');
    }

    public function scopeCancelled($query)
    {
        return $query->where('status', 'cancelled');
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isConfirmed(): bool
    {
        return $this->status === 'confirmed';
    }
}
