<?php

namespace App\Livewire\TeacherPanel\Pages\Invoice\Parts;

use App\Enums\Teacher\TeacherPermissionEnum;
use App\Models\Invoice;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class InvoiceData extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public string $search = '';
    public string $statusFilter = '';
    public string $monthFilter = '';

    protected $listeners = ['refreshData' => '$refresh'];

    public function updatingSearch(): void { $this->resetPage(); }
    public function updatingStatusFilter(): void { $this->resetPage(); }
    public function updatingMonthFilter(): void { $this->resetPage(); }

    public function clearFilters(): void
    {
        $this->search = '';
        $this->statusFilter = '';
        $this->monthFilter = '';
        $this->resetPage();
    }

    public function render()
    {
        $user = Auth::guard('teacher')->user();

        abort_unless(
            $user->hasPermission(TeacherPermissionEnum::PAYMENT_VIEW),
            403
        );

        $ownerId = $user->getOwnerId();
        $accessibleBranchIds = $user->getAccessibleBranchIds();

        $query = Invoice::query()
            ->with(['student', 'items.group'])
            ->where('teacher_id', $ownerId);

        if (!$user->isOwner()) {
            empty($accessibleBranchIds)
                ? $query->whereRaw('1 = 0')
                : $query->whereIn('branch_id', $accessibleBranchIds);
        }

        $query->when($this->search !== '', function ($q) {
            $q->where(function ($sub) {
                $sub->where('invoice_number', 'like', "%{$this->search}%")
                    ->orWhereHas('student', function ($s) {
                        $s->where('name', 'like', "%{$this->search}%")
                            ->orWhere('code', 'like', "%{$this->search}%");
                    });
            });
        });

        $query->when($this->statusFilter !== '', fn($q) => $q->where('status', $this->statusFilter));

        $query->when($this->monthFilter !== '', function ($q) {
            try {
                $date = Carbon::createFromFormat('Y-m', $this->monthFilter);
                $q->whereYear('period_start', $date->year)
                    ->whereMonth('period_start', $date->month);
            } catch (\Exception $e) {}
        });

        $invoices = $query->orderByDesc('created_at')->paginate(15);

        $baseQuery = Invoice::where('teacher_id', $ownerId);

        if (!$user->isOwner()) {
            empty($accessibleBranchIds)
                ? $baseQuery->whereRaw('1 = 0')
                : $baseQuery->whereIn('branch_id', $accessibleBranchIds);
        }

        $stats = [
            'total' => (clone $baseQuery)->count(),
            'unpaid_amount' => (float) (clone $baseQuery)->where('status', 'unpaid')->sum('remaining_amount'),
            'partial_amount' => (float) (clone $baseQuery)->where('status', 'partial')->sum('remaining_amount'),
            'paid_amount' => (float) (clone $baseQuery)->where('status', 'paid')->sum('total'),
        ];

        return view('teacher-panel.pages.invoice.parts.invoice-data', [
            'invoices' => $invoices,
            'stats' => $stats,
        ]);
    }
}
