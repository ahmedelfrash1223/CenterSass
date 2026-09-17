<?php

namespace App\Livewire\TeacherPanel\Concerns;

use App\Models\Invoice;
use Illuminate\Support\Facades\Auth;

trait HasInvoiceManagement
{
    /**
     * Ensure the current user can access this invoice.
     */
    protected function ensureInvoiceAccess(Invoice $invoice): void
    {
        $user = Auth::guard('teacher')->user();

        abort_unless(
            $invoice->teacher_id === $user->getOwnerId(),
            404
        );

        if (!$user->isOwner() && !in_array($invoice->branch_id, $user->getAccessibleBranchIds())) {
            abort(404);
        }
    }

    /**
     * Format an amount in EGP.
     */
    protected function formatMoney(float|string|null $amount): string
    {
        return number_format((float) $amount, 2) . ' ج.م';
    }
}
