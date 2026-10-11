<?php

namespace App\Models;

use App\Enums\Teacher\AssignmentGradeEnum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AssignmentStudent extends Model
{
    protected $table = 'assignment_student';

    protected $fillable = [
        'assignment_id',
        'student_id',
        'score',
        'grade',
        'notes',
        'graded_by',
        'graded_at',
    ];

    protected function casts(): array
    {
        return [
            'score' => 'decimal:2',
            'grade' => AssignmentGradeEnum::class,
            'graded_at' => 'datetime',
        ];
    }

    public function assignment(): BelongsTo
    {
        return $this->belongsTo(Assignment::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function gradedBy(): BelongsTo
    {
        return $this->belongsTo(Teacher::class, 'graded_by');
    }

    public function isGraded(): bool
    {
        return $this->score !== null;
    }

    public function percentage(): ?float
    {
        if ($this->score === null || ! $this->assignment) {
            return null;
        }

        $max = (float) $this->assignment->max_score;

        return $max > 0 ? round(($this->score / $max) * 100, 2) : null;
    }
}
