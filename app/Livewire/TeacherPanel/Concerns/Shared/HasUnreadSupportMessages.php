<?php

namespace App\Livewire\TeacherPanel\Concerns\Shared;

use App\Models\SupportTicketMessage;
use Illuminate\Support\Facades\Auth;

trait HasUnreadSupportMessages
{
    public function unreadSupportCount(): int
    {
        $user = Auth::guard('teacher')->user();

        if (! $user) {
            return 0;
        }

        return SupportTicketMessage::query()
            ->whereNull('read_at')
            ->where('sender_type', 'admin')
            ->whereHas('ticket', function ($q) use ($user) {
                $q->where('teacher_id', $user->getOwnerId());
            })
            ->count();
    }
}
