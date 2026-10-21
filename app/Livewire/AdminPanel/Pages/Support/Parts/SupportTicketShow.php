<?php

namespace App\Livewire\AdminPanel\Pages\Support\Parts;

use App\Enums\Admin\SupportTicketStatusEnum;
use App\Http\Requests\AdminPanel\ReplySupportTicketRequest;
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
        $this->ticket = $ticket;

        $this->ticket->messages()
            ->where('sender_type', 'teacher')
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        $this->dispatch('refreshSupportBadge');
    }

    public function sendMessage(): void
    {
        if ($this->ticket->isClosed()) {
            $this->dispatch('show-error-message', message: 'التذكرة مغلقة ولا يمكن الرد عليها.');

            return;
        }

        $request = new ReplySupportTicketRequest;
        $this->validate($request->rules(), $request->messages());

        SupportTicketMessage::create([
            'ticket_id' => $this->ticket->id,
            'sender_type' => 'admin',
            'sender_id' => Auth::guard('admin')->id(),
            'body' => $this->newMessage,
        ]);

        if (in_array($this->ticket->status, [SupportTicketStatusEnum::OPEN, SupportTicketStatusEnum::WAITING], true)) {
            $this->ticket->update(['status' => SupportTicketStatusEnum::IN_PROGRESS->value]);
        }

        $this->ticket->update(['last_reply_at' => now()]);
        $this->ticket->refresh();

        $this->newMessage = '';

        $this->dispatch('message-sent');
        $this->dispatch('show-success-message', message: 'تم إرسال ردك.');
    }

    public function closeTicket(): void
    {
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

        return view('admin-panel.pages.support.parts.support-ticket-show', [
            'messages' => $messages,
            'canReply' => ! $this->ticket->isClosed(),
            'canClose' => ! $this->ticket->isClosed(),
        ]);
    }
}
