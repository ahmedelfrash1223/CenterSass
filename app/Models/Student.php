<?php

namespace App\Models;

use App\Traits\Teacher\HasCreator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Student extends Model
{
    use HasCreator;

    protected $fillable = [
        'code',
        'barcode_digits',
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

    /**
     * Boot the model — using boot() for guaranteed execution.
     */
    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (Student $student) {
            // Ensure uuid
            $student->uuid ??= (string)Str::uuid();

            // Ensure barcode_digits
            if (empty($student->barcode_digits)) {
                $student->barcode_digits = self::generateUniqueBarcodeDigits($student->uuid);
            }

            // Ensure code — never allow empty
            if (empty($student->code)) {
                $student->code = 'STD-' . $student->barcode_digits;
            }
        });
    }

    /**
     * Convert uuid → 12-digit deterministic string.
     * Same uuid → same digits, always.
     */
    public static function deriveBarcodeDigits(string $uuid): string
    {
        $hex = str_replace('-', '', $uuid);

        $digits = '';
        foreach (str_split($hex, 4) as $chunk) {
            // 4 hex chars = 0..65535 → pad to 5 digits
            $digits .= str_pad((string)hexdec($chunk), 5, '0', STR_PAD_LEFT);
        }

        return substr($digits, 0, 12);
    }

    /**
     * Generate a unique 12-digit barcode string.
     * Falls back to random if the derived value collides.
     */
    public static function generateUniqueBarcodeDigits(string $uuid): string
    {
        $digits = self::deriveBarcodeDigits($uuid);

        $attempts = 0;
        while (self::where('barcode_digits', $digits)->exists()) {
            $digits = str_pad((string)random_int(0, 999999999999), 12, '0', STR_PAD_LEFT);
            $attempts++;

            if ($attempts >= 10) {
                $digits = str_pad((string)random_int(0, 99999999999999), 14, '0', STR_PAD_LEFT);
                break;
            }
        }

        return $digits;
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

    // =============================================
    // HELPERS
    // =============================================

    public function getOwnerId(): int
    {
        return $this->teacher_id;
    }


    public function getBarcodeValueAttribute(): string
    {
        return $this->code ?? ('STD-' . $this->barcode_digits);
    }


    public function getQrValueAttribute(): string
    {
        return $this->code ?? $this->uuid;
    }


    public function getTotalUnpaidAttribute(): float
    {
        return (float) $this->invoices()
            ->whereIn('status', ['unpaid', 'partial'])
            ->sum('remaining_amount');
    }

    public function getHasUnpaidInvoicesAttribute(): bool
    {
        return $this->invoices()
            ->whereIn('status', ['unpaid', 'partial'])
            ->exists();
    }
}
