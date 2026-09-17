<?php

namespace App\Livewire\TeacherPanel\Pages\Payment\Parts;

use App\Enums\Teacher\TeacherPermissionEnum;
use App\Livewire\TeacherPanel\Concerns\HasInvoiceManagement;
use App\Models\Payment;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;

class PaymentReceipt extends Component
{
    use HasInvoiceManagement;

    public ?Payment $payment = null;
    public bool $showModal = false;

    #[On('show-receipt')]
    public function show(int $paymentId): void
    {
        $user = Auth::guard('teacher')->user();

        $payment = Payment::query()
            ->where('teacher_id', $user->getOwnerId())
            ->with(['student', 'invoice.items.group', 'creator'])
            ->find($paymentId);

        if (!$payment) {
            $this->dispatch('show-error-message', message: 'الإيصال غير موجود.');
            return;
        }

        $this->payment = $payment;
        $this->showModal = true;
    }

    public function close(): void
    {
        $this->showModal = false;
        $this->payment = null;
    }

    public function render()
    {
        return view('teacher-panel.pages.payment.parts.payment-receipt');
    }
}
