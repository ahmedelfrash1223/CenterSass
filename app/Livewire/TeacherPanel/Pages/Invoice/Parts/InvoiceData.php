<?php

namespace App\Livewire\TeacherPanel\Pages\Invoice\Parts;

use App\Livewire\TeacherPanel\Concerns\Invoice\HasInvoiceActions;
use App\Livewire\TeacherPanel\Concerns\Invoice\HasInvoiceQuery;
use App\Livewire\TeacherPanel\Concerns\Payment\HasPaymentPermissions;
use App\Livewire\TeacherPanel\Concerns\Shared\HasMonthFilter;
use App\Models\Invoice;
use App\Services\Teacher\PaymentQueryService;
use Carbon\Carbon;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class InvoiceData extends Component
{
    use HasInvoiceActions;
    use HasInvoiceQuery;
    use HasMonthFilter;
    use HasPaymentPermissions;
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public string $search = '';

    public string $statusFilter = '';

    public string $branchId = '';

    public ?int $confirmingDeleteId = null;

    public function mount(): void
    {
        $this->initializeMonthFilter();
        $this->branchId = (string) session('teacher.payments.selected_branch', '');
    }

    #[On('invoice-updated')]
    public function refreshData(): void
    {
        // re-render
    }

    #[On('branch-changed')]
    public function onBranchChanged(string $branchId): void
    {
        $this->branchId = $branchId;
        $this->resetPage();
    }

    protected function getMonthContext(): string
    {
        return 'invoices';
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingStatusFilter(): void
    {
        $this->resetPage();
    }

    public function clearFilters(): void
    {
        $this->search = '';
        $this->statusFilter = '';
        $this->resetPage();
    }

    public function requestDelete(int $invoiceId): void
    {
        if (! $this->denyUnless($this->canDeletePayments())) {
            return;
        }

        $this->confirmingDeleteId = $invoiceId;
    }

    public function cancelDelete(): void
    {
        $this->confirmingDeleteId = null;
    }

    public function deleteInvoice(int $invoiceId): void
    {
        if (! $this->denyUnless($this->canDeletePayments())) {
            return;
        }

        $invoice = Invoice::where('teacher_id', $this->currentUser()->getOwnerId())
            ->find($invoiceId);

        if (! $invoice) {
            $this->dispatch('show-error-message', message: 'الفاتورة غير موجودة.');

            return;
        }

        if (! $this->performDelete($invoice)) {
            $this->dispatch('show-error-message', message: 'فشل في حذف الفاتورة.');

            return;
        }

        $this->confirmingDeleteId = null;
        $this->dispatch('show-success-message', message: 'تم حذف الفاتورة.');
    }

    public function render()
    {
        abort_unless($this->canViewPayments(), 403);

        $user = $this->currentUser();
        $ownerId = $user->getOwnerId();
        $accessibleBranches = $user->getAccessibleBranchIds();
        $service = app(PaymentQueryService::class);

        $branchFilter = $this->branchId !== '' ? (int) $this->branchId : null;

        if ($branchFilter && ! $user->isOwner() && ! in_array($branchFilter, $accessibleBranches)) {
            $branchFilter = null;
        }

        $query = $this->baseInvoiceQuery()
            ->with(['student', 'items.group'])
            ->orderByDesc('created_at');

        if ($branchFilter) {
            $query->where('branch_id', $branchFilter);
        }

        $this->applyMonthFilter($query);
        $this->applySearchFilter($query, $this->search);
        $this->applyStatusFilter($query, $this->statusFilter);

        $invoices = $query->paginate(10);

        $monthValue = $this->monthFilter !== '' ? $this->monthFilter : now()->format('Y-m');
        $date = Carbon::createFromFormat('Y-m', $monthValue);
        $periodStart = $date->copy()->startOfMonth();
        $periodEnd = $date->copy()->endOfMonth();

        $studentsWithNoPayment = $service->studentsUnpaid($user, $monthValue)
            ->when($branchFilter, fn ($q) => $q->where('branch_id', $branchFilter))
            ->count();

        $studentsWithRemaining = $service->studentsPartial($user, $monthValue)
            ->when($branchFilter, fn ($q) => $q->where('branch_id', $branchFilter))
            ->count();

        $statsInvoicesQuery = Invoice::query()
            ->where('teacher_id', $ownerId)
            ->whereDate('period_start', $periodStart->toDateString())
            ->whereDate('period_end', $periodEnd->toDateString())
            ->whereNull('cancelled_at');

        if (! $user->isOwner()) {
            empty($accessibleBranches)
                ? $statsInvoicesQuery->whereRaw('1 = 0')
                : $statsInvoicesQuery->whereIn('branch_id', $accessibleBranches);
        }

        if ($branchFilter) {
            $statsInvoicesQuery->where('branch_id', $branchFilter);
        }

        $collected = (float) (clone $statsInvoicesQuery)->sum('paid_amount');

        $paidInvoicesCount = (clone $statsInvoicesQuery)
            ->where('total', '>', 0)
            ->whereRaw('paid_amount >= total')
            ->count();

        $stats = [
            'students_no_payment' => $studentsWithNoPayment,
            'students_remaining' => $studentsWithRemaining,
            'paid_invoices' => $paidInvoicesCount,
            'collected' => $collected,
        ];

        return view('teacher-panel.pages.invoice.parts.invoice-data', [
            'invoices' => $invoices,
            'stats' => $stats,
            'canDelete' => $this->canDeletePayments(),
        ]);
    }
}
