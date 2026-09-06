<?php

namespace App\Models;

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
}
