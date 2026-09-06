<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AdminThemeSetting extends Model
{
    protected $fillable = [
        'admin_id',
        'theme_mode',
        'sidebar_theme',
        'accent_color',
        'layout_width',
    ];

    /**
     * Get the admin that owns the theme settings.
     */
    public function admin(): BelongsTo
    {
        return $this->belongsTo(Admin::class);
    }
}
