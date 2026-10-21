<?php

namespace App\Models;

use App\Enums\Admin\AdminPermissionEnum;
use App\Traits\Admin\HasIdentityDefaults;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Admin extends Authenticatable
{
    use HasIdentityDefaults, Notifiable, SoftDeletes;

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

    // ===================== CASTS ====================>
    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'is_active' => 'integer',
        ];
    }

    // =================== RELATIONS ===================>
    public function themeSettings(): HasOne
    {
        return $this->hasOne(AdminThemeSetting::class);
    }

    public function getOwnerId(): ?int
    {
        return null;
    }

    public function hasPermission(string|AdminPermissionEnum $permission): bool
    {
        return true;
    }
}
