<?php

namespace App\Models;

use App\Enums\Admin\SubscriptionRequestStatusEnum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SubscriptionRequest extends Model
{
    protected $fillable = [
        'teacher_id',
        'current_subscription_id',
        'requested_package_id',
        'status',
        'notes',
        'approved_at',
        'rejected_at',
        'processed_by',
    ];

    protected $casts = [
        'status' => 'integer',
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
    ];

    // =============================================
    // 📌 RELATIONS
    // =============================================
    public function teacher(): BelongsTo
    {
        return $this->belongsTo(Teacher::class, 'teacher_id');
    }

    public function currentSubscription(): BelongsTo
    {
        return $this->belongsTo(Subscription::class, 'current_subscription_id');
    }

    public function requestedPackage(): BelongsTo
    {
        return $this->belongsTo(Package::class, 'requested_package_id');
    }

    public function processedBy(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'processed_by');
    }

    // =============================================
    // 📌 ACCESSORS
    // =============================================
    public function getStatusLabelAttribute(): string
    {
        return SubscriptionRequestStatusEnum::tryFrom($this->status)?->label() ?? 'غير معروف';
    }

    public function getStatusColorAttribute(): string
    {
        return SubscriptionRequestStatusEnum::tryFrom($this->status)?->color() ?? 'secondary';
    }

    public function getStatusBadgeAttribute(): string
    {
        $color = $this->status_color;
        $label = $this->status_label;
        return "<span class='badge bg-{$color}-bg-opacity-10 text-{$color}'>{$label}</span>";
    }
}
