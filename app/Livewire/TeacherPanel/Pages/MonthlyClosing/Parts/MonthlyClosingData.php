<?php

namespace App\Livewire\TeacherPanel\Pages\MonthlyClosing\Parts;

use App\Http\Requests\TeacherPanel\CloseMonthRequest;
use App\Models\Branch;
use App\Models\MonthlyClosing;
use App\Services\Teacher\MonthlyClosingService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class MonthlyClosingData extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public string $month = '';

    public string $branchFilter = '';

    public string $search = '';

    public bool $showCloseForm = false;

    public string $closeMonth = '';

    public string $closeBranchId = '';

    public string $closeNotes = '';

    public ?int $confirmingReopenId = null;

    public ?int $confirmingDeleteId = null;

    public function mount(): void
    {
        $this->month = session('teacher.monthly-closing.selected_month', now()->format('Y-m'));
        $this->closeMonth = now()->format('Y-m');
    }

    #[On('month-changed')]
    public function onMonthChanged(string $month): void
    {
        $this->month = $month;
        $this->resetPage();
    }

    public function updatingBranchFilter(): void { $this->resetPage(); }
    public function updatingSearch(): void { $this->resetPage(); }

    public function clearFilters(): void
    {
        $this->branchFilter = '';
        $this->search = '';
        $this->resetPage();
    }

    public function toggleCloseForm(): void
    {
        $this->showCloseForm = ! $this->showCloseForm;
        if (! $this->showCloseForm) {
            $this->resetCloseForm();
        }
    }

    public function resetCloseForm(): void
    {
        $this->closeMonth = now()->format('Y-m');
        $this->closeBranchId = '';
        $this->closeNotes = '';
        $this->resetValidation();
    }

    public function closeMonth(MonthlyClosingService $service): void
    {
        $request = new CloseMonthRequest;
        $this->validate($request->rules(), $request->messages(), $request->attributes());

        $user = Auth::guard('teacher')->user();

        try {
            $closing = $service->closeMonth(
                $user,
                $this->closeMonth,
                $this->closeBranchId !== '' ? (int) $this->closeBranchId : null,
                $this->closeNotes ?: null,
            );

            $this->resetCloseForm();
            $this->showCloseForm = false;

            $this->dispatch('show-success-message', message: 'تم تقفيل الشهر بنجاح.');
        } catch (\Throwable $e) {
            $this->dispatch('show-error-message', message: $e->getMessage());
        }
    }

    public function askReopen(int $id): void
    {
        $this->confirmingReopenId = $id;
    }

    public function cancelReopen(): void
    {
        $this->confirmingReopenId = null;
    }

    public function reopen(MonthlyClosingService $service): void
    {
        if (! $this->confirmingReopenId) {
            return;
        }

        $closing = MonthlyClosing::findOrFail($this->confirmingReopenId);
        $user = Auth::guard('teacher')->user();

        try {
            $service->reopen($user, $closing);
            $this->confirmingReopenId = null;
            $this->dispatch('show-success-message', message: 'تم إعادة فتح الشهر.');
        } catch (\Throwable $e) {
            $this->confirmingReopenId = null;
            $this->dispatch('show-error-message', message: $e->getMessage());
        }
    }

    public function askDelete(int $id): void
    {
        $this->confirmingDeleteId = $id;
    }

    public function cancelDelete(): void
    {
        $this->confirmingDeleteId = null;
    }

    public function delete(MonthlyClosingService $service): void
    {
        if (! $this->confirmingDeleteId) {
            return;
        }

        $closing = MonthlyClosing::findOrFail($this->confirmingDeleteId);
        $user = Auth::guard('teacher')->user();

        try {
            $service->delete($user, $closing);
            $this->confirmingDeleteId = null;
            $this->dispatch('show-success-message', message: 'تم حذف التقفيل.');
        } catch (\Throwable $e) {
            $this->confirmingDeleteId = null;
            $this->dispatch('show-error-message', message: $e->getMessage());
        }
    }

    public function render()
    {
        $user = Auth::guard('teacher')->user();
        $ownerId = $user->getOwnerId();
        $accessibleBranches = $user->getAccessibleBranchIds();

        $date = Carbon::createFromFormat('Y-m', $this->month);
        $periodStart = $date->copy()->startOfMonth();
        $periodEnd = $date->copy()->endOfMonth();

        $query = MonthlyClosing::query()
            ->with(['branch', 'closedBy'])
            ->where('teacher_id', $ownerId)
            ->where('month', $this->month);

        if (! $user->isOwner()) {
            empty($accessibleBranches)
                ? $query->whereRaw('1 = 0')
                : $query->whereIn('branch_id', $accessibleBranches);
        }

        $query->when($this->branchFilter !== '', fn($q) => $q->where('branch_id', $this->branchFilter));
        $query->when($this->search !== '', fn($q) => $q->where('reference', 'like', "%{$this->search}%"));

        $closings = $query->orderByDesc('closed_at')->paginate(15);

        return view('teacher-panel.pages.monthly-closing.parts.monthly-closing-data', [
            'closings' => $closings,
            'branches' => Branch::whereIn('id', $accessibleBranches)->orderBy('name')->get(),
        ]);
    }
}
