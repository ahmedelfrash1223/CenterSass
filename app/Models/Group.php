<?php

namespace App\Models;

use App\Traits\Teacher\HasCreator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Group extends Model
{
    use HasCreator;

    protected $fillable = [
        'teacher_id',
        'created_by',
        'branch_id',
        'grade',
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

    public function students(): BelongsToMany
    {
        return $this->belongsToMany(Student::class, 'group_student')
            ->using(GroupStudent::class)
            ->withPivot('joined_at', 'left_at', 'status')
            ->withTimestamps();
    }

    public function activeStudents(): BelongsToMany
    {
        return $this->students()->wherePivot('status', 'active');
    }

    // =============================================
    // 📌 ACCESSORS
    // =============================================

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'active' => 'success',
            'inactive' => 'secondary',
            'completed' => 'info',
            default => 'secondary',
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'active' => 'نشط',
            'inactive' => 'غير نشط',
            'completed' => 'منتهي',
            default => $this->status,
        };
    }


    // =============================================
    // 📌 METHODS
    // =============================================

    public function addStudent(Student $student): bool
    {
        $exists = $this->students()
            ->where('student_id', $student->id)
            ->wherePivot('status', 'active')
            ->exists();

        if ($exists) {
            return false;
        }

        if ($this->max_students > 0) {
            $current = $this->students()->wherePivot('status', 'active')->count();
            if ($current >= $this->max_students) {
                return false;
            }
        }

        $this->students()->attach($student->id, [
            'joined_at' => now()->toDateString(),
            'status' => 'active',
            'created_by' => auth('teacher')->id(),
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
