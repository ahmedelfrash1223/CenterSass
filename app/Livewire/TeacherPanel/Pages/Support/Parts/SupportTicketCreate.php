<?php

namespace App\Livewire\TeacherPanel\Pages\Support\Parts;

use App\Enums\Teacher\SupportTicketCategoryEnum;
use App\Enums\Teacher\SupportTicketPriorityEnum;
use App\Enums\Teacher\SupportTicketStatusEnum;
use App\Enums\Teacher\TeacherPermissionEnum;
use App\Http\Requests\TeacherPanel\StoreSupportTicketRequest;
use App\Models\SupportTicket;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class SupportTicketCreate extends Component
{
    public string $subject = '';

    public string $category = '';

    public string $priority = 'normal';

    public string $message = '';

    public function mount(): void
    {
        $user = Auth::guard('teacher')->user();

        abort_unless(
            $user->hasPermission(TeacherPermissionEnum::SUPPORT_CREATE),
            403
        );
    }

    public function save(): void
    {
        $user = Auth::guard('teacher')->user();
        $ownerId = $user->getOwnerId();

        $request = new StoreSupportTicketRequest;

        $this->validate($request->rules(), $request->messages());

        $ticket = DB::transaction(function () use ($user, $ownerId) {
            $ticket = SupportTicket::create([
                'teacher_id' => $ownerId,
                'created_by' => $user->id,
                'subject' => $this->subject,
                'category' => $this->category,
                'priority' => $this->priority,
                'status' => SupportTicketStatusEnum::OPEN->value,
                'last_reply_at' => now(),
            ]);

            $ticket->messages()->create([
                'sender_type' => 'teacher',
                'sender_id' => $user->id,
                'body' => $this->message,
            ]);

            return $ticket;
        });

        session()->flash('support-success', 'تم إنشاء التذكرة بنجاح.');
        $this->redirect(route('teacher.support.show', $ticket->id), navigate: true);
    }

    public function render()
    {
        return view('teacher-panel.pages.support.parts.support-ticket-create', [
            'categories' => SupportTicketCategoryEnum::options(),
            'priorities' => SupportTicketPriorityEnum::options(),
        ]);
    }
}
