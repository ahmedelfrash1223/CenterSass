<?php

namespace App\Livewire\TeacherPanel\Pages\Invoice\Parts;

use App\Models\Invoice;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;
use App\Livewire\TeacherPanel\Concerns\HasInvoiceActions;
use App\Livewire\TeacherPanel\Concerns\HasInvoiceQuery;
use App\Livewire\TeacherPanel\Concerns\HasMonthFilter;
use App\Livewire\TeacherPanel\Concerns\HasPaymentPermissions;

class InvoiceData extends Component
{
    use WithPagination;
    use HasMonthFilter;
    use HasPaymentPermissions;
    use HasInvoiceQuery;
    use HasInvoiceActions;

    protected $paginationTheme = 'bootstrap';

    public string $search = '';
    public string $statusFilter = '';

    public ?int $confirmingDeleteId = null;

    public function mount(): void
    {
        $this->initializeMonthFilter();
    }

    #[On('invoice-updated')]
    public function refreshData(): void
    {
        // re-render
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

    // ============================================
    // Delete flow
    // ============================================
    public function requestDelete(int $invoiceId): void
    {
        if (!$this->denyUnless($this->canDeletePayments())) {
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
        if (!$this->denyUnless($this->canDeletePayments())) {
            return;
        }

        $invoice = Invoice::where('teacher_id', $this->currentUser()->getOwnerId())
            ->find($invoiceId);

        if (!$invoice) {
            $this->dispatch('show-error-message', message: 'الفاتورة غير موجودة.');
            return;
        }

        if (!$this->performDelete($invoice)) {
            $this->dispatch('show-error-message', message: 'فشل في حذف الفاتورة.');
            return;
        }

        $this->confirmingDeleteId = null;
        $this->dispatch('show-success-message', message: 'تم حذف الفاتورة.');
    }

    // ============================================
    // Render
    // ============================================
    public function render()
    {
        abort_unless($this->canViewPayments(), 403);

        $query = $this->baseInvoiceQuery()
            ->with(['student', 'items.group'])
            ->orderByDesc('created_at');

        $this->applyMonthFilter($query);
        $this->applySearchFilter($query, $this->search);
        $this->applyStatusFilter($query, $this->statusFilter);

        $invoices = $query->paginate(10);

        // ========= Stats =========
        $statsBase = $this->baseInvoiceQuery();
        $this->applyMonthFilter($statsBase);

        $stats = [
            'total'          => (clone $statsBase)->count(),
            'unpaid_amount'  => (float) (clone $statsBase)->where('status', 'unpaid')->sum('remaining_amount'),
            'partial_amount' => (float) (clone $statsBase)->where('status', 'partial')->sum('remaining_amount'),
            'paid_amount'    => (float) (clone $statsBase)->where('status', 'paid')->sum('total'),
        ];

        return view('teacher-panel.pages.invoice.parts.invoice-data', [
            'invoices'  => $invoices,
            'stats'     => $stats,
            'canDelete' => $this->canDeletePayments(),
        ]);
    }
}
