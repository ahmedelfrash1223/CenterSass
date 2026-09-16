<?php

namespace App\Livewire\TeacherPanel\Pages\Staff\Parts;

use App\Enums\Admin\PackageFeatureKeyEnum;
use App\Enums\Teacher\TeacherPermissionEnum;
use App\Enums\Teacher\TeacherRoleEnum;
use App\Livewire\TeacherPanel\Concerns\HasDeleteConfirmation;
use App\Livewire\TeacherPanel\Concerns\HasSubscriptionLimitCheck;
use App\Models\Branch;
use App\Models\Teacher;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class StaffData extends Component
{
    use WithPagination;
    use HasDeleteConfirmation;
    use HasSubscriptionLimitCheck;

    protected $paginationTheme = 'bootstrap';

    public string $search = '';
    public string $roleFilter = '';
    public string $statusFilter = '';
    public string $branchFilter = '';

    protected $listeners = ['refreshData' => '$refresh'];

    public function updatingSearch(): void { $this->resetPage(); }
    public function updatingRoleFilter(): void { $this->resetPage(); }
    public function updatingStatusFilter(): void { $this->resetPage(); }
    public function updatingBranchFilter(): void { $this->resetPage(); }

    public function clearFilters(): void
    {
        $this->search = '';
        $this->roleFilter = '';
        $this->statusFilter = '';
        $this->branchFilter = '';
        $this->resetPage();
    }

    public function confirmDelete(int $id): void
    {
        if (!Auth::guard('teacher')->user()->hasPermission(TeacherPermissionEnum::STAFF_DELETE)) {
            $this->dispatch('show-error-message', message: 'ليس لديك صلاحية لحذف أعضاء الفريق.');
            return;
        }

        $this->deleteId = $id;
    }

    public function delete(): void
    {
        if (!$this->deleteId) {
            return;
        }

        $user = Auth::guard('teacher')->user();

        if (!$user->hasPermission(TeacherPermissionEnum::STAFF_DELETE)) {
            $this->dispatch('show-error-message', message: 'ليس لديك صلاحية لحذف أعضاء الفريق.');
            $this->deleteId = null;
            return;
        }

        $ownerId = $user->getOwnerId();

        // Block deleting the owner account
        if ($this->deleteId === $ownerId) {
            $this->dispatch('show-error-message', message: 'لا يمكن حذف صاحب الحساب الرئيسي.');
            $this->deleteId = null;
            return;
        }

        $member = Teacher::where('id', $this->deleteId)
            ->where('owner_id', $ownerId)
            ->firstOrFail();

        if (!$user->hasAccessToTeacher($member)) {
            $this->dispatch('show-error-message', message: 'ليس لديك صلاحية على هذا العضو.');
            $this->deleteId = null;
            return;
        }

        $member->delete();
        $this->deleteId = null;
        $this->dispatch('show-success-message', message: 'تم حذف عضو الفريق بنجاح.');
    }

    public function render()
    {
        $user = Auth::guard('teacher')->user();
        $ownerId = $user->getOwnerId();
        $isOwner = $user->isOwner();
        $accessibleBranchIds = $user->getAccessibleBranchIds();

        $query = Teacher::query()
            ->with(['creator', 'branches'])
            ->where(function ($q) use ($ownerId) {
                $q->where('id', $ownerId)->orWhere('owner_id', $ownerId);
            });

        // Non-owner: only self, owner, or staff in shared branches
        if (!$isOwner) {
            if (empty($accessibleBranchIds)) {
                $query->where('id', $user->id);
            } else {
                $query->where(function ($q) use ($user, $accessibleBranchIds) {
                    $q->where('id', $user->id)
                        ->orWhere('id', $user->getOwnerId())
                        ->orWhereHas('branches', fn($sub) => $sub->whereIn('branches.id', $accessibleBranchIds));
                });
            }
        }

        $query->when($this->search !== '', fn($q) => $q->where(function ($sub) {
            $sub->where('name', 'like', "%{$this->search}%")
                ->orWhere('email', 'like', "%{$this->search}%")
                ->orWhere('username', 'like', "%{$this->search}%")
                ->orWhere('phone', 'like', "%{$this->search}%");
        }));

        $query->when($this->roleFilter !== '', fn($q) => $q->where('role', $this->roleFilter));
        $query->when($this->statusFilter !== '', fn($q) => $q->where('is_active', $this->statusFilter));
        $query->when($this->branchFilter !== '', fn($q) => $q->whereHas('branches', fn($sub) => $sub->where('branches.id', $this->branchFilter)));

        $members = $query
            ->orderByRaw('CASE WHEN role = 1 THEN 1 WHEN role = 2 THEN 2 ELSE 3 END')
            ->orderBy('name')
            ->paginate(10);

        $total = Teacher::where('owner_id', $ownerId)->count();

        return view('teacher-panel.pages.staff.parts.staff-data', [
            'members' => $members,
            'roles' => TeacherRoleEnum::options(),
            'branches' => Branch::whereIn('id', $accessibleBranchIds)->orderBy('name')->get(),
            'currentTeacher' => $user,
            'ownerId' => $ownerId,
            'isOwner' => $isOwner,
            'limit' => $this->getLimitData(PackageFeatureKeyEnum::MAX_EMPLOYEES->value, $total),
            'totalStaff' => $total,
            'canView' => $user->hasPermission(TeacherPermissionEnum::STAFF_VIEW),
            'canCreate' => $user->hasPermission(TeacherPermissionEnum::STAFF_CREATE),
            'canUpdate' => $user->hasPermission(TeacherPermissionEnum::STAFF_UPDATE),
            'canDelete' => $user->hasPermission(TeacherPermissionEnum::STAFF_DELETE),
        ]);
    }
}
