<?php

namespace App\Livewire\TeacherPanel\Concerns\Invoice;

use App\Models\Payment;

trait HasInvoiceReceipt
{
    public ?Payment $activePayment = null;
    public array $activeBreakdown = [];


    protected function loadReceipt(int $paymentId): bool
    {
        $user = $this->currentUser();

        $payment = Payment::with([
            'student.branch',
            'invoice.items.group',
            'teacher',
            'creator',
            'allocations.invoiceItem',
        ])
            ->where('teacher_id', $user->getOwnerId())
            ->find($paymentId);

        if (!$payment) {
            return false;
        }

        $this->activePayment = $payment;
        $this->activeBreakdown = $this->buildBreakdown($payment);

        return true;
    }


    protected function buildBreakdown(Payment $payment): array
    {
        $breakdown = [];

        foreach ($payment->allocations as $alloc) {
            $item = $alloc->invoiceItem;

            $breakdown[$alloc->invoice_item_id] = [
                'description'     => $item?->description ?? '—',
                'amount'          => (float) $alloc->amount,
                'discount_amount' => (float) ($item?->discount_amount ?? 0),
                'discount_type'   => $item?->discount_type,
                'discount_value'  => (float) ($item?->discount_value ?? 0),
                'discount_reason' => $item?->discount_reason,
            ];
        }

        return $breakdown;
    }


    public function closeReceipt(): void
    {
        $this->activePayment = null;
        $this->activeBreakdown = [];
    }
}
