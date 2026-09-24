<?php

namespace App\Traits\Teacher;

use App\Models\Teacher;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait HasCreator
{
    public function creator(): BelongsTo
    {
        return $this->belongsTo(Teacher::class, 'created_by');
    }
}
