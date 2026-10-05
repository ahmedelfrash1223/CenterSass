<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Exam extends Model
{
//    use HasExamAttributes;

    #-----------------------------------------------------FILLABLE
    protected $fillable = [
        'teacher_id',
        'created_by',
        'title',
        'description',
        'instructions',
        'duration',
        'status',
        'starts_at',
        'ends_at',
        'shuffle_questions',
        'shuffle_options',
        'show_result',
        'total_marks',
    ];

    #-----------------------------------------------------RELATIONSHIPS
    public function teacher(): BelongsTo
    {
        return $this->belongsTo(Teacher::class, 'teacher_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(Teacher::class, 'created_by');
    }

    public function questions(): HasMany
    {
        return $this->hasMany(ExamQuestion::class);
    }

    public function targets(): HasMany
    {
        return $this->hasMany(ExamTarget::class);
    }
}
