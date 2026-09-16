<?php

namespace App\Livewire\TeacherPanel\Pages\Group\Parts;

use App\Enums\Admin\PackageFeatureKeyEnum;
use App\Enums\Teacher\GroupStatusEnum;
use App\Enums\Teacher\TeacherPermissionEnum;
use App\Livewire\TeacherPanel\Concerns\HasDeleteConfirmation;
use App\Livewire\TeacherPanel\Concerns\HasSubscriptionLimitCheck;
use App\Models\Branch;
use App\Models\Group;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class GroupData extends Component
{
    use WithPagination;
    use HasDeleteConfirmation;
    use HasSubscriptionLimitCheck;

    protected $paginationTheme = 'bootstrap';

    public string $search = '';
    public string $branchFilter = '';
    public string $statusFilter = 'active';

    protected $listeners = ['refreshData' => '$refresh'];

    public function updatingSearch(): void { $this->resetPage(); }
    public function updatingBranchFilter(): void { $this->resetPage(); }
    public function updatingStatusFilter(): void { $this->resetPage(); }

    public function clearFilters(): void
    {
        $this->search = '';
        $this->branchFilter = '';
        $this->statusFilter = '';
        $this->resetPage();
    }

    public function confirmDelete(int $id): void
    {
        if (!Auth::guard('teacher')->user()->hasPermission(TeacherPermissionEnum::GROUP_DELETE)) {
            $this->dispatch('show-error-message', message: 'ليس لديك صلاحية لحذف المجموعات.');
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

        if (!$user->hasPermission(TeacherPermissionEnum::GROUP_DELETE)) {
            $this->dispatch('show-error-message', message: 'ليس لديك صلاحية لحذف المجموعات.');
            $this->deleteId = null;
            return;
        }

        $group = Group::where('id', $this->deleteId)
            ->where('teacher_id', $user->getOwnerId())
            ->firstOrFail();

        // Non-owner: ensure the group branch is accessible
        if (!$user->isOwner() && !in_array($group->branch_id, $user->getAccessibleBranchIds())) {
            $this->dispatch('show-error-message', message: 'ليس لديك صلاحية على هذه المجموعة.');
            $this->deleteId = null;
            return;
        }

        $group->delete();

        $this->deleteId = null;
        $this->dispatch('show-success-message', message: 'تم حذف المجموعة بنجاح.');
    }

    public function render()
    {
        $user = Auth::guard('teacher')->user();
        $ownerId = $user->getOwnerId();
        $accessibleBranchIds = $user->getAccessibleBranchIds();

        $query = Group::query()
            ->with(['branch', 'creator'])
            ->withCount(['students' => fn($q) => $q->where('group_student.status', 'active')])
            ->where('teacher_id', $ownerId);

        // Scope non-owners to their accessible branches
        if (!$user->isOwner()) {
            empty($accessibleBranchIds)
                ? $query->whereRaw('1 = 0')
                : $query->whereIn('branch_id', $accessibleBranchIds);
        }

        $query->when($this->search !== '', fn($q) => $q->where('name', 'like', "%{$this->search}%"));
        $query->when($this->branchFilter !== '', fn($q) => $q->where('branch_id', $this->branchFilter));
        $query->when($this->statusFilter !== '', fn($q) => $q->where('status', $this->statusFilter));

        $groups = $query->orderBy('name')->paginate(10);

        $total = Group::where('teacher_id', $ownerId)->count();

        return view('teacher-panel.pages.group.parts.group-data', [
            'groups' => $groups,
            'branches' => Branch::whereIn('id', $accessibleBranchIds)->orderBy('name')->get(),
            'statuses' => GroupStatusEnum::options(),
            'currentTeacher' => $user,
            'ownerId' => $ownerId,
            'isOwner' => $user->isOwner(),
            'limit' => $this->getLimitData(PackageFeatureKeyEnum::MAX_GROUPS->value, $total),
            'totalGroups' => $total,
            'canView' => $user->hasPermission(TeacherPermissionEnum::GROUP_VIEW),
            'canCreate' => $user->hasPermission(TeacherPermissionEnum::GROUP_CREATE),
            'canUpdate' => $user->hasPermission(TeacherPermissionEnum::GROUP_UPDATE),
            'canDelete' => $user->hasPermission(TeacherPermissionEnum::GROUP_DELETE),
        ]);
    }
}
