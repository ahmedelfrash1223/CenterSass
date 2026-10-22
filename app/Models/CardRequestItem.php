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
        'card_price',
        'is_paid',
        'paid_at',
        'paid_by',
        'paid_by_admin_id',
        'revenue_id',
    ];

    protected function casts(): array
    {
        return [
            'card_price' => 'decimal:2',
            'is_paid' => 'boolean',
            'paid_at' => 'datetime',
        ];
    }

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

    public function paidBy(): BelongsTo
    {
        return $this->belongsTo(Teacher::class, 'paid_by');
    }

    public function paidByAdmin(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'paid_by_admin_id');
    }
}
