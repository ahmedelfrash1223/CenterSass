<?php

namespace App\Livewire\TeacherPanel\Pages\Invoice\Parts;

use App\Models\Invoice;
use Livewire\Attributes\On;
use Livewire\Component;
use App\Livewire\TeacherPanel\Concerns\Invoice\HasInvoiceManagement;
use App\Livewire\TeacherPanel\Concerns\Invoice\HasInvoiceReceipt;
use App\Livewire\TeacherPanel\Concerns\Payment\HasPaymentPermissions;

class InvoiceShow extends Component
{
    use HasPaymentPermissions;
    use HasInvoiceManagement;
    use HasInvoiceReceipt;

    public Invoice $invoice;

    public function mount(Invoice $invoice): void
    {
        $this->ensureInvoiceAccess($invoice);

        $this->loadInvoice($invoice);
    }

    protected function loadInvoice(Invoice $invoice): void
    {
        $this->invoice = $invoice->load([
            'student.branch',
            'items.group',
            'payments.creator',
            'payments.allocations.invoiceItem',
            'creator',
        ]);
    }

    #[On('invoice-updated')]
    public function refreshInvoice(): void
    {
        $this->invoice->refresh();
        $this->loadInvoice($this->invoice);
    }

    #[On('discount-updated')]
    public function onDiscountUpdated(): void
    {
        $this->refreshInvoice();
    }

    #[On('show-payment-receipt')]
    public function showReceipt(int $paymentId): void
    {
        if (!$this->loadReceipt($paymentId)) {
            $this->dispatch('show-error-message', message: 'الإيصال غير موجود.');
        }
    }

    public function render()
    {
        return view('teacher-panel.pages.invoice.parts.invoice-show');
    }
}
