<?php

namespace App\Livewire\TeacherPanel\Concerns\Payment;

use App\Enums\Teacher\PaymentMethodEnum;
use App\Models\InvoiceItem;
use App\Models\Payment;
use App\Services\Teacher\PaymentService;

trait RecordsStudentPayment
{
    public string $payment_method = 'cash';
    public string $reference = '';
    public string $paid_at = '';
    public string $notes = '';

    public bool $showConfirm = false;

    public ?Payment $activeReceipt = null;
    public array $activeReceiptBreakdown = [];


    public function confirmPayment(): void
    {
        if (!$this->student) return;

        if (empty($this->selectedAmounts)) {
            $this->dispatch('show-error-message', message: 'اختر مجموعة واحدة على الأقل.');
            return;
        }

        if (!$this->denyUnless($this->canManagePayments())) {
            return;
        }

        $this->validate([
            'payment_method' => 'required|in:' . implode(',', PaymentMethodEnum::values()),
            'reference'      => 'nullable|string|max:100',
            'paid_at'        => 'required|date',
            'notes'          => 'nullable|string|max:500',
        ]);

        $this->showConfirm = true;
    }

    public function cancelConfirm(): void
    {
        $this->showConfirm = false;
    }


    public function recordPayment(PaymentService $paymentService): void
    {
        if (!$this->student) return;

        $this->showConfirm = false;

        if (!$this->denyUnless($this->canManagePayments())) {
            return;
        }

        $firstDue = collect($this->dueGroups)->firstWhere('invoice', '!=', null);
        if (!$firstDue || !$firstDue['invoice']) {
            $this->dispatch('show-error-message', message: 'الفاتورة غير موجودة.');
            return;
        }

        $invoice = $firstDue['invoice'];

        [$itemsPayload, $breakdown] = $this->buildPaymentPayload($invoice);

        if (empty($itemsPayload)) {
            $this->dispatch('show-error-message', message: 'لا توجد بنود صالحة.');
            return;
        }

        $payment = $paymentService->record(
            invoice: $invoice,
            items: $itemsPayload,
            paymentMethod: $this->payment_method,
            user: $this->currentUser(),
            reference: $this->reference ?: null,
            paidAt: $this->paid_at,
            notes: $this->notes ?: null,
        );

        if (!$payment) {
            $this->dispatch('show-error-message', message: 'فشل في تسجيل الدفع. راجع الـ logs.');
            return;
        }

        $this->activeReceipt = Payment::with([
            'student.branch',
            'invoice.items.group',
            'teacher',
            'creator',
            'allocations.invoiceItem',
        ])->find($payment->id);

        $this->activeReceiptBreakdown = $breakdown;

        $this->resetPaymentForm();
        $this->resetSelections();
        $this->resetDiscounts();

        $this->refreshDueGroups();
        $this->loadDiscountData();

        $this->dispatch('show-success-message', message: 'تم تسجيل الدفعة بنجاح.');
        $this->dispatchStatsRefresh();
        $this->dispatch('scroll-to-receipt');
    }


    protected function buildPaymentPayload($invoice): array
    {
        $itemsPayload = [];
        $breakdown = [];

        foreach ($this->selectedAmounts as $itemId => $amount) {
            $item = InvoiceItem::where('id', $itemId)
                ->where('invoice_id', $invoice->id)
                ->first();

            if (!$item) continue;
            if ($amount <= 0) continue;

            $itemsPayload[] = [
                'invoice_item_id' => $item->id,
                'amount'          => (float) $amount,
            ];

            $breakdown[$item->id] = [
                'description'     => $item->description,
                'amount'          => (float) $amount,
                'discount_amount' => (float) $item->discount_amount,
                'discount_type'   => $item->discount_type,
                'discount_value'  => (float) $item->discount_value,
                'discount_reason' => $item->discount_reason,
            ];
        }

        return [$itemsPayload, $breakdown];
    }


    public function closeReceipt(): void
    {
        $this->activeReceipt = null;
        $this->activeReceiptBreakdown = [];
    }

    protected function resetPaymentForm(): void
    {
        $this->payment_method = 'cash';
        $this->reference = '';
        $this->notes = '';
        $this->paid_at = now()->format('Y-m-d');
        $this->showConfirm = false;
    }


    protected function dispatchStatsRefresh(): void
    {
        $this->dispatch('payment-recorded')->to('teacher-panel.pages.payment.parts.payment-stats');
        $this->dispatch('payment-recorded')->to('teacher-panel.pages.payment.parts.students-with-dues');
    }
}
