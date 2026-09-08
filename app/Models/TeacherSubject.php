<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

class TeacherSubject extends Model
{
    protected $fillable = ['teacher_id', 'grade', 'subject', 'is_active'];

    protected $casts = [
        'is_active' => 'integer',
    ];

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(Teacher::class);
    }
}