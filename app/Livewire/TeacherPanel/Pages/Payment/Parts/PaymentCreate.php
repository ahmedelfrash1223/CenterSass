<?php

namespace App\Livewire\TeacherPanel\Pages\Payment\Parts;

use App\Enums\Teacher\PaymentMethodEnum;
use App\Enums\Teacher\TeacherPermissionEnum;
use App\Livewire\TeacherPanel\Concerns\HasInvoiceManagement;
use App\Models\Invoice;
use App\Models\Payment;
use App\Services\Teacher\PaymentService;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;

class PaymentCreate extends Component
{
    use HasInvoiceManagement;

    public ?Invoice $invoice = null;

    public string $amount = '';
    public string $payment_method = 'cash';
    public string $reference = '';
    public string $paid_at = '';
    public string $notes = '';

    public bool $showModal = false;

    #[On('open-payment-modal')]
    public function openModal(int $invoiceId): void
    {
        $user = Auth::guard('teacher')->user();

        $invoice = Invoice::query()
            ->where('teacher_id', $user->getOwnerId())
            ->find($invoiceId);

        if (!$invoice) {
            $this->dispatch('show-error-message', message: 'الفاتورة غير موجودة.');
            return;
        }

        if ($invoice->isCancelled()) {
            $this->dispatch('show-error-message', message: 'الفاتورة ملغية.');
            return;
        }

        if ($invoice->isPaid()) {
            $this->dispatch('show-error-message', message: 'الفاتورة مدفوعة بالكامل.');
            return;
        }

        $this->invoice = $invoice;
        $this->amount = (string) $invoice->remaining_amount;
        $this->payment_method = 'cash';
        $this->reference = '';
        $this->paid_at = now()->format('Y-m-d');
        $this->notes = '';
        $this->showModal = true;
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->invoice = null;
        $this->reset(['amount', 'payment_method', 'reference', 'paid_at', 'notes']);
    }

    public function save(PaymentService $service): void
    {
        if (!$this->invoice) {
            return;
        }

        $user = Auth::guard('teacher')->user();

        if (!$user->hasPermission(TeacherPermissionEnum::PAYMENT_MANAGE)) {
            $this->dispatch('show-error-message', message: 'ليس لديك صلاحية لتسجيل المدفوعات.');
            return;
        }

        $this->validate([
            'amount' => 'required|numeric|min:0.01|max:' . $this->invoice->remaining_amount,
            'payment_method' => 'required|in:' . implode(',', PaymentMethodEnum::values()),
            'reference' => 'nullable|string|max:100',
            'paid_at' => 'required|date',
            'notes' => 'nullable|string|max:500',
        ], [
            'amount.required' => 'المبلغ مطلوب.',
            'amount.numeric' => 'المبلغ يجب أن يكون رقم.',
            'amount.min' => 'المبلغ يجب أن يكون أكبر من صفر.',
            'amount.max' => 'المبلغ أكبر من المتبقي.',
            'payment_method.required' => 'طريقة الدفع مطلوبة.',
            'paid_at.required' => 'تاريخ الدفع مطلوب.',
        ]);

        $payment = $service->record(
            invoice: $this->invoice,
            amount: (float) $this->amount,
            paymentMethod: $this->payment_method,
            user: $user,
            reference: $this->reference ?: null,
            paidAt: $this->paid_at,
            notes: $this->notes ?: null,
        );

        if (!$payment) {
            $this->dispatch('show-error-message', message: 'فشل في تسجيل الدفع. تأكد من المبلغ والمتبقي.');
            return;
        }

        $paymentId = $payment->id;

        $this->dispatch('show-success-message', message: 'تم تسجيل الدفع بنجاح.');

        // Close payment modal
        $this->closeModal();

        $this->dispatch('show-receipt', paymentId: $paymentId);

        $this->dispatch('student-selected', studentId: $this->invoice->student_id);
    }
    public function render()
    {
        return view('teacher-panel.pages.payment.parts.payment-create', [
            'paymentMethods' => PaymentMethodEnum::options(),
        ]);
    }
}
