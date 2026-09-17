<?php

namespace App\Http\Controllers\TeacherPanel;

use App\Enums\Teacher\TeacherPermissionEnum;
use App\Models\Invoice;
use App\Models\Payment;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;

class PaymentController extends BaseResourceController
{
    public function index()
    {
        $user = $this->currentTeacher();

        abort_unless(
            $user->hasPermission(TeacherPermissionEnum::PAYMENT_VIEW),
            403
        );

        return view('teacher-panel.pages.payment.index');
    }

    /**
     * Download receipt as PDF.
     */
    public function pdf(Payment $payment)
    {
        $user = $this->currentTeacher();

        abort_unless(
            $payment->teacher_id === $user->getOwnerId(),
            404
        );

        abort_unless(
            $user->hasPermission(TeacherPermissionEnum::PAYMENT_RECEIPT),
            403
        );

        $payment->load(['student.branch', 'invoice', 'teacher', 'creator']);

        $pdf = Pdf::loadView('teacher-panel.pages.payment.pdf.receipt', [
            'payment' => $payment,
        ]);

        $pdf->setPaper([0, 0, 226, 600]); // A5 width (80mm) x 600

        return $pdf->download("receipt-{$payment->receipt_number}.pdf");
    }

    /**
     * Verify a receipt by its number (public).
     */
    public function verify(string $receiptNumber)
    {
        $payment = Payment::where('receipt_number', $receiptNumber)
            ->with(['student', 'invoice'])
            ->first();

        if (!$payment) {
            abort(404, 'الإيصال غير موجود');
        }

        return view('teacher-panel.pages.payment.verify', compact('payment'));
    }

    /**
     * Download invoice as PDF.
     */
    public function invoicePdf(Invoice $invoice)
    {
        $user = $this->currentTeacher();

        abort_unless(
            $invoice->teacher_id === $user->getOwnerId(),
            404
        );

        if (!$user->isOwner()) {
            abort_unless(
                in_array($invoice->branch_id, $user->getAccessibleBranchIds()),
                404
            );
        }

        $invoice->load(['student.branch', 'items.group', 'teacher', 'creator']);

        $pdf = Pdf::loadView('teacher-panel.pages.payment.pdf.invoice', [
            'invoice' => $invoice,
        ]);

        $pdf->setPaper([0, 0, 226, 800]); // A5 width

        return $pdf->download("invoice-{$invoice->invoice_number}.pdf");
    }
}
