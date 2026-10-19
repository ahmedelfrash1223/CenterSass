<?php

namespace App\Livewire\TeacherPanel\Pages\Payroll\Parts;

use App\Enums\Teacher\TeacherPayrollStatusEnum;
use App\Enums\Teacher\TeacherPermissionEnum;
use App\Models\Branch;
use App\Models\Teacher;
use App\Models\TeacherPayroll;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class PayrollData extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public string $monthFilter = '';

    public string $statusFilter = '';

    public string $branchFilter = '';

    public string $teacherFilter = '';

    public string $search = '';

    public function mount(): void
    {
        $this->monthFilter = session('payroll.selected_month', now()->format('Y-m'));
    }

    public function updatedMonthFilter(): void
    {
        session(['payroll.selected_month' => $this->monthFilter]);
        $this->resetPage();
    }

    #[On('payroll-generated')]
    public function refreshData(): void
    {
        // re-render
    }

    public function updatingStatusFilter(): void
    {
        $this->resetPage();
    }

    public function updatingBranchFilter(): void
    {
        $this->resetPage();
    }

    public function updatingTeacherFilter(): void
    {
        $this->resetPage();
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function clearFilters(): void
    {
        $this->monthFilter = now()->format('Y-m');
        $this->statusFilter = '';
        $this->branchFilter = '';
        $this->teacherFilter = '';
        $this->search = '';
        session(['payroll.selected_month' => $this->monthFilter]);
        $this->resetPage();
    }

    public function resetToCurrentMonth(): void
    {
        $this->monthFilter = now()->format('Y-m');
        session(['payroll.selected_month' => $this->monthFilter]);
        $this->resetPage();
    }

    public function render()
    {
        $user = Auth::guard('teacher')->user();
        $ownerId = $user->getOwnerId();
        $accessibleBranches = $user->getAccessibleBranchIds();

        $date = Carbon::createFromFormat('Y-m', $this->monthFilter);
        $year = $date->year;
        $month = $date->month;

        $query = TeacherPayroll::query()
            ->with(['teacher', 'branch'])
            ->whereHas('teacher', fn ($q) => $q->where('id', $ownerId)->orWhere('owner_id', $ownerId))
            ->whereYear('period_start', $year)
            ->whereMonth('period_start', $month);

        if (! $user->isOwner()) {
            empty($accessibleBranches)
                ? $query->whereRaw('1 = 0')
                : $query->whereIn('branch_id', $accessibleBranches);
        }

        $query->when($this->statusFilter !== '', fn ($q) => $q->where('status', $this->statusFilter));
        $query->when($this->branchFilter !== '', fn ($q) => $q->where('branch_id', $this->branchFilter));
        $query->when($this->teacherFilter !== '', fn ($q) => $q->where('teacher_id', $this->teacherFilter));

        $query->when($this->search !== '', function ($q) {
            $q->where(function ($sub) {
                $sub->whereHas('teacher', fn ($t) => $t->where('name', 'like', "%{$this->search}%"))
                    ->orWhereHas('branch', fn ($b) => $b->where('name', 'like', "%{$this->search}%"));
            });
        });

        $payrolls = $query->orderByDesc('created_at')->paginate(15);

        $teachersQuery = Teacher::where(function ($q) use ($ownerId) {
            $q->where('id', $ownerId)->orWhere('owner_id', $ownerId);
        });

        if (! $user->isOwner()) {
            $teachersQuery->whereHas('branches', fn ($q) => $q->whereIn('branches.id', $accessibleBranches));
        }

        $teachers = $teachersQuery->orderBy('name')->get();

        return view('teacher-panel.pages.payroll.parts.payroll-data', [
            'payrolls' => $payrolls,
            'teachers' => $teachers,
            'branches' => Branch::whereIn('id', $accessibleBranches)->orderBy('name')->get(),
            'statuses' => TeacherPayrollStatusEnum::options(),
            'canGenerate' => $user->hasPermission(TeacherPermissionEnum::PAYROLL_GENERATE),
            'canMarkPaid' => $user->hasPermission(TeacherPermissionEnum::PAYROLL_MARK_PAID),
            'isCurrentMonth' => $this->monthFilter === now()->format('Y-m'),
        ]);
    }
}
