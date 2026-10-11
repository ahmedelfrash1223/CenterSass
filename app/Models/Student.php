<?php

namespace App\Models;

use App\Traits\Student\GeneratesBarcode;
use App\Traits\Student\HasStudentPresentation;
use App\Traits\Teacher\HasCreator;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Student extends Authenticatable
{
    use GeneratesBarcode, HasCreator, HasStudentPresentation;

    protected $guard = 'student';

    protected $rememberTokenName = '';

    protected $fillable = [
        'code',
        'barcode_digits',
        'uuid',
        'name',
        'phone',
        'grade',
        'branch_id',
        'is_active',
        'created_by',
        'teacher_id',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'integer',
        ];
    }

    // =============================================
    // RELATIONS
    // =============================================

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(Teacher::class, 'teacher_id');
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function profile(): HasOne
    {
        return $this->hasOne(StudentProfile::class, 'student_id');
    }

    public function groups(): BelongsToMany
    {
        return $this->belongsToMany(Group::class, 'group_student')
            ->withPivot(['joined_at', 'left_at', 'status', 'created_by'])
            ->withTimestamps();
    }

    public function activeGroups(): BelongsToMany
    {
        return $this->groups()->wherePivot('status', 'active');
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class, 'confirmed_student_id');
    }
}
