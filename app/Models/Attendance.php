<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Attendance extends Model
{
    protected $fillable = [
        'student_id',
        'group_id',
        'schedule_id',
        'branch_id',
        'teacher_id',
        'created_by',
        'attendance_date',
        'check_in_time',
        'status',
        'notes',
        'is_auto_closed',
        'closed_at',
    ];

    protected $casts = [
        'attendance_date' => 'date',
        'is_auto_closed' => 'boolean',
        'closed_at' => 'datetime',
    ];

    // =============================================
    // RELATIONS
    // =============================================

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class);
    }

    public function schedule(): BelongsTo
    {
        return $this->belongsTo(GroupSchedule::class, 'schedule_id');
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(Teacher::class, 'teacher_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(Teacher::class, 'created_by');
    }

    // =============================================
    // HELPERS
    // =============================================

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'present' => 'success',
            'late' => 'warning',
            'absent' => 'danger',
            'excused' => 'info',
            default => 'secondary',
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'present' => 'حاضر',
            'late' => 'متأخر',
            'absent' => 'غائب',
            'excused' => 'بعذر',
            default => $this->status,
        };
    }
}
