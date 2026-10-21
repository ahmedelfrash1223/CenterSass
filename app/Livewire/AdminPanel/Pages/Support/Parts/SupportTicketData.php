<?php

namespace App\Livewire\AdminPanel\Pages\Support\Parts;

use App\Enums\Admin\SupportTicketCategoryEnum;
use App\Enums\Admin\SupportTicketPriorityEnum;
use App\Enums\Admin\SupportTicketStatusEnum;
use App\Models\SupportTicket;
use Livewire\Component;
use Livewire\WithPagination;

class SupportTicketData extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public string $search = '';
    public string $statusFilter = '';
    public string $priorityFilter = '';
    public string $categoryFilter = '';
    public ?int $deleteId = null;

    protected $listeners = ['refreshData' => '$refresh'];

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingStatusFilter(): void
    {
        $this->resetPage();
    }

    public function updatingPriorityFilter(): void
    {
        $this->resetPage();
    }

    public function updatingCategoryFilter(): void
    {
        $this->resetPage();
    }

    public function clearFilters(): void
    {
        $this->search = '';
        $this->statusFilter = '';
        $this->priorityFilter = '';
        $this->categoryFilter = '';
        $this->resetPage();
    }

    public function confirmDelete(int $id): void
    {
        $this->deleteId = $id;
    }

    public function cancelDelete(): void
    {
        $this->deleteId = null;
    }

    public function delete(): void
    {
        if (! $this->deleteId) {
            return;
        }

        $ticket = SupportTicket::findOrFail($this->deleteId);

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
        $query = SupportTicket::query()
            ->with(['creator', 'teacher'])
            ->withCount('messages');

        $query->when($this->search !== '', fn ($q) => $q->where(function ($sub) {
            $sub->where('subject', 'like', "%{$this->search}%")
                ->orWhere('reference', 'like', "%{$this->search}%");
        }));

        $query->when($this->statusFilter !== '', fn ($q) => $q->where('status', $this->statusFilter));
        $query->when($this->priorityFilter !== '', fn ($q) => $q->where('priority', $this->priorityFilter));
        $query->when($this->categoryFilter !== '', fn ($q) => $q->where('category', $this->categoryFilter));

        $tickets = $query
            ->orderByDesc('last_reply_at')
            ->orderByDesc('created_at')
            ->paginate(15);

        return view('admin-panel.pages.support.parts.support-ticket-data', [
            'tickets' => $tickets,
            'statuses' => SupportTicketStatusEnum::options(),
            'priorities' => SupportTicketPriorityEnum::options(),
            'categories' => SupportTicketCategoryEnum::options(),
        ]);
    }
}
