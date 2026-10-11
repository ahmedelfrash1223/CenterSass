<?php

namespace App\Livewire\TeacherPanel\Pages\Assignment\Parts;

use App\Enums\Teacher\AssignmentStatusEnum;
use App\Enums\Teacher\AssignmentTypeEnum;
use App\Enums\Teacher\TeacherPermissionEnum;
use App\Livewire\TeacherPanel\Concerns\Shared\HasDeleteConfirmation;
use App\Models\Assignment;
use App\Models\Branch;
use App\Models\Group;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class AssignmentData extends Component
{
    use HasDeleteConfirmation;
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public string $search = '';

    public string $statusFilter = '';

    public string $typeFilter = '';

    public string $groupFilter = '';

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

    public function updatingTypeFilter(): void
    {
        $this->resetPage();
    }

    public function updatingGroupFilter(): void
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
        $this->typeFilter = '';
        $this->groupFilter = '';
        $this->branchFilter = '';
        $this->resetPage();
    }

    public function confirmDelete(int $id): void
    {
        if (! Auth::guard('teacher')->user()->hasPermission(TeacherPermissionEnum::ASSIGNMENT_DELETE)) {
            $this->dispatch('show-error-message', message: 'ليس لديك صلاحية لحذف المتابعات.');

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

        if (! $user->hasPermission(TeacherPermissionEnum::ASSIGNMENT_DELETE)) {
            $this->dispatch('show-error-message', message: 'ليس لديك صلاحية لحذف المتابعات.');
            $this->deleteId = null;

            return;
        }

        $assignment = Assignment::where('id', $this->deleteId)
            ->where('teacher_id', $user->getOwnerId())
            ->firstOrFail();

        if (! $user->isOwner() && ! in_array($assignment->branch_id, $user->getAccessibleBranchIds())) {
            $this->dispatch('show-error-message', message: 'ليس لديك صلاحية على هذه المتابعة.');
            $this->deleteId = null;

            return;
        }

        $assignment->delete();
        $this->deleteId = null;
        $this->dispatch('show-success-message', message: 'تم حذف المتابعة بنجاح.');
    }

    public function render()
    {
        $user = Auth::guard('teacher')->user();
        $ownerId = $user->getOwnerId();
        $accessibleBranchIds = $user->getAccessibleBranchIds();

        $query = Assignment::query()
            ->with(['group', 'branch', 'creator'])
            ->withCount(['grades as students_count'])
            ->where('teacher_id', $ownerId);

        if (! $user->isOwner()) {
            empty($accessibleBranchIds)
                ? $query->whereRaw('1 = 0')
                : $query->whereIn('branch_id', $accessibleBranchIds);
        }

        $query->when($this->search !== '', fn ($q) => $q->where('title', 'like', "%{$this->search}%"));

        $query->when($this->statusFilter !== '', fn ($q) => $q->where('status', $this->statusFilter));
        $query->when($this->typeFilter !== '', fn ($q) => $q->where('type', $this->typeFilter));
        $query->when($this->branchFilter !== '', fn ($q) => $q->where('branch_id', $this->branchFilter));
        $query->when($this->groupFilter !== '', fn ($q) => $q->where('group_id', $this->groupFilter));

        $assignments = $query->latest()->paginate(10);

        return view('teacher-panel.pages.assignment.parts.assignment-data', [
            'assignments' => $assignments,
            'groups' => Group::where('teacher_id', $ownerId)
                ->when(! $user->isOwner(), fn ($q) => $q->whereIn('branch_id', $accessibleBranchIds))
                ->orderBy('name')->get(),
            'branches' => Branch::whereIn('id', $accessibleBranchIds)->orderBy('name')->get(),
            'types' => AssignmentTypeEnum::options(),
            'statuses' => AssignmentStatusEnum::options(),
            'canView' => $user->hasPermission(TeacherPermissionEnum::ASSIGNMENT_VIEW),
            'canCreate' => $user->hasPermission(TeacherPermissionEnum::ASSIGNMENT_CREATE),
            'canUpdate' => $user->hasPermission(TeacherPermissionEnum::ASSIGNMENT_UPDATE),
            'canDelete' => $user->hasPermission(TeacherPermissionEnum::ASSIGNMENT_DELETE),
            'canGrade' => $user->hasPermission(TeacherPermissionEnum::ASSIGNMENT_GRADE),
        ]);
    }
}
