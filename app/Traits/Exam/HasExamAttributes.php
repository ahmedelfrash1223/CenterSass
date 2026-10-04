<?php

namespace App\Traits\Exam;

trait HasExamAttributes
{
//    protected $guarded = [];

    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'shuffle_questions' => 'boolean',
        'shuffle_options' => 'boolean',
        'show_result' => 'boolean',
        'total_marks' => 'decimal:2',
        'duration' => 'integer',
    ];
}
