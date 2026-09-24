<?php

namespace App\Traits\Media;

use App\Models\Media;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;

trait HasMedia
{

    public function media(): MorphMany
    {
        return $this->morphMany(Media::class, 'mediable')
            ->orderBy('sort_order')
            ->orderBy('id');
    }


    public function singleMedia(): MorphOne
    {
        return $this->morphOne(Media::class, 'mediable')
            ->orderBy('sort_order');
    }


    public function mediaOfType(string $type): MorphMany
    {
        return $this->media()->where('type', $type);
    }

    public function firstMediaOfType(string $type): ?Media
    {
        return $this->mediaOfType($type)->first();
    }
}
