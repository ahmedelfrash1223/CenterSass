<?php

namespace App\Traits\Teacher;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait HasCreator
{

    public function creator(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Teacher::class, 'created_by');
    }
}
