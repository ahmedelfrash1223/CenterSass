<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Group extends Model
{
    protected $fillable = [
        'teacher_id',
        'branch_id',
        'name',
        'description',
        'max_students',
        'status',
        'start_date',
        'end_date',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'max_students' => 'integer',
    ];

    // =============================================
    // 📌 RELATIONS
    // =============================================

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(Teacher::class, 'teacher_id');
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }

    public function schedules(): HasMany
    {
        return $this->hasMany(GroupSchedule::class, 'group_id');
    }

    // ✅ العلاقة الصحيحة مع Pivot Table
    public function students(): BelongsToMany
    {
        return $this->belongsToMany(Student::class, 'group_student')
            ->using(GroupStudent::class)
            ->withPivot('joined_at', 'left_at', 'status')
            ->withTimestamps();
    }

    // Get active students only
    public function activeStudents(): BelongsToMany
    {
        return $this->students()->wherePivot('status', 'active');
    }

    // =============================================
    // 📌 ACCESSORS
    // =============================================

    public function getStudentCountAttribute(): int
    {
        return $this->activeStudents()->count();
    }

    public function getIsFullAttribute(): bool
    {
        if ($this->max_students === 0) {
            return false;
        }
        return $this->student_count >= $this->max_students;
    }

    // =============================================
    // 📌 METHODS
    // =============================================

    public function addStudent(Student $student, ?string $joinedAt = null): bool
    {
        if ($this->is_full) {
            return false;
        }

        if ($student->isInGroup($this)) {
            return false;
        }

        $this->students()->attach($student->id, [
            'joined_at' => $joinedAt ?? now(),
            'status' => 'active',
        ]);

        return true;
    }

    public function removeStudent(Student $student, ?string $leftAt = null): void
    {
        $this->students()->updateExistingPivot($student->id, [
            'left_at' => $leftAt ?? now(),
            'status' => 'left',
        ]);
    }
}
