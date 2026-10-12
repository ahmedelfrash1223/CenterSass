<?php

namespace App\Livewire\TeacherPanel\Pages\Support\Parts;

use App\Enums\Teacher\SupportTicketStatusEnum;
use App\Enums\Teacher\TeacherPermissionEnum;
use App\Livewire\TeacherPanel\Concerns\Shared\HasDeleteConfirmation;
use App\Models\SupportTicket;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class SupportTicketData extends Component
{
    use HasDeleteConfirmation;
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public string $search = '';

    public string $statusFilter = '';

    protected $listeners = ['refreshData' => '$refresh'];

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingStatusFilter(): void
    {
        $this->resetPage();
    }

    public function clearFilters(): void
    {
        $this->search = '';
        $this->statusFilter = '';
        $this->resetPage();
    }

    public function confirmDelete(int $id): void
    {
        if (! Auth::guard('teacher')->user()->hasPermission(TeacherPermissionEnum::SUPPORT_CLOSE)) {
            $this->dispatch('show-error-message', message: 'ليس لديك صلاحية لإغلاق التذاكر.');

            return;
        }

        $this->deleteId = $id;
    }

    public function delete(): void
    {
        if (! $this->deleteId) {
            return;
        }

        $user = Auth::guard('teacher')->user();

        if (! $user->hasPermission(TeacherPermissionEnum::SUPPORT_CLOSE)) {
            $this->dispatch('show-error-message', message: 'ليس لديك صلاحية لإغلاق التذاكر.');
            $this->deleteId = null;

            return;
        }

        $ticket = SupportTicket::where('id', $this->deleteId)
            ->where('teacher_id', $user->getOwnerId())
            ->firstOrFail();

        if ($ticket->isClosed()) {
            $this->dispatch('show-error-message', message: 'التذكرة مغلقة بالفعل.');
            $this->deleteId = null;

            return;
        }

        $ticket->update([
            'status' => SupportTicketStatusEnum::CLOSED->value,
            'closed_at' => now(),
        ]);

        $this->deleteId = null;
        $this->dispatch('show-success-message', message: 'تم إغلاق التذكرة.');
    }

    public function render()
    {
        $user = Auth::guard('teacher')->user();
        $ownerId = $user->getOwnerId();

        $query = SupportTicket::query()
            ->with(['creator'])
            ->withCount('messages')
            ->where('teacher_id', $ownerId);

        $query->when($this->search !== '', fn ($q) => $q->where(function ($sub) {
            $sub->where('subject', 'like', "%{$this->search}%")
                ->orWhere('reference', 'like', "%{$this->search}%");
        }));

        $query->when($this->statusFilter !== '', fn ($q) => $q->where('status', $this->statusFilter));

        $tickets = $query->orderByDesc('last_reply_at')->orderByDesc('created_at')->paginate(10);

        return view('teacher-panel.pages.support.parts.support-ticket-data', [
            'tickets' => $tickets,
            'statuses' => SupportTicketStatusEnum::options(),
            'canView' => $user->hasPermission(TeacherPermissionEnum::SUPPORT_VIEW),
            'canCreate' => $user->hasPermission(TeacherPermissionEnum::SUPPORT_CREATE),
            'canClose' => $user->hasPermission(TeacherPermissionEnum::SUPPORT_CLOSE),
        ]);
    }
}
