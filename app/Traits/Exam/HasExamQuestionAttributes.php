<?php

namespace App\Traits\Exam;

trait HasExamQuestionAttributes
{
    protected $guarded = [];

    protected $casts = [
        'marks' => 'decimal:2',
        'sort_order' => 'integer',
        'is_required' => 'boolean',
        'settings' => 'array',
    ];
}
