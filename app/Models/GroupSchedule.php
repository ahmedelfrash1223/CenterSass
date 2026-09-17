<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GroupSchedule extends Model
{
    protected $fillable = [
        'group_id',
        'day',
        'start_time',
        'end_time',
        'subject_id',
        'teacher_id',
        'hours',
    ];

    protected $casts = [
        'hours' => 'decimal:1',
        'start_time' => 'datetime:H:i',
        'end_time' => 'datetime:H:i',
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

    public function getFormattedHoursAttribute(): string
    {
        if (!$this->hours) {
            return '—';
        }

        $totalMinutes = (int) round($this->hours * 60);
        $h = intdiv($totalMinutes, 60);
        $m = $totalMinutes % 60;

        if ($h > 0 && $m > 0) return "{$h} س {$m} د";
        if ($h > 0) return "{$h} س";
        return "{$m} د";
    }
}
