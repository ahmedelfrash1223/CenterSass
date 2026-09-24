<?php

namespace App\Http\Controllers\TeacherPanel;

use App\Http\Controllers\Controller;
use App\Models\Payment;

class PaymentController extends Controller
{
    public function verify(string $receiptNumber)
    {
        $payment = Payment::where('receipt_number', $receiptNumber)
            ->with([
                'student.branch',
                'invoice',
                'teacher',
                'creator',
                'allocations.invoiceItem',
            ])
            ->first();

        return view('teacher-panel.pages.payment.verify', [
            'payment' => $payment,
        ]);
    }
}
