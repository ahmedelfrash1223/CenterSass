<?php

namespace App\Livewire\AdminPanel\Pages\CardRequest\Parts;

use App\Enums\Teacher\CardRequestStatusEnum;
use App\Enums\Teacher\TeacherRoleEnum;
use App\Models\CardRequest;
use App\Models\Teacher;
use Livewire\Component;
use Livewire\WithPagination;

class CardRequestData extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public string $search = '';

    public string $statusFilter = '';

    public string $teacherFilter = '';

    public ?int $cancelId = null;

    protected $listeners = ['refreshData' => '$refresh'];

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingStatusFilter(): void
    {
        $this->resetPage();
    }

    public function updatingTeacherFilter(): void
    {
        $this->resetPage();
    }

    public function clearFilters(): void
    {
        $this->search = '';
        $this->statusFilter = '';
        $this->teacherFilter = '';
        $this->resetPage();
    }

    public function confirmCancel(int $id): void
    {
        $this->cancelId = $id;
    }

    public function cancelConfirmation(): void
    {
        $this->cancelId = null;
    }

    public function cancelRequest(): void
    {
        if (! $this->cancelId) {
            return;
        }

        $cardRequest = CardRequest::findOrFail($this->cancelId);

        if (! $cardRequest->canBeCancelled()) {
            $this->dispatch('show-error-message', message: 'لا يمكن إلغاء الطلب في حالته الحالية.');
            $this->cancelId = null;

            return;
        }

        $cardRequest->update(['status' => CardRequestStatusEnum::CANCELLED->value]);
        $this->cancelId = null;

        $this->dispatch('show-success-message', message: 'تم إلغاء الطلب بنجاح.');
    }

    public function render()
    {
        $query = CardRequest::query()
            ->with(['creator', 'teacher'])
            ->withCount('items');

        $query->when($this->search !== '', fn ($builder) => $builder->where('reference', 'like', "%{$this->search}%")
        );

        $query->when($this->statusFilter !== '', fn ($builder) => $builder->where('status', $this->statusFilter)
        );

        $query->when($this->teacherFilter !== '', fn ($builder) => $builder->whereHas(
            'teacher',
            fn ($teacherQuery) => $teacherQuery->where('id', $this->teacherFilter)
        )
        );

        return view('admin-panel.pages.card-request.parts.card-request-data', [
            'requests' => $query->latest('created_at')->paginate(15),
            'statuses' => CardRequestStatusEnum::options(),
            'teachers' => Teacher::query()
                ->whereIn('role', [TeacherRoleEnum::OWNER->value, TeacherRoleEnum::OWNER])
                ->orderBy('name')
                ->get(['id', 'name']),
        ]);
    }
}
