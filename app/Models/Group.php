<?php

namespace App\Models;

use App\Traits\Teacher\HasCreator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Enums\Teacher\PricingTypeEnum;


class Group extends Model
{
    use HasCreator;

    protected $fillable = [
        'teacher_id',
        'created_by',
        'branch_id',
        'grade',
        'name',
        'description',
        'max_students',
        'status',
        'start_date',
        'end_date',
        'pricing_type',
        'pricing_amount',
        'pricing_config',
        'has_pricing',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'max_students' => 'integer',
        'pricing_config' => 'array',
        'pricing_amount' => 'decimal:2',
        'has_pricing' => 'boolean',
    ];

    // =============================================
    // 📌 RELATIONS
    // =============================================



    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(Teacher::class, 'teacher_id');
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }

    public function schedules(): HasMany
    {
        return $this->hasMany(GroupSchedule::class, 'group_id');
    }

    public function students(): BelongsToMany
    {
        return $this->belongsToMany(Student::class, 'group_student')
            ->using(GroupStudent::class)
            ->withPivot('joined_at', 'left_at', 'status')
            ->withTimestamps();
    }

    public function activeStudents(): BelongsToMany
    {
        return $this->students()->wherePivot('status', 'active');
    }

    public function invoiceItems(): HasMany
    {
        return $this->hasMany(InvoiceItem::class);
    }

    // =============================================
    // 📌 ACCESSORS
    // =============================================

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'active' => 'success',
            'inactive' => 'secondary',
            'completed' => 'info',
            default => 'secondary',
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'active' => 'نشط',
            'inactive' => 'غير نشط',
            'completed' => 'منتهي',
            default => $this->status,
        };
    }


    // =============================================
    // 📌 METHODS
    // =============================================

    public function addStudent(Student $student): bool
    {
        $exists = $this->students()
            ->where('student_id', $student->id)
            ->wherePivot('status', 'active')
            ->exists();

        if ($exists) {
            return false;
        }

        if ($this->max_students > 0) {
            $current = $this->students()->wherePivot('status', 'active')->count();
            if ($current >= $this->max_students) {
                return false;
            }
        }

        $this->students()->attach($student->id, [
            'joined_at' => now()->toDateString(),
            'status' => 'active',
            'created_by' => auth('teacher')->id(),
        ]);

        return true;
    }

    public function removeStudent(Student $student, ?string $leftAt = null): void
    {
        $this->students()->updateExistingPivot($student->id, [
            'left_at' => $leftAt ?? now(),
            'status' => 'left',
        ]);
    }



    public function getPricingTypeEnumAttribute(): ?PricingTypeEnum
    {
        return PricingTypeEnum::tryFrom($this->pricing_type);
    }

    public function getPricingTypeLabelAttribute(): string
    {
        return $this->pricing_type_enum?->label() ?? $this->pricing_type;
    }


    public function getFormattedPriceAttribute(): string
    {
        if (!$this->has_pricing || $this->pricing_amount <= 0) {
            return 'مجاني';
        }

        return number_format((float)$this->pricing_amount, 2) . ' ج.م';
    }

}
