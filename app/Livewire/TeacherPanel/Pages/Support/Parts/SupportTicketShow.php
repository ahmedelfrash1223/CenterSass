<?php

namespace App\Livewire\TeacherPanel\Pages\Support\Parts;

use App\Enums\Teacher\SupportTicketStatusEnum;
use App\Enums\Teacher\TeacherPermissionEnum;
use App\Http\Requests\TeacherPanel\ReplySupportTicketRequest;
use App\Models\SupportTicket;
use App\Models\SupportTicketMessage;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class SupportTicketShow extends Component
{
    public SupportTicket $ticket;

    public string $newMessage = '';

    public function mount(SupportTicket $ticket): void
    {
        $user = Auth::guard('teacher')->user();

        abort_unless(
            $user->hasPermission(TeacherPermissionEnum::SUPPORT_VIEW),
            403
        );

        abort_unless($ticket->teacher_id === $user->getOwnerId(), 404);

        $this->ticket = $ticket;

        // mark admin messages as read
        $this->ticket->messages()
            ->where('sender_type', 'admin')
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        $this->dispatch('refreshSupportBadge');
    }

    public function sendMessage(): void
    {
        $user = Auth::guard('teacher')->user();

        if (! $user->hasPermission(TeacherPermissionEnum::SUPPORT_REPLY)) {
            $this->dispatch('show-error-message', message: 'ليس لديك صلاحية للرد على التذاكر.');

            return;
        }

        if ($this->ticket->isClosed()) {
            $this->dispatch('show-error-message', message: 'التذكرة مغلقة ولا يمكن الرد عليها.');

            return;
        }

        $request = new ReplySupportTicketRequest;
        $this->validate($request->rules(), $request->messages());

        SupportTicketMessage::create([
            'ticket_id' => $this->ticket->id,
            'sender_type' => 'teacher',
            'sender_id' => $user->id,
            'body' => $this->newMessage,
        ]);

        if ($this->ticket->status === SupportTicketStatusEnum::WAITING) {
            $this->ticket->update(['status' => SupportTicketStatusEnum::IN_PROGRESS->value]);
        }

        $this->ticket->update(['last_reply_at' => now()]);
        $this->ticket->refresh();

        $this->newMessage = '';

        $this->dispatch('message-sent');
        $this->dispatch('show-success-message', message: 'تم إرسال رسالتك.');
    }

    public function closeTicket(): void
    {
        $user = Auth::guard('teacher')->user();

        if (! $user->hasPermission(TeacherPermissionEnum::SUPPORT_CLOSE)) {
            $this->dispatch('show-error-message', message: 'ليس لديك صلاحية لإغلاق التذاكر.');

            return;
        }

        if ($this->ticket->isClosed()) {
            $this->dispatch('show-error-message', message: 'التذكرة مغلقة بالفعل.');

            return;
        }

        $this->ticket->update([
            'status' => SupportTicketStatusEnum::CLOSED->value,
            'closed_at' => now(),
        ]);

        $this->ticket->refresh();
        $this->dispatch('show-success-message', message: 'تم إغلاق التذكرة.');
    }

    public function render()
    {
        $messages = $this->ticket->messages()->get();

        return view('teacher-panel.pages.support.parts.support-ticket-show', [
            'messages' => $messages,
            'canReply' => Auth::guard('teacher')->user()->hasPermission(TeacherPermissionEnum::SUPPORT_REPLY),
            'canClose' => Auth::guard('teacher')->user()->hasPermission(TeacherPermissionEnum::SUPPORT_CLOSE),
        ]);
    }
}
