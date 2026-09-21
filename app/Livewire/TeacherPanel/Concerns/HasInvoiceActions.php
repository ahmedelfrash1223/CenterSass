<?php

namespace App\Livewire\TeacherPanel\Concerns;

use App\Models\Invoice;
use Illuminate\Support\Facades\DB;

trait HasInvoiceActions
{

    protected function performDelete(Invoice $invoice): bool
    {
        try {
            DB::transaction(function () use ($invoice) {
                foreach ($invoice->items as $item) {
                    $item->allocations()->delete();
                }

                $invoice->items()->delete();
                $invoice->payments()->delete();
                $invoice->delete();
            });

            return true;
        } catch (\Throwable $e) {
            \Log::error('Invoice delete failed', [
                'invoice_id' => $invoice->id,
                'error'      => $e->getMessage(),
            ]);

            return false;
        }
    }


    protected function performCancel(Invoice $invoice, string $reason): bool
    {
        if ($invoice->isCancelled()) {
            return false;
        }

        $user = $this->currentUser();

        $invoice->update([
            'status'              => 'cancelled',
            'cancelled_at'        => now(),
            'cancelled_by'        => $user->id,
            'cancellation_reason' => $reason,
        ]);

        $invoice->refresh();

        return true;
    }
}
