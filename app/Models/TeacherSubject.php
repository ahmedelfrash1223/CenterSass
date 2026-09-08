<?php

namespace App\Models;

use App\Enums\Teacher\GradeEnum;
use App\Enums\Teacher\SubjectEnum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TeacherSubject extends Model
{
    protected $fillable = [
        'teacher_id',
        'grade',
        'subject',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(Teacher::class, 'teacher_id');
    }

    public function getGradeLabelAttribute(): string
    {
        return GradeEnum::tryFrom($this->grade)?->label() ?? $this->grade;
    }

    public function getSubjectLabelAttribute(): string
    {
        return SubjectEnum::tryFrom($this->subject)?->label() ?? $this->subject;
    }
}
