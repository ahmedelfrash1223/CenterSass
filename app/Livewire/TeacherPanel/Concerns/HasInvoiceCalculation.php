<?php

namespace App\Livewire\TeacherPanel\Concerns;

use App\Models\Invoice;
use App\Models\InvoiceItem;

trait HasInvoiceCalculation
{

    protected function recalculateItem(InvoiceItem $item): void
    {
        $item->recalculateDiscount();
        $item->recalculate();
    }


    protected function recalculateInvoice(Invoice $invoice): void
    {
        $invoice->refresh();

        foreach ($invoice->items()->get() as $item) {
            $item->recalculateDiscount();
            $item->recalculate();
        }

        $invoice->recalculate();
    }


    protected function itemIsEditable(InvoiceItem $item): bool
    {
        return !$item->invoice->isCancelled();
    }
}
