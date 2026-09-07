<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SubscriptionFeature extends Model
{
    protected $table = 'subscription_features';

    protected $fillable = [
        'subscription_id',
        'feature_key',
        'value',
    ];

    protected $casts = [
        'value' => 'array',
    ];

    public function subscription(): BelongsTo
    {
        return $this->belongsTo(
            Subscription::class,
            'subscription_id'
        );
    }


}
