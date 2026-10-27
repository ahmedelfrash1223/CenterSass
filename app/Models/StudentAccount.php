<?php

namespace App\Models;

use App\Enums\Student\AuthProviderEnum;
use App\Enums\Student\StudentAccountStatusEnum;
use App\Traits\Student\HasAccountIdentityDefaults;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class StudentAccount extends Authenticatable
{
    use HasApiTokens;
    use HasAccountIdentityDefaults;
    use Notifiable;
    use SoftDeletes;

    protected $guard = 'student_account';

    protected $fillable = [
        'uuid',
        'name',
        'email',
        'phone',
        'password',
        'email_verified_at',
        'phone_verified_at',
        'google_id',
        'avatar',
        'provider',
        'status',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'email_verified_at' => 'datetime',
            'phone_verified_at' => 'datetime',
            'provider' => AuthProviderEnum::class,
            'status' => StudentAccountStatusEnum::class,
            'is_active' => 'boolean',
        ];
    }

    // =============================================
    // Relations
    // =============================================

    public function students(): BelongsToMany
    {
        return $this->belongsToMany(
            Student::class,
            'student_account_student',
            'student_account_id',
            'student_id'
        )->withPivot('linked_at')->withTimestamps();
    }

    // =============================================
    // Helpers
    // =============================================

    public function isEmailVerified(): bool
    {
        return $this->email_verified_at !== null;
    }

    public function isPhoneVerified(): bool
    {
        return $this->phone_verified_at !== null;
    }

    public function isGoogleAccount(): bool
    {
        return $this->provider === AuthProviderEnum::GOOGLE;
    }

    public function canLogin(): bool
    {
        return $this->is_active && $this->status->canLogin();
    }

    public function markEmailAsVerified(): void
    {
        if (! $this->isEmailVerified()) {
            $this->forceFill(['email_verified_at' => now()])->save();
        }
    }

    public function markPhoneAsVerified(): void
    {
        if (! $this->isPhoneVerified()) {
            $this->forceFill(['phone_verified_at' => now()])->save();
        }
    }


}
