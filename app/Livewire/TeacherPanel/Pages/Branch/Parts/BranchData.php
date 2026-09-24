<?php

namespace App\Livewire\TeacherPanel\Pages\Branch\Parts;

use App\Enums\Admin\PackageFeatureKeyEnum;
use App\Enums\Teacher\TeacherPermissionEnum;
use App\Livewire\TeacherPanel\Concerns\Shared\HasDeleteConfirmation;
use App\Livewire\TeacherPanel\Concerns\Shared\HasSubscriptionLimitCheck;
use App\Models\Branch;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class BranchData extends Component
{
    use WithPagination;
    use HasDeleteConfirmation;
    use HasSubscriptionLimitCheck;

    protected $paginationTheme = 'bootstrap';

    public string $search = '';
    public string $status = '';

    protected $listeners = ['refreshData' => '$refresh'];

    public function updatingSearch(): void { $this->resetPage(); }
    public function updatedStatus(): void { $this->resetPage(); }

    public function clearFilters(): void
    {
        $this->search = '';
        $this->status = '';
        $this->resetPage();
    }

    public function confirmDelete(int $id): void
    {
        if (!Auth::guard('teacher')->user()->hasPermission(TeacherPermissionEnum::BRANCH_DELETE)) {
            $this->dispatch('show-error-message', message: 'ليس لديك صلاحية لحذف الفروع.');
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

        if (!$user->hasPermission(TeacherPermissionEnum::BRANCH_DELETE)) {
            $this->dispatch('show-error-message', message: 'ليس لديك صلاحية لحذف الفروع.');
            $this->deleteId = null;
            return;
        }

        Branch::where('teacher_id', $user->getOwnerId())
            ->findOrFail($this->deleteId)
            ->delete();

        $this->deleteId = null;
        $this->dispatch('show-success-message', message: 'تم حذف الفرع بنجاح.');
    }

    public function render()
    {
        $user = Auth::guard('teacher')->user();
        $ownerId = $user->getOwnerId();

        $branches = Branch::query()
            ->with('creator')
            ->where('teacher_id', $ownerId)
            ->when($this->search !== '', fn($q) => $q->where(function ($q) {
                $q->where('name', 'like', "%{$this->search}%")
                    ->orWhere('address', 'like', "%{$this->search}%")
                    ->orWhere('phone', 'like', "%{$this->search}%")
                    ->orWhere('email', 'like', "%{$this->search}%");
            }))
            ->when($this->status !== '', fn($q) => $q->where('is_active', $this->status))
            ->latest()
            ->paginate(10);

        $total = Branch::where('teacher_id', $ownerId)->count();
        $limit = $this->getLimitData(PackageFeatureKeyEnum::MAX_BRANCHES->value, $total);

        return view('teacher-panel.pages.branch.parts.branch-data', [
            'branches' => $branches,
            'limit' => $limit,
            'totalBranches' => $total,
            'canCreate' => $user->hasPermission(TeacherPermissionEnum::BRANCH_CREATE),
            'canUpdate' => $user->hasPermission(TeacherPermissionEnum::BRANCH_UPDATE),
            'canDelete' => $user->hasPermission(TeacherPermissionEnum::BRANCH_DELETE),
        ]);
    }
}
