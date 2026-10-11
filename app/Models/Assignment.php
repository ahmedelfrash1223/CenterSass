<?php

namespace App\Models;

use App\Enums\Teacher\AssignmentStatusEnum;
use App\Enums\Teacher\AssignmentTypeEnum;
use App\Traits\Teacher\HasCreator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Assignment extends Model
{
    use HasCreator;

    protected $fillable = [
        'title',
        'description',
        'type',
        'status',
        'max_score',
        'due_date',
        'group_id',
        'teacher_id',
        'branch_id',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'type' => AssignmentTypeEnum::class,
            'status' => AssignmentStatusEnum::class,
            'max_score' => 'decimal:2',
            'due_date' => 'date',
        ];
    }

    // =============================================
    // RELATIONS
    // =============================================

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(Teacher::class, 'teacher_id');
    }

    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function students(): BelongsToMany
    {
        return $this->belongsToMany(Student::class, 'assignment_student')
            ->withPivot(['score', 'grade', 'notes', 'graded_by', 'graded_at'])
            ->withTimestamps();
    }

    public function grades(): HasMany
    {
        return $this->hasMany(AssignmentStudent::class);
    }

    // =============================================
    // HELPERS
    // =============================================

    public function isActive(): bool
    {
        return $this->status === AssignmentStatusEnum::ACTIVE;
    }

    public function isClosed(): bool
    {
        return $this->status === AssignmentStatusEnum::CLOSED;
    }

    public function gradedCount(): int
    {
        return $this->grades()->whereNotNull('score')->count();
    }

    public function studentsCount(): int
    {
        return $this->grades()->count();
    }
}
