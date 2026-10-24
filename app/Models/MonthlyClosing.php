<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class MonthlyClosing extends Model
{
    protected $fillable = [
        'reference',
        'teacher_id',
        'branch_id',
        'closed_by',
        'month',
        'total_revenue',
        'total_expense',
        'net_amount',
        'revenues_count',
        'expenses_count',
        'status',
        'closed_at',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'total_revenue' => 'decimal:2',
            'total_expense' => 'decimal:2',
            'net_amount' => 'decimal:2',
            'closed_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (self $row) {
            if (empty($row->reference)) {
                $row->reference = 'MC-'.strtoupper(Str::random(8));
            }
        });
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(Teacher::class, 'teacher_id');
    }

    public function closedBy(): BelongsTo
    {
        return $this->belongsTo(Teacher::class, 'closed_by');
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(MonthlyClosingItem::class);
    }

    public function logs(): HasMany
    {
        return $this->hasMany(MonthlyClosingLog::class);
    }

    public function revenueItems(): HasMany
    {
        return $this->items()->where('type', 'revenue');
    }

    public function expenseItems(): HasMany
    {
        return $this->items()->where('type', 'expense');
    }

    public function isClosed(): bool
    {
        return $this->status === 'closed';
    }
}
