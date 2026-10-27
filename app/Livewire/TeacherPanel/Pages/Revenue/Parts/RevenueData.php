<?php

namespace App\Livewire\TeacherPanel\Pages\Revenue\Parts;

use App\Enums\Teacher\RevenueSourceEnum;
use App\Enums\Teacher\TeacherPermissionEnum;
use App\Livewire\TeacherPanel\Concerns\Shared\HasDeleteConfirmation;
use App\Models\Branch;
use App\Models\Revenue;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class RevenueData extends Component
{
    use HasDeleteConfirmation;
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public string $month = '';

    public string $sourceFilter = '';

    public string $branchFilter = '';

    public string $search = '';

    public function mount(): void
    {
        $this->month = session('teacher.revenues.selected_month', now()->format('Y-m'));

        $this->branchFilter = (string) session('teacher.payments.selected_branch', '');
    }

    #[On('month-changed')]
    public function onMonthChanged(string $month): void
    {
        $this->month = $month;
        $this->resetPage();
    }


    #[On('branch-changed')]
    public function onBranchChanged(string $branchId): void
    {
        $this->branchFilter = $branchId;
        $this->resetPage();
    }

    #[On('revenue-recorded')]
    public function refreshData(): void
    {
        // re-render
    }

    public function updatingSourceFilter(): void
    {
        $this->resetPage();
    }

    public function updatingBranchFilter(): void
    {
        $this->resetPage();
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function clearFilters(): void
    {
        $this->sourceFilter = '';
        $this->branchFilter = '';
        $this->search = '';
        $this->resetPage();
    }

    public function confirmDelete(int $id): void
    {
        if (! Auth::guard('teacher')->user()->hasPermission(TeacherPermissionEnum::REVENUE_DELETE)) {
            $this->dispatch('show-error-message', message: 'ليس لديك صلاحية لحذف الإيرادات.');

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

        if (! $user->hasPermission(TeacherPermissionEnum::REVENUE_DELETE)) {
            $this->dispatch('show-error-message', message: 'ليس لديك صلاحية لحذف الإيرادات.');
            $this->deleteId = null;

            return;
        }

        $revenue = Revenue::where('id', $this->deleteId)
            ->where('teacher_id', $user->getOwnerId())
            ->firstOrFail();

        if (! $user->isOwner() && ! in_array($revenue->branch_id, $user->getAccessibleBranchIds())) {
            $this->dispatch('show-error-message', message: 'ليس لديك صلاحية على هذا الإيراد.');
            $this->deleteId = null;

            return;
        }

        $revenue->delete();
        $this->deleteId = null;

        $this->dispatch('show-success-message', message: 'تم حذف الإيراد.');
    }

    public function render()
    {
        $user = Auth::guard('teacher')->user();
        $ownerId = $user->getOwnerId();
        $accessibleBranches = $user->getAccessibleBranchIds();

        $date = Carbon::createFromFormat('Y-m', $this->month);
        $periodStart = $date->copy()->startOfMonth();
        $periodEnd = $date->copy()->endOfMonth();

        $baseQuery = Revenue::query()
            ->with(['student', 'branch', 'recordedBy'])
            ->where('teacher_id', $ownerId)
            ->whereBetween('occurred_at', [$periodStart, $periodEnd]);

        if (! $user->isOwner()) {
            empty($accessibleBranches)
                ? $baseQuery->whereRaw('1 = 0')
                : $baseQuery->whereIn('branch_id', $accessibleBranches);
        }

        $listQuery = (clone $baseQuery)
            ->when($this->sourceFilter !== '', fn ($q) => $q->where('source', $this->sourceFilter))
            ->when($this->branchFilter !== '', fn ($q) => $q->where('branch_id', $this->branchFilter))
            ->when($this->search !== '', fn ($q) => $q->where(function ($sub) {
                $sub->where('reference', 'like', "%{$this->search}%")
                    ->orWhere('description', 'like', "%{$this->search}%");
            }));

        $revenues = $listQuery->orderByDesc('occurred_at')->paginate(15);

        $totalsQuery = (clone $baseQuery)
            ->when($this->branchFilter !== '', fn ($q) => $q->where('branch_id', $this->branchFilter));

        $totalsBySource = $totalsQuery
            ->selectRaw('source, SUM(amount) as total, COUNT(*) as count')
            ->groupBy('source')
            ->get()
            ->keyBy('source');

        $grandTotal = (float) $totalsBySource->sum('total');

        return view('teacher-panel.pages.revenue.parts.revenue-data', [
            'revenues' => $revenues,
            'totalsBySource' => $totalsBySource,
            'grandTotal' => $grandTotal,
            'branches' => Branch::whereIn('id', $accessibleBranches)->orderBy('name')->get(),
            'sources' => RevenueSourceEnum::options(),
            'canCreate' => $user->hasPermission(TeacherPermissionEnum::REVENUE_CREATE),
            'canDelete' => $user->hasPermission(TeacherPermissionEnum::REVENUE_DELETE),
        ]);
    }
}
