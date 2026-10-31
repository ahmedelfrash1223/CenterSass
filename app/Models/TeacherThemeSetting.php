<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TeacherThemeSetting extends Model
{
    protected $fillable = [
        'teacher_id',
        'theme_mode',
        'layout_width',
    ];

    /**
     * Get the teacher that owns the theme settings.
     */
    public function teacher(): BelongsTo
    {
        return $this->belongsTo(Teacher::class);
    }
}
