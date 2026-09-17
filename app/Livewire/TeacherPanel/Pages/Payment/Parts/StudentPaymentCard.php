<?php

namespace App\Livewire\TeacherPanel\Pages\Payment\Parts;

use App\Enums\Teacher\PaymentMethodEnum;
use App\Enums\Teacher\TeacherPermissionEnum;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Student;
use App\Services\Teacher\InvoiceService;
use App\Services\Teacher\PaymentService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;

class StudentPaymentCard extends Component
{
    public ?Student $student = null;

    // Payment form
    public ?int $selectedInvoiceId = null;
    public string $amount = '';
    public string $payment_method = 'cash';
    public string $reference = '';
    public string $paid_at = '';
    public string $notes = '';

    // Generate invoice
    public string $generateMonth = '';

    // Receipt display
    public ?Payment $activeReceipt = null;

    public function mount(): void
    {
        $this->paid_at = now()->format('Y-m-d');
        $this->generateMonth = now()->format('Y-m');
    }

    #[On('student-selected')]
    public function loadStudent(int $studentId): void
    {
        $user = Auth::guard('teacher')->user();

        $this->student = Student::query()
            ->where('teacher_id', $user->getOwnerId())
            ->with(['branch', 'activeGroups'])
            ->withCount(['activeGroups as groups_count'])
            ->find($studentId);

        if (!$this->student) {
            $this->dispatch('show-error-message', message: 'الطالب غير موجود.');
            return;
        }

        if (!$user->isOwner() && !in_array($this->student->branch_id, $user->getAccessibleBranchIds())) {
            $this->student = null;
            $this->dispatch('show-error-message', message: 'هذا الطالب لا ينتمي لفروعك المتاحة.');
            return;
        }

        $this->activeReceipt = null;
        $this->resetPaymentForm();

        // Auto-select first unpaid invoice
        $firstUnpaid = $this->student->invoices()
            ->where('teacher_id', $user->getOwnerId())
            ->whereIn('status', ['unpaid', 'partial'])
            ->orderBy('period_start')
            ->first();

        if ($firstUnpaid) {
            $this->selectInvoice($firstUnpaid->id);
        }
    }

    public function clear(): void
    {
        $this->student = null;
        $this->activeReceipt = null;
        $this->resetPaymentForm();
    }

    public function selectInvoice(int $invoiceId): void
    {
        $invoice = $this->getInvoice($invoiceId);
        if (!$invoice || !$invoice->has_remaining) {
            return;
        }

        $this->selectedInvoiceId = $invoice->id;
        $this->amount = (string)$invoice->remaining_amount;
        $this->activeReceipt = null;
    }

    public function resetPaymentForm(): void
    {
        $this->selectedInvoiceId = null;
        $this->amount = '';
        $this->payment_method = 'cash';
        $this->reference = '';
        $this->paid_at = now()->format('Y-m-d');
        $this->notes = '';
    }

    public function recordPayment(PaymentService $service): void
    {
        if (!$this->student) return;

        $user = Auth::guard('teacher')->user();

        if (!$user->hasPermission(TeacherPermissionEnum::PAYMENT_MANAGE)) {
            $this->dispatch('show-error-message', message: 'ليس لديك صلاحية لتسجيل المدفوعات.');
            return;
        }

        $invoice = $this->getInvoice($this->selectedInvoiceId);
        if (!$invoice) {
            $this->dispatch('show-error-message', message: 'اختر الفاتورة أولاً.');
            return;
        }

        $this->validate([
            'amount' => 'required|numeric|min:0.01|max:' . $invoice->remaining_amount,
            'payment_method' => 'required|in:' . implode(',', PaymentMethodEnum::values()),
            'reference' => 'nullable|string|max:100',
            'paid_at' => 'required|date',
            'notes' => 'nullable|string|max:500',
        ], [
            'amount.required' => 'المبلغ مطلوب.',
            'amount.min' => 'المبلغ يجب أن يكون أكبر من صفر.',
            'amount.max' => 'المبلغ أكبر من المتبقي.',
            'payment_method.required' => 'طريقة الدفع مطلوبة.',
            'paid_at.required' => 'تاريخ الدفع مطلوب.',
        ]);

        $payment = $service->record(
            invoice: $invoice,
            amount: (float)$this->amount,
            paymentMethod: $this->payment_method,
            user: $user,
            reference: $this->reference ?: null,
            paidAt: $this->paid_at,
            notes: $this->notes ?: null,
        );

        if (!$payment) {
            $this->dispatch('show-error-message', message: 'فشل في تسجيل الدفع.');
            return;
        }

        $this->activeReceipt = $payment->load(['student.branch', 'invoice.items.group', 'teacher', 'creator']);
        $this->resetPaymentForm();
        $this->dispatch('show-success-message', message: 'تم تسجيل الدفعة بنجاح.');
    }

    public function showReceipt(int $paymentId): void
    {
        $user = Auth::guard('teacher')->user();

        $payment = Payment::where('teacher_id', $user->getOwnerId())
            ->with(['student.branch', 'invoice.items.group', 'teacher', 'creator'])
            ->find($paymentId);

        if (!$payment) {
            $this->dispatch('show-error-message', message: 'الإيصال غير موجود.');
            return;
        }

        $this->activeReceipt = $payment;
    }

    public function closeReceipt(): void
    {
        $this->activeReceipt = null;
    }

    public function generateInvoice(InvoiceService $service): void
    {
        if (!$this->student) return;

        $user = Auth::guard('teacher')->user();

        if (!$user->hasPermission(TeacherPermissionEnum::PAYMENT_MANAGE)) {
            $this->dispatch('show-error-message', message: 'ليس لديك صلاحية لتوليد الفواتير.');
            return;
        }

        try {
            $date = Carbon::createFromFormat('Y-m', $this->generateMonth);
        } catch (\Exception $e) {
            $this->dispatch('show-error-message', message: 'الشهر غير صالح.');
            return;
        }

        $periodStart = $date->copy()->startOfMonth();
        $periodEnd = $date->copy()->endOfMonth();

        $invoice = $service->generateForStudent($this->student, $periodStart, $periodEnd, $user);

        if (!$invoice) {
            $this->dispatch('show-error-message', message: 'مش قادر يولد فاتورة. تأكد إن الطالب في مجموعة فيها أسعار أو إن الفاتورة موجودة بالفعل.');
            return;
        }

        $this->selectInvoice($invoice->id);
        $this->dispatch('show-success-message', message: 'تم توليد الفاتورة.');
    }

    /**
     * Fetch a scoped invoice by ID.
     */
    protected function getInvoice(?int $invoiceId): ?Invoice
    {
        if (!$this->student || !$invoiceId) {
            return null;
        }

        $user = Auth::guard('teacher')->user();

        return Invoice::where('id', $invoiceId)
            ->where('teacher_id', $user->getOwnerId())
            ->where('student_id', $this->student->id)
            ->first();
    }

    public function render()
    {
        $user = Auth::guard('teacher')->user();

        $invoices = collect();

        if ($this->student) {
            $invoices = Invoice::where('student_id', $this->student->id)
                ->where('teacher_id', $user->getOwnerId())
                ->with(['items', 'payments'])
                ->orderByDesc('period_start')
                ->get();
        }

        return view('teacher-panel.pages.payment.parts.student-payment-card', [
            'invoices' => $invoices,
            'paymentMethods' => PaymentMethodEnum::options(),
            'canManage' => $user->hasPermission(TeacherPermissionEnum::PAYMENT_MANAGE),
        ]);
    }
}
