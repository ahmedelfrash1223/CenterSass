<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\SoftDeletes;

class Admin extends Authenticatable
{
    use Notifiable, SoftDeletes;

    protected $guard = 'admin';

    protected $fillable = [
        'code',
        'username',
        'name',
        'email',
        'password',
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
        static::creating(function (Admin $admin) {
            $admin->uuid ??= (string) Str::uuid();
            $admin->code ??= 'ADM-' . strtoupper(uniqid());
        });
    }

    #=================== RELATIONS ===================>
    public function themeSettings(): HasOne
    {
        return $this->hasOne(AdminThemeSetting::class);
    }

}
