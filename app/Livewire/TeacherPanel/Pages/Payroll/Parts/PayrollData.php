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
        $this->monthFilter = now()->format('Y-m');
    }

    #[On('payroll-generated')]
    public function refreshData(): void
    {
        // re-render
    }

    public function updatingMonthFilter(): void
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
        $this->resetPage();
    }

    public function render()
    {
        $user = Auth::guard('teacher')->user();
        $ownerId = $user->getOwnerId();
        $accessibleBranches = $user->getAccessibleBranchIds();

        $date = Carbon::createFromFormat('Y-m', $this->monthFilter);
        $periodStart = $date->copy()->startOfMonth();
        $periodEnd = $date->copy()->endOfMonth();

        $query = TeacherPayroll::query()
            ->with(['teacher', 'branch'])
            ->where('teacher_id', $ownerId)
            ->whereDate('period_start', $periodStart->toDateString())
            ->whereDate('period_end', $periodEnd->toDateString());

        if (! $user->isOwner()) {
            empty($accessibleBranches)
                ? $query->whereRaw('1 = 0')
                : $query->where(function ($q) use ($accessibleBranches) {
                    $q->whereIn('branch_id', $accessibleBranches)
                        ->orWhereNull('branch_id');
                });
        }

        $query->when($this->statusFilter !== '', fn ($q) => $q->where('status', $this->statusFilter));
        $query->when($this->branchFilter !== '', fn ($q) => $q->where('branch_id', $this->branchFilter));
        $query->when($this->teacherFilter !== '', fn ($q) => $q->where('teacher_id', $this->teacherFilter));
        $query->when($this->search !== '', fn ($q) => $q->where('reference', 'like', "%{$this->search}%"));

        $payrolls = $query->orderByDesc('created_at')->paginate(15);

        $teachers = Teacher::where(function ($q) use ($ownerId) {
            $q->where('id', $ownerId)->orWhere('owner_id', $ownerId);
        })->orderBy('name')->get();

        return view('teacher-panel.pages.payroll.parts.payroll-data', [
            'payrolls' => $payrolls,
            'teachers' => $teachers,
            'branches' => Branch::whereIn('id', $accessibleBranches)->orderBy('name')->get(),
            'statuses' => TeacherPayrollStatusEnum::options(),
            'canGenerate' => $user->hasPermission(TeacherPermissionEnum::PAYROLL_GENERATE),
            'canMarkPaid' => $user->hasPermission(TeacherPermissionEnum::PAYROLL_MARK_PAID),
        ]);
    }
}
