<?php

namespace App\Livewire\TeacherPanel\Pages\CardRequest\Parts;

use App\Enums\Teacher\CardRequestStatusEnum;
use App\Enums\Teacher\TeacherPermissionEnum;
use App\Livewire\TeacherPanel\Concerns\Shared\HasDeleteConfirmation;
use App\Models\CardRequest;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class CardRequestData extends Component
{
    use HasDeleteConfirmation;
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public string $statusFilter = '';

    public string $search = '';

    protected $listeners = ['refreshData' => '$refresh'];

    public function updatingStatusFilter(): void
    {
        $this->resetPage();
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function clearFilters(): void
    {
        $this->statusFilter = '';
        $this->search = '';
        $this->resetPage();
    }

    public function confirmDelete(int $id): void
    {
        if (! Auth::guard('teacher')->user()->hasPermission(TeacherPermissionEnum::CARD_REQUEST_DELETE)) {
            $this->dispatch('show-error-message', message: 'ليس لديك صلاحية لإلغاء الطلبات.');

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

        if (! $user->hasPermission(TeacherPermissionEnum::CARD_REQUEST_DELETE)) {
            $this->dispatch('show-error-message', message: 'ليس لديك صلاحية لإلغاء الطلبات.');
            $this->deleteId = null;

            return;
        }

        $request = CardRequest::where('id', $this->deleteId)
            ->where('teacher_id', $user->getOwnerId())
            ->firstOrFail();

        if (! $request->canBeCancelled()) {
            $this->dispatch('show-error-message', message: 'لا يمكن إلغاء الطلب في حالته الحالية.');
            $this->deleteId = null;

            return;
        }

        $request->delete();
        $this->deleteId = null;
        $this->dispatch('show-success-message', message: 'تم إلغاء الطلب بنجاح.');
    }

    public function render()
    {
        $user = Auth::guard('teacher')->user();
        $ownerId = $user->getOwnerId();

        $query = CardRequest::query()
            ->with(['creator'])
            ->withCount('items')
            ->where('teacher_id', $ownerId);

        $query->when($this->search !== '', fn ($q) => $q->where('reference', 'like', "%{$this->search}%"));
        $query->when($this->statusFilter !== '', fn ($q) => $q->where('status', $this->statusFilter));

        $requests = $query->latest()->paginate(10);

        return view('teacher-panel.pages.card-request.parts.card-request-data', [
            'requests' => $requests,
            'statuses' => CardRequestStatusEnum::options(),
            'canView' => $user->hasPermission(TeacherPermissionEnum::CARD_REQUEST_VIEW),
            'canCreate' => $user->hasPermission(TeacherPermissionEnum::CARD_REQUEST_CREATE),
            'canDelete' => $user->hasPermission(TeacherPermissionEnum::CARD_REQUEST_DELETE),
        ]);
    }
}
