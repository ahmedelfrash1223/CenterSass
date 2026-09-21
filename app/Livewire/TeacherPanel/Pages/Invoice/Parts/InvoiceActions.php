<?php

namespace App\Livewire\TeacherPanel\Pages\Invoice\Parts;

use App\Models\Invoice;
use Livewire\Component;
use App\Livewire\TeacherPanel\Concerns\HasInvoiceActions;
use App\Livewire\TeacherPanel\Concerns\HasInvoiceCalculation;
use App\Livewire\TeacherPanel\Concerns\HasInvoiceEditing;
use App\Livewire\TeacherPanel\Concerns\HasPaymentPermissions;

class InvoiceActions extends Component
{
    use HasPaymentPermissions;
    use HasInvoiceActions;
    use HasInvoiceCalculation;
    use HasInvoiceEditing;

    public Invoice $invoice;
    public bool $showCancelPanel = false;
    public bool $showDeletePanel = false;
    public bool $showEditPanel = false;
    public bool $deleteConfirmed = false;
    public string $cancellation_reason = '';
    public array $editItems = [];


    public function mount(Invoice $invoice): void
    {
        $this->invoice = $invoice;
    }

    // ============================================
    // Permissions
    // ============================================
    protected function canEdit(): bool
    {
        return $this->canManagePayments() && !$this->invoice->isCancelled();
    }

    protected function canCancel(): bool
    {
        return $this->canDeletePayments() && !$this->invoice->isCancelled();
    }

    protected function canDelete(): bool
    {
        return $this->canDeletePayments();
    }

    // ============================================
    // Cancel
    // ============================================
    public function openCancelPanel(): void
    {
        if (!$this->denyUnless($this->canCancel())) {
            return;
        }

        $this->closeAll();
        $this->cancellation_reason = '';
        $this->showCancelPanel = true;
    }

    public function closeCancelPanel(): void
    {
        $this->showCancelPanel = false;
        $this->cancellation_reason = '';
    }

    public function cancelInvoice(): void
    {
        if (!$this->denyUnless($this->canCancel())) {
            return;
        }

        $this->validate([
            'cancellation_reason' => 'required|string|min:3|max:500',
        ], [
            'cancellation_reason.required' => 'سبب الإلغاء مطلوب.',
            'cancellation_reason.min' => 'السبب قصير جدًا.',
        ]);

        if (!$this->performCancel($this->invoice, $this->cancellation_reason)) {
            $this->dispatch('show-error-message', message: 'الفاتورة ملغية بالفعل.');
            return;
        }

        $this->dispatch('show-success-message', message: 'تم إلغاء الفاتورة.');
        $this->dispatch('invoice-updated');
        $this->dispatch('invoice-updated')->to('teacher-panel.pages.invoice.parts.invoice-data');

        $this->closeCancelPanel();
    }

    // ============================================
    // Delete
    // ============================================
    public function openDeletePanel(): void
    {
        if (!$this->denyUnless($this->canDelete())) {
            return;
        }

        $this->closeAll();
        $this->deleteConfirmed = false;
        $this->showDeletePanel = true;
    }

    public function closeDeletePanel(): void
    {
        $this->showDeletePanel = false;
        $this->deleteConfirmed = false;
    }

    public function requestDeleteConfirm(): void
    {
        if (!$this->denyUnless($this->canDelete())) {
            return;
        }

        $this->deleteConfirmed = true;
    }

    public function cancelDeleteConfirm(): void
    {
        $this->deleteConfirmed = false;
    }

    public function deleteInvoice()
    {
        if (!$this->denyUnless($this->canDelete())) {
            return;
        }

        if (!$this->deleteConfirmed) {
            $this->dispatch('show-error-message', message: 'لازم تأكد الحذف أولاً.');
            return;
        }

        if (!$this->performDelete($this->invoice)) {
            $this->dispatch('show-error-message', message: 'فشل في حذف الفاتورة.');
            return;
        }

        session()->flash('invoice-success', 'تم حذف الفاتورة.');

        return $this->redirect(route('teacher.invoices.index'), navigate: true);
    }

    // ============================================
    // Edit
    // ============================================
    public function openEditPanel(): void
    {
        if (!$this->denyUnless($this->canEdit())) {
            return;
        }

        $this->closeAll();
        $this->editItems = $this->prepareEditItems($this->invoice);
        $this->showEditPanel = true;
    }

    public function closeEditPanel(): void
    {
        $this->showEditPanel = false;
        $this->editItems = [];
    }

    public function updateItem(int $index): void
    {
        if (!$this->denyUnless($this->canEdit())) {
            return;
        }

        $data = $this->editItems[$index] ?? null;
        if (!$data) {
            return;
        }

        $item = $this->invoice->items()->find($data['id']);
        if (!$item) {
            return;
        }

        if (!$this->performItemUpdate($item, $data)) {
            $this->dispatch('show-error-message', message: 'البيانات غير صالحة.');
            return;
        }

        $this->invoice->refresh();
        $this->invoice->load('items');

        $this->editItems = $this->prepareEditItems($this->invoice);

        $this->dispatch('show-success-message', message: 'تم تحديث البند.');
        $this->dispatch('invoice-updated');
    }

    protected function closeAll(): void
    {
        $this->showCancelPanel = false;
        $this->showDeletePanel = false;
        $this->showEditPanel = false;
        $this->deleteConfirmed = false;
    }

    public function render()
    {
        return view('teacher-panel.pages.invoice.parts.invoice-actions', [
            'canEdit' => $this->canEdit(),
            'canCancel' => $this->canCancel(),
            'canDelete' => $this->canDelete(),
        ]);
    }
}
