<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ExamQuestion extends Model
{
    protected $table = 'exam_questions';

    protected $fillable = [
        'exam_id',
        'type',
        'question',
        'description',
        'marks',
        'sort_order',
        'is_required',
        'correct_answer',
        'media_id',
    ];

    protected $casts = [
        'marks'       => 'decimal:2',
        'sort_order'  => 'integer',
        'is_required' => 'boolean',
    ];

    public function exam(): BelongsTo
    {
        return $this->belongsTo(Exam::class);
    }

    public function options(): HasMany
    {
        return $this->hasMany(ExamQuestionOption::class, 'exam_question_id');
    }

    public function media(): BelongsTo
    {
        return $this->belongsTo(Media::class);
    }
}
