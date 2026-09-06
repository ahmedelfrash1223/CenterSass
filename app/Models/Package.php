<?php

namespace App\Models;

use App\Enums\Admin\DiscountTypeEnum;
use App\Enums\Admin\DurationTypeEnum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Package extends Model
{
    protected $fillable = ['admin_id', 'title', 'price', 'discount_type', 'discount_value', 'price_after_discount', 'duration', 'duration_type'];
    protected $casts = ['discount_type' => DiscountTypeEnum::class, 'duration_type' => DurationTypeEnum::class, 'price' => 'decimal:2', 'discount_value' => 'decimal:2', 'price_after_discount' => 'decimal:2'];

    public function admin(): BelongsTo
    {
        return $this->belongsTo(Admin::class);
    }

    public function features(): HasMany
    {
        return $this->hasMany(PackageFeature::class);
    }
}
