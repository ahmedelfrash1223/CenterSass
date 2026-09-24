<?php

namespace App\Models;

use App\Traits\GroupSchedule\FormatsDuration;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
}
