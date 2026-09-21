<?php

namespace App\Http\Controllers\TeacherPanel;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use Illuminate\Support\Facades\Auth;

class InvoiceController extends Controller
{
    public function index()
    {
        return view('teacher-panel.pages.invoice.index');
    }

    public function show(Invoice $invoice)
    {
        $user = Auth::guard('teacher')->user();

        abort_unless(
            $invoice->teacher_id === $user->getOwnerId(),
            404
        );

        if (!$user->isOwner() && !in_array($invoice->branch_id, $user->getAccessibleBranchIds())) {
            abort(404);
        }

        return view('teacher-panel.pages.invoice.show', [
            'invoice' => $invoice->load(['student.branch', 'items.group', 'payments.creator', 'creator']),
        ]);
    }
}
