<?php

namespace App\Http\Controllers\TeacherPanel;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PaymentController extends Controller
{




    public function verify(string $receiptNumber)
    {
        $payment = Payment::where('receipt_number', $receiptNumber)
            ->with(['student.branch', 'invoice', 'teacher', 'creator'])
            ->first();

        if (!$payment) {
            abort(404, 'الإيصال غير موجود.');
        }

        return view('teacher-panel.pages.payment.verify', [
            'payment' => $payment,
        ]);
    }
}
