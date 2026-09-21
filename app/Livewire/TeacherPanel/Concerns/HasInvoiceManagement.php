<?php

namespace App\Livewire\TeacherPanel\Concerns;

use App\Models\Invoice;

trait HasInvoiceManagement
{

    protected function ensureInvoiceAccess(Invoice $invoice): void
    {
        $user = $this->currentUser();

        abort_unless(
            $invoice->teacher_id === $user->getOwnerId(),
            404
        );

        if (!$user->isOwner() && !in_array($invoice->branch_id, $user->getAccessibleBranchIds())) {
            abort(404);
        }
    }


    protected function formatMoney(float|string|null $amount): string
    {
        return number_format((float) $amount, 2) . ' ج.م';
    }
}
