<?php

    namespace App\Livewire\TeacherPanel\Pages\Expense\Parts;

    use App\Enums\Teacher\ExpenseSourceEnum;
    use App\Http\Requests\TeacherPanel\StoreOtherExpenseRequest;
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

        public bool $showOtherForm = false;

        public string $otherSource = '';

        public string $otherReason = '';

        public string $otherDescription = '';

        public string $otherAmount = '';

        public string $otherBranchId = '';

        public string $otherOccurredAt = '';

        public function mount(): void
        {
            $this->month = session('teacher.expenses.selected_month', now()->format('Y-m'));
            $this->otherOccurredAt = now()->format('Y-m-d\TH:i');
        }

        #[On('month-changed')]
        public function onMonthChanged(string $month): void
        {
            $this->month = $month;
            $this->resetPage('otherPage');
            $this->resetPage('teacherPage');
        }

        public function updatingSourceFilter(): void
        {
            $this->resetPage('otherPage');
            $this->resetPage('teacherPage');
        }

        public function updatingBranchFilter(): void
        {
            $this->resetPage('otherPage');
            $this->resetPage('teacherPage');
        }

        public function updatingSearch(): void
        {
            $this->resetPage('otherPage');
            $this->resetPage('teacherPage');
        }

        public function clearFilters(): void
        {
            $this->sourceFilter = '';
            $this->branchFilter = '';
            $this->search = '';
            $this->resetPage('otherPage');
            $this->resetPage('teacherPage');
        }

        public function toggleOtherForm(): void
        {
            $this->showOtherForm = ! $this->showOtherForm;

            if (! $this->showOtherForm) {
                $this->resetOtherForm();
            }
        }

        public function resetOtherForm(): void
        {
            $this->otherSource = '';
            $this->otherReason = '';
            $this->otherDescription = '';
            $this->otherAmount = '';
            $this->otherBranchId = '';
            $this->otherOccurredAt = now()->format('Y-m-d\TH:i');
            $this->resetValidation();
        }

        public function saveOtherExpense(): void
        {
            $request = new StoreOtherExpenseRequest;

            $this->validate($request->rules(), $request->messages(), $request->attributes());

            $user = Auth::guard('teacher')->user();
            $ownerId = $user->getOwnerId();

            Expense::create([
                'teacher_id' => $ownerId,
                'source' => $this->otherSource,
                'recorded_by' => $user->id,
                'amount' => (float) $this->otherAmount,
                'currency' => 'EGP',
                'branch_id' => $this->otherBranchId !== '' ? (int) $this->otherBranchId : null,
                'description' => $this->otherDescription ?: null,
                'meta' => [
                    'reason' => $this->otherReason,
                ],
                'occurred_at' => Carbon::parse($this->otherOccurredAt),
            ]);

            $this->resetOtherForm();
            $this->showOtherForm = false;

            $this->dispatch('show-success-message', message: 'تم تسجيل المصروف بنجاح.');
        }

        public function render()
        {
            $user = Auth::guard('teacher')->user();
            $ownerId = $user->getOwnerId();
            $accessibleBranches = $user->getAccessibleBranchIds();

            $date = Carbon::createFromFormat('Y-m', $this->month);
            $periodStart = $date->copy()->startOfMonth();
            $periodEnd = $date->copy()->endOfMonth();

            $baseQuery = Expense::query()
                ->with(['teacherPayee', 'branch', 'recordedBy'])
                ->where('teacher_id', $ownerId)
                ->whereBetween('occurred_at', [$periodStart, $periodEnd]);

            if (! $user->isOwner()) {
                empty($accessibleBranches)
                    ? $baseQuery->whereRaw('1 = 0')
                    : $baseQuery->whereIn('branch_id', $accessibleBranches);
            }

            $otherQuery = (clone $baseQuery)
                ->whereIn('source', collect(ExpenseSourceEnum::cases())
                    ->filter(fn ($s) => ! $s->isTeacherRelated())
                    ->pluck('value')
                    ->toArray())
                ->when($this->sourceFilter !== '', fn ($q) => $q->where('source', $this->sourceFilter))
                ->when($this->branchFilter !== '', fn ($q) => $q->where('branch_id', $this->branchFilter))
                ->when($this->search !== '', fn ($q) => $q->where(function ($sub) {
                    $sub->where('reference', 'like', "%{$this->search}%")
                        ->orWhere('description', 'like', "%{$this->search}%")
                        ->orWhere('meta->reason', 'like', "%{$this->search}%");
                }));

            $otherExpenses = $otherQuery->orderByDesc('occurred_at')->paginate(15, ['*'], 'otherPage');

            $teacherQuery = (clone $baseQuery)
                ->whereIn('source', collect(ExpenseSourceEnum::cases())
                    ->filter(fn ($s) => $s->isTeacherRelated())
                    ->pluck('value')
                    ->toArray())
                ->when($this->sourceFilter !== '', fn ($q) => $q->where('source', $this->sourceFilter))
                ->when($this->branchFilter !== '', fn ($q) => $q->where('branch_id', $this->branchFilter))
                ->when($this->search !== '', fn ($q) => $q->where(function ($sub) {
                    $sub->where('reference', 'like', "%{$this->search}%")
                        ->orWhere('description', 'like', "%{$this->search}%");
                }));

            $teacherExpenses = $teacherQuery->orderByDesc('occurred_at')->paginate(15, ['*'], 'teacherPage');

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
                'otherExpenses' => $otherExpenses,
                'teacherExpenses' => $teacherExpenses,
                'totalsBySource' => $totalsBySource,
                'grandTotal' => $grandTotal,
                'branches' => Branch::whereIn('id', $accessibleBranches)->orderBy('name')->get(),
                'sources' => ExpenseSourceEnum::options(),
                'otherSources' => ExpenseSourceEnum::otherOptions(),
            ]);
        }
    }
