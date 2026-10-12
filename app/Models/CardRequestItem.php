<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CardRequestItem extends Model
{
    protected $fillable = [
        'card_request_id',
        'student_id',
        'student_name',
        'student_code',
        'grade',
        'branch_id',
        'group_id',
    ];

    public function cardRequest(): BelongsTo
    {
        return $this->belongsTo(CardRequest::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class);
    }
}
