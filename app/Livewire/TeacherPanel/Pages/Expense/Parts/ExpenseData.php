<?php

namespace App\Livewire\TeacherPanel\Pages\Expense\Parts;

use App\Enums\Teacher\ExpenseSourceEnum;
use App\Models\Branch;
use App\Models\Expense;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class ExpenseData extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public string $month = '';

    public string $sourceFilter = '';

    public string $branchFilter = '';

    public string $search = '';

    public function mount(): void
    {
        $this->month = session('teacher.expenses.selected_month', now()->format('Y-m'));
    }

    #[On('month-changed')]
    public function onMonthChanged(string $month): void
    {
        $this->month = $month;
        $this->resetPage();
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

    public function render()
    {
        $user = Auth::guard('teacher')->user();
        $ownerId = $user->getOwnerId();
        $accessibleBranches = $user->getAccessibleBranchIds();

        $date = Carbon::createFromFormat('Y-m', $this->month);
        $periodStart = $date->copy()->startOfMonth();
        $periodEnd = $date->copy()->endOfMonth();

        $query = Expense::query()
            ->with(['teacherPayee', 'branch', 'recordedBy'])
            ->where('teacher_id', $ownerId)
            ->whereBetween('occurred_at', [$periodStart, $periodEnd]);

        if (! $user->isOwner()) {
            empty($accessibleBranches)
                ? $query->whereRaw('1 = 0')
                : $query->whereIn('branch_id', $accessibleBranches);
        }

        $query->when($this->sourceFilter !== '', fn ($q) => $q->where('source', $this->sourceFilter));
        $query->when($this->branchFilter !== '', fn ($q) => $q->where('branch_id', $this->branchFilter));
        $query->when($this->search !== '', fn ($q) => $q->where(function ($sub) {
            $sub->where('reference', 'like', "%{$this->search}%")
                ->orWhere('description', 'like', "%{$this->search}%");
        }));

        $expenses = $query->orderByDesc('occurred_at')->paginate(15);

        $totalsBySource = Expense::query()
            ->selectRaw('source, SUM(amount) as total, COUNT(*) as count')
            ->where('teacher_id', $ownerId)
            ->whereBetween('occurred_at', [$periodStart, $periodEnd])
            ->when(! $user->isOwner(), fn ($q) => $q->whereIn('branch_id', $accessibleBranches))
            ->groupBy('source')
            ->get()
            ->keyBy('source');

        $grandTotal = (float) $totalsBySource->sum('total');

        return view('teacher-panel.pages.expense.parts.expense-data', [
            'expenses' => $expenses,
            'totalsBySource' => $totalsBySource,
            'grandTotal' => $grandTotal,
            'branches' => Branch::whereIn('id', $accessibleBranches)->orderBy('name')->get(),
            'sources' => ExpenseSourceEnum::options(),
        ]);
    }
}
