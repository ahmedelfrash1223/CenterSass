<?php

namespace App\Models;

use App\Enums\Teacher\SupportTicketCategoryEnum;
use App\Enums\Teacher\SupportTicketPriorityEnum;
use App\Enums\Teacher\SupportTicketStatusEnum;
use App\Traits\Teacher\HasCreator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class SupportTicket extends Model
{
    use HasCreator;

    protected $fillable = [
        'reference',
        'teacher_id',
        'created_by',
        'subject',
        'category',
        'priority',
        'status',
        'last_reply_at',
        'closed_at',
    ];

    protected function casts(): array
    {
        return [
            'category' => SupportTicketCategoryEnum::class,
            'priority' => SupportTicketPriorityEnum::class,
            'status' => SupportTicketStatusEnum::class,
            'last_reply_at' => 'datetime',
            'closed_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (self $ticket) {
            if (empty($ticket->reference)) {
                $ticket->reference = 'TKT-'.strtoupper(Str::random(8));
            }
        });
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(Teacher::class, 'teacher_id');
    }

    public function messages(): HasMany
    {
        return $this->hasMany(SupportTicketMessage::class, 'ticket_id')->orderBy('created_at');
    }

    public function lastMessage(): ?SupportTicketMessage
    {
        return $this->messages()->latest()->first();
    }

    public function isClosed(): bool
    {
        return $this->status === SupportTicketStatusEnum::CLOSED;
    }

    public function unreadCount(): int
    {
        return $this->messages()
            ->where('sender_type', 'admin')
            ->whereNull('read_at')
            ->count();
    }
}
