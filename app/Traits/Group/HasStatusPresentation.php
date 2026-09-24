<?php

namespace App\Traits\Group;

use App\Models\GroupSchedule;
use App\Models\Student;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

trait HasStatusPresentation
{
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

    public function activeStudents(): BelongsToMany
    {
        return $this->belongsToMany(Student::class, 'group_student', 'group_id', 'student_id')
            ->wherePivot('status', 'active')
            ->withTimestamps();
    }

    public function schedules(): HasMany
    {
        return $this->hasMany(GroupSchedule::class);
    }
}
