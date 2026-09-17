<?php

namespace App\Http\Controllers\TeacherPanel;

use App\Enums\Teacher\TeacherPermissionEnum;
use App\Models\Invoice;
use Illuminate\Support\Facades\Auth;

class InvoiceController extends BaseResourceController
{
    public function index()
    {
        $user = $this->currentTeacher();

        abort_unless(
            $user->hasPermission(TeacherPermissionEnum::PAYMENT_VIEW),
            403
        );

        return view('teacher-panel.pages.invoice.index');
    }

    public function show(Invoice $invoice)
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

        return view('teacher-panel.pages.invoice.show', compact('invoice'));
    }
}
