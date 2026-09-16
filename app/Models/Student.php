<?php

namespace App\Models;

use App\Traits\Teacher\HasCreator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Student extends Model
{
    use HasCreator;

    protected $fillable = [
        'code',
        'uuid',
        'username',
        'name',
        'email',
        'phone',
        'password',
        'grade',
        'branch_id',
        'is_active',
        'created_by',
        'teacher_id',
    ];

    protected $hidden = [
        'password',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'is_active' => 'integer',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Student $student) {
            $student->uuid ??= (string) Str::uuid();
            $student->code ??= 'STD-' . strtoupper(uniqid());
        });
    }

    // =============================================
    // 📌 RELATIONS
    // =============================================

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(Teacher::class, 'teacher_id');
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
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

    // =============================================
    // 📌 HELPERS
    // =============================================

    public function getOwnerId(): int
    {
        return $this->teacher_id;
    }
}
