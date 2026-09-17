<?php

namespace App\Livewire\TeacherPanel\Pages\Invoice\Parts;

use App\Enums\Teacher\TeacherPermissionEnum;
use App\Models\Invoice;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class InvoiceShow extends Component
{
    public Invoice $invoice;

    public function mount(Invoice $invoice): void
    {
        $user = Auth::guard('teacher')->user();

        abort_unless(
            $invoice->teacher_id === $user->getOwnerId(),
            404
        );

        if (!$user->isOwner() && !in_array($invoice->branch_id, $user->getAccessibleBranchIds())) {
            abort(404);
        }

        $this->invoice = $invoice->load([
            'student.branch',
            'items.group',
            'payments.creator',
            'creator',
        ]);
    }

    public function render()
    {
        return view('teacher-panel.pages.invoice.parts.invoice-show');
    }
}
