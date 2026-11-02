<?php

namespace App\Models;

use App\Enums\Teacher\ScheduleCancellationReasonEnum;
use App\Traits\GroupSchedule\FormatsDuration;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GroupSchedule extends Model
{
    use FormatsDuration;

    protected $fillable = [
        'group_id',
        'day',
        'start_time',
        'end_time',
        'subject_id',
        'teacher_id',
        'hours',
        'is_cancelled',
        'cancellation_reason',
        'cancellation_notes',
        'cancelled_at',
        'cancelled_by',
    ];

    protected $casts = [
        'hours' => 'decimal:1',
        'start_time' => 'datetime:H:i',
        'end_time' => 'datetime:H:i',
        'is_cancelled' => 'boolean',
        'cancellation_reason' => ScheduleCancellationReasonEnum::class,
        'cancelled_at' => 'datetime',
    ];

    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class, 'group_id');
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(TeacherSubject::class, 'subject_id');
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(Teacher::class, 'teacher_id');
    }

    public function canceller(): BelongsTo
    {
        return $this->belongsTo(Teacher::class, 'cancelled_by');
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class, 'schedule_id');
    }
}
