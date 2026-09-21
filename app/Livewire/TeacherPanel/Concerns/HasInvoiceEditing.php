<?php

namespace App\Livewire\TeacherPanel\Concerns;

use App\Models\Invoice;
use App\Models\InvoiceItem;
use Illuminate\Support\Facades\DB;

trait HasInvoiceEditing
{

    protected function prepareEditItems(Invoice $invoice): array
    {
        return $invoice->items->map(function (InvoiceItem $item) {
            return [
                'id'              => $item->id,
                'description'     => $item->description,
                'total'           => (float) $item->total,
                'discount_type'   => $item->discount_type ?: 'fixed',
                'discount_value'  => (float) $item->discount_value,
                'discount_reason' => $item->discount_reason,
            ];
        })->toArray();
    }


    protected function performItemUpdate(InvoiceItem $item, array $data): bool
    {
        $total = (float) ($data['total'] ?? 0);
        $discountValue = (float) ($data['discount_value'] ?? 0);
        $discountType = $data['discount_type'] ?? 'fixed';

        if ($total < 0) {
            return false;
        }

        if ($discountType === 'percentage' && $discountValue > 100) {
            return false;
        }

        DB::transaction(function () use ($item, $data, $total, $discountValue, $discountType) {
            $item->update([
                'total'           => $total,
                'discount_type'   => $discountType ?: 'fixed',
                'discount_value'  => $discountValue,
                'discount_reason' => $data['discount_reason'] ?: null,
            ]);

            $this->recalculateItem($item);

            $invoice = $item->invoice;
            if ($invoice) {
                $this->recalculateInvoice($invoice);
            }
        });

        return true;
    }
}
