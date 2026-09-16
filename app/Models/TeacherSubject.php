<?php

namespace App\Models;

use App\Enums\Teacher\GradeEnum;
use App\Enums\Teacher\SubjectEnum;
use App\Traits\Teacher\HasCreator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TeacherSubject extends Model
{
    use HasCreator;

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
    // 📌 RELATIONS
    // =============================================

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(Teacher::class, 'teacher_id');
    }

    // =============================================
    // 📌 ACCESSORS
    // =============================================

    public function getGradeLabelAttribute(): string
    {
        return GradeEnum::tryFrom($this->grade)?->label() ?? $this->grade;
    }

    public function getSubjectLabelAttribute(): string
    {
        return SubjectEnum::tryFrom($this->subject)?->label() ?? $this->subject;
    }
}
