<?php

namespace App\Models;

use App\Traits\Group\HasPricingPresentation;
use App\Traits\Group\HasStatusPresentation;
use App\Traits\Group\ManagesStudents;
use App\Traits\Teacher\HasCreator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Group extends Model
{
    use HasCreator, HasPricingPresentation, HasStatusPresentation, ManagesStudents;

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
        'last_closed_date',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'max_students' => 'integer',
        'pricing_config' => 'array',
        'pricing_amount' => 'decimal:2',
        'has_pricing' => 'boolean',
        'last_closed_date' => 'date',
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
}
