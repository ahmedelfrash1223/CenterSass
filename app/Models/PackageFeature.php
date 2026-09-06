<?php

namespace App\Models;

use App\Enums\Admin\PackageFeatureKeyEnum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PackageFeature extends Model
{
    protected $fillable = ['package_id', 'package_feature_key', 'value'];
    protected $casts = ['package_feature_key' => PackageFeatureKeyEnum::class, 'value' => 'array'];

    public function package(): BelongsTo
    {
        return $this->belongsTo(Package::class);
    }
}
