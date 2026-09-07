<?php

namespace App\Models;

use App\Models\Package;
use App\Models\Teacher;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Subscription extends Model
{
    protected $table = 'subscriptions';

    protected $fillable = [
        'teacher_id',
        'package_id',
        'starts_at',
        'ends_at',
        'price',
        'discount_type',
        'discount_value',
        'price_after_discount',
        'status',
        'is_paid',
        'paid_at',
        'parent_id',
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'paid_at' => 'datetime',
        'price' => 'decimal:2',
        'discount_value' => 'decimal:2',
        'price_after_discount' => 'decimal:2',
        'is_paid' => 'boolean',
    ];

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(
            Teacher::class,
            'teacher_id'
        );
    }

    public function package(): BelongsTo
    {
        return $this->belongsTo(
            Package::class,
            'package_id'
        );
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(
            self::class,
            'parent_id'
        );
    }

    public function renewals(): HasMany
    {
        return $this->hasMany(
            self::class,
            'parent_id'
        );
    }

    public function features(): HasMany
    {
        return $this->hasMany(
            SubscriptionFeature::class,
            'subscription_id'
        );
    }

    public function histories(): HasMany
    {
        return $this->hasMany(SubscriptionHistory::class)->latest('created_at');
    }
}
