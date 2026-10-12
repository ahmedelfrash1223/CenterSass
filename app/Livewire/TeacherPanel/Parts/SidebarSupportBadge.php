<?php

namespace App\Livewire\TeacherPanel\Parts;

use App\Models\SupportTicketMessage;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class SidebarSupportBadge extends Component
{
    public int $count = 0;

    protected $listeners = ['refreshSupportBadge' => 'refresh'];

    public function mount(): void
    {
        $this->refresh();
    }

    public function refresh(): void
    {
        $user = Auth::guard('teacher')->user();

        if (! $user) {
            $this->count = 0;

            return;
        }

        $this->count = SupportTicketMessage::query()
            ->whereNull('read_at')
            ->where('sender_type', 'admin')
            ->whereHas('ticket', fn ($q) => $q->where('teacher_id', $user->getOwnerId()))
            ->count();
    }

    public function render()
    {
        return view('teacher-panel.parts.sidebar-support-badge');
    }
}
