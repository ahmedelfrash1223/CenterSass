<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

class Teacher extends Authenticatable
{
    use Notifiable;

    protected $guard = 'teacher';

    protected $fillable = [
        'code',
        'username',
        'name',
        'email',
        'password',
        'phone',
        'is_active',
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
        static::creating(function (Teacher $teacher) {
            $teacher->uuid ??= (string) Str::uuid();
            $teacher->code ??= 'TCH-' . strtoupper(uniqid());
        });
    }


    #=================== RELATIONS ===================>
    public function students()
    {
        return $this->hasMany(Student::class);
    }

    public function branches(): HasMany
    {
        return $this->hasMany(Branch::class);
    }

    public function subjects(): HasMany
    {
        return $this->hasMany(TeacherSubject::class);
    }

    public function groups(): HasMany
    {
        return $this->hasMany(Group::class);
    }

    public function themeSettings(): HasOne
    {
        return $this->hasOne(TeacherThemeSetting::class);
    }
}
