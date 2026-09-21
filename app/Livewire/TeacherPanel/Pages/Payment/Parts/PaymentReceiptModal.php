<?php

namespace App\Livewire\TeacherPanel\Pages\Payment\Parts;

use App\Models\Payment;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;

class PaymentReceiptModal extends Component
{
    public ?Payment $payment = null;
    public array $breakdown = [];
    public bool $showModal = false;

    #[On('show-payment-receipt')]
    public function show(int $paymentId): void
    {
        $user = Auth::guard('teacher')->user();

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
            $this->dispatch('show-error-message', message: 'الإيصال غير موجود.');
            return;
        }

        $breakdown = [];
        foreach ($payment->allocations as $alloc) {
            $breakdown[$alloc->invoice_item_id] = [
                'description' => $alloc->invoiceItem?->description ?? '—',
                'amount'      => (float) $alloc->amount,
            ];
        }

        $this->payment = $payment;
        $this->breakdown = $breakdown;
        $this->showModal = true;
    }

    public function close(): void
    {
        $this->showModal = false;
        $this->payment = null;
        $this->breakdown = [];
    }

    public function render()
    {
        return view('teacher-panel.pages.payment.parts.payment-receipt-modal');
    }
}
