<?php

namespace App\Models;

use App\Enums\Teacher\GradeEnum;
use App\Traits\Teacher\HasCreator;
use App\Traits\TeacherSubject\HasEnumLabels;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TeacherSubject extends Model
{
    use HasCreator, HasEnumLabels;

    protected $fillable = [
        'teacher_id',
        'created_by',
        'grade',
        'subject',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // =============================================
    // Relations
    // =============================================

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(Teacher::class, 'teacher_id');
    }

    // =============================================
    // Accessors
    // =============================================

    public function getSubjectLabelAttribute(): string
    {
        return (string) ($this->subject ?? '—');
    }

    public function getGradeLabelAttribute(): string
    {
        return GradeEnum::tryFrom($this->grade)?->label() ?? (string) $this->grade;
    }
}
