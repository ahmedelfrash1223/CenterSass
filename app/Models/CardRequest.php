<?php

namespace App\Models;

use App\Enums\Teacher\CardRequestStatusEnum;
use App\Traits\Teacher\HasCreator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class CardRequest extends Model
{
    use HasCreator;

    protected $fillable = [
        'reference',
        'teacher_id',
        'created_by',
        'status',
        'cards_count',
        'unit_price',
        'total_price',
        'paid_amount',
        'paid_cards_count',
        'notes',
        'admin_notes',
        'approved_at',
        'completed_at',
        'shipped_at',
        'delivered_at',
        'rejected_at',
    ];

    protected function casts(): array
    {
        return [
            'status' => CardRequestStatusEnum::class,
            'unit_price' => 'decimal:2',
            'total_price' => 'decimal:2',
            'paid_amount' => 'decimal:2',
            'approved_at' => 'datetime',
            'completed_at' => 'datetime',
            'shipped_at' => 'datetime',
            'delivered_at' => 'datetime',
            'rejected_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (self $request) {
            if (empty($request->reference)) {
                $request->reference = 'CR-'.strtoupper(Str::random(8));
            }
        });
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(Teacher::class, 'teacher_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(CardRequestItem::class);
    }

    public function isPending(): bool
    {
        return $this->status === CardRequestStatusEnum::PENDING;
    }

    public function canBeCancelled(): bool
    {
        return in_array($this->status, [
            CardRequestStatusEnum::PENDING,
            CardRequestStatusEnum::APPROVED,
        ]);
    }

    public function isFullyPaid(): bool
    {
        return $this->paid_amount >= $this->total_price;
    }

    public function remainingAmount(): float
    {
        return max(0, (float) $this->total_price - (float) $this->paid_amount);
    }
}
