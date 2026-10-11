<?php

namespace App\Livewire\TeacherPanel\Pages\Booking\Parts;

use App\Enums\Teacher\TeacherPermissionEnum;
use App\Livewire\TeacherPanel\Concerns\Shared\HasDeleteConfirmation;
use App\Models\Booking;
use App\Models\Branch;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class BookingData extends Component
{
    use HasDeleteConfirmation;
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public string $search = '';

    public string $statusFilter = 'pending';

    public string $branchFilter = '';

    protected $listeners = ['refreshData' => '$refresh'];

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingStatusFilter(): void
    {
        $this->resetPage();
    }

    public function updatingBranchFilter(): void
    {
        $this->resetPage();
    }

    public function clearFilters(): void
    {
        $this->search = '';
        $this->statusFilter = '';
        $this->branchFilter = '';
        $this->resetPage();
    }

    public function confirmDelete(int $id): void
    {
        if (! Auth::guard('teacher')->user()->hasPermission(TeacherPermissionEnum::BOOKING_DELETE)) {
            $this->dispatch('show-error-message', message: 'ليس لديك صلاحية لحذف الحجوزات.');

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

        if (! $user->hasPermission(TeacherPermissionEnum::BOOKING_DELETE)) {
            $this->dispatch('show-error-message', message: 'ليس لديك صلاحية لحذف الحجوزات.');
            $this->deleteId = null;

            return;
        }

        $booking = Booking::where('id', $this->deleteId)
            ->where('teacher_id', $user->getOwnerId())
            ->firstOrFail();

        if (! $user->isOwner() && ! in_array($booking->branch_id, $user->getAccessibleBranchIds())) {
            $this->dispatch('show-error-message', message: 'ليس لديك صلاحية على هذا الحجز.');
            $this->deleteId = null;

            return;
        }

        $booking->delete();
        $this->deleteId = null;
        $this->dispatch('show-success-message', message: 'تم حذف الحجز بنجاح.');
    }

    public function render()
    {
        $user = Auth::guard('teacher')->user();
        $ownerId = $user->getOwnerId();
        $accessibleBranchIds = $user->getAccessibleBranchIds();

        $query = Booking::query()
            ->with(['branch', 'creator'])
            ->where('teacher_id', $ownerId);

        if (! $user->isOwner()) {
            empty($accessibleBranchIds)
                ? $query->whereRaw('1 = 0')
                : $query->whereIn('branch_id', $accessibleBranchIds);
        }

        $query->when($this->search !== '', fn ($q) => $q->where(function ($sub) {
            $sub->where('name', 'like', "%{$this->search}%")
                ->orWhere('phone', 'like', "%{$this->search}%");
        }));

        $query->when($this->statusFilter !== '', fn ($q) => $q->where('status', $this->statusFilter));
        $query->when($this->branchFilter !== '', fn ($q) => $q->where('branch_id', $this->branchFilter));

        $bookings = $query->latest()->paginate(10);

        return view('teacher-panel.pages.booking.parts.booking-data', [
            'bookings' => $bookings,
            'branches' => Branch::whereIn('id', $accessibleBranchIds)->orderBy('name')->get(),
            'canView' => $user->hasPermission(TeacherPermissionEnum::BOOKING_VIEW),
            'canCreate' => $user->hasPermission(TeacherPermissionEnum::BOOKING_CREATE),
            'canUpdate' => $user->hasPermission(TeacherPermissionEnum::BOOKING_UPDATE),
            'canDelete' => $user->hasPermission(TeacherPermissionEnum::BOOKING_DELETE),
            'canConfirm' => $user->hasPermission(TeacherPermissionEnum::BOOKING_CONFIRM),
        ]);
    }
}
