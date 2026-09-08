<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

class Student extends Authenticatable
{
    use Notifiable;

    protected $guard = 'student';

    protected $fillable = [
        'code',
        'username',
        'name',
        'email',
        'password',
        'is_active',
        'teacher_id',
        'group_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    #===================== CASTS ====================>
    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'is_active' => 'integer',
        ];
    }

    #================== BOOT METHOD ==================>
    protected static function booted(): void
    {
        static::creating(function (Student $student) {
            $student->uuid ??= (string) Str::uuid();;
            $student->code ??= 'STD-' . strtoupper(uniqid());
        });
    }


    #=================== RELATIONS ===================>
    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }

    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    // Many-to-Many with Groups
    public function groups(): BelongsToMany
    {
        return $this->belongsToMany(Group::class, 'group_student')
            ->withPivot('joined_at', 'left_at', 'status')
            ->withTimestamps();
    }

    // Get active groups only
    public function activeGroups(): BelongsToMany
    {
        return $this->groups()->wherePivot('status', 'active');
    }

    // Get groups where student is currently enrolled
    public function currentGroups(): BelongsToMany
    {
        return $this->groups()
            ->wherePivot('status', 'active')
            ->where(function ($query) {
                $query->whereNull('pivot_left_at')
                    ->orWhere('pivot_left_at', '>', now());
            });
    }

    // Check if student is in a specific group
    public function isInGroup(Group $group): bool
    {
        return $this->groups()
            ->where('group_id', $group->id)
            ->wherePivot('status', 'active')
            ->exists();
    }

    // Enroll student in a group
    public function enrollInGroup(Group $group, ?string $joinedAt = null): void
    {
        $this->groups()->attach($group->id, [
            'joined_at' => $joinedAt ?? now(),
            'status' => 'active',
        ]);
    }

    // Leave a group
    public function leaveGroup(Group $group, ?string $leftAt = null): void
    {
        $this->groups()->updateExistingPivot($group->id, [
            'left_at' => $leftAt ?? now(),
            'status' => 'left',
        ]);
    }
}
