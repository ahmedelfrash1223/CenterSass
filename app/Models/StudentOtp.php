<?php

namespace App\Models;

use App\Enums\Student\OtpChannelEnum;
use App\Enums\Student\OtpPurposeEnum;
use Illuminate\Database\Eloquent\Model;

class StudentOtp extends Model
{
    protected $table = 'student_otps';

    protected $fillable = [
        'identifier',
        'channel',
        'purpose',
        'code',
        'attempts',
        'expires_at',
        'verified_at',
    ];

    protected function casts(): array
    {
        return [
            'channel' => OtpChannelEnum::class,
            'purpose' => OtpPurposeEnum::class,
            'attempts' => 'integer',
            'expires_at' => 'datetime',
            'verified_at' => 'datetime',
        ];
    }

    // =============================================
    // Scopes
    // =============================================

    public function scopeActive($query)
    {
        return $query
            ->whereNull('verified_at')
            ->where('expires_at', '>', now());
    }

    public function scopeForIdentifier($query, string $identifier, OtpPurposeEnum $purpose)
    {
        return $query
            ->where('identifier', $identifier)
            ->where('purpose', $purpose->value);
    }

    // =============================================
    // Helpers
    // =============================================

    public function isExpired(): bool
    {
        return $this->expires_at->isPast();
    }

    public function isVerified(): bool
    {
        return $this->verified_at !== null;
    }

    public function markAsVerified(): void
    {
        $this->forceFill(['verified_at' => now()])->save();
    }

    public function incrementAttempts(): void
    {
        $this->increment('attempts');
    }
}
