<?php

namespace App\Livewire\TeacherPanel\Concerns\Payment;

use App\Enums\Teacher\DiscountTypeEnum;
use App\Models\InvoiceItem;
use Illuminate\Support\Facades\DB;

trait HasItemDiscount
{
    public array $discountTypes = [];

    public array $discountValues = [];

    public array $discountReasons = [];


    protected function loadDiscountData(): void
    {
        foreach ($this->dueGroups as $due) {
            $itemId = $due['item_id'];
            if (!$itemId) continue;

            $item = $due['item'];
            if (!$item) continue;

            $this->discountTypes[$itemId] = $item->discount_type ?: DiscountTypeEnum::FIXED->value;
            $this->discountValues[$itemId] = (string) ($item->discount_value ?: '0');
            $this->discountReasons[$itemId] = $item->discount_reason ?? '';
        }
    }


    public function toggleDiscountForm(int $itemId): void
    {
        $this->showDiscountForm[$itemId] = !($this->showDiscountForm[$itemId] ?? false);
    }


    public function applyDiscountToItem(int $itemId): void
    {
        $due = $this->findDue($itemId);
        if (!$due) return;

        if ($due['paid'] > 0) {
            $this->dispatch('show-error-message', message: 'مش ممكن تعدل الخصم بعد ما اتدفع على البند.');
            return;
        }

        $type = $this->discountTypes[$itemId] ?? DiscountTypeEnum::FIXED->value;
        $value = (float) ($this->discountValues[$itemId] ?? 0);
        $reason = $this->discountReasons[$itemId] ?? null;

        if (!DiscountTypeEnum::tryFrom($type)) {
            $this->dispatch('show-error-message', message: 'نوع الخصم غير صالح.');
            return;
        }

        if ($value < 0) {
            $this->dispatch('show-error-message', message: 'قيمة الخصم لا يمكن أن تكون سالبة.');
            return;
        }

        if ($type === DiscountTypeEnum::PERCENTAGE->value && $value > 100) {
            $this->dispatch('show-error-message', message: 'النسبة لا يمكن أن تتجاوز 100%.');
            return;
        }

        DB::transaction(function () use ($itemId, $type, $value, $reason) {
            $item = InvoiceItem::find($itemId);
            if (!$item) return;

            $item->update([
                'discount_type'   => $type,
                'discount_value'  => $value,
                'discount_reason' => $reason ?: null,
            ]);

            $item->recalculateDiscount();

            $invoice = $item->invoice;
            if ($invoice) {
                $invoice->recalculate();
            }
        });

        $this->refreshDueGroups();

        if (isset($this->selectedAmounts[$itemId])) {
            $due = $this->findDue($itemId);
            if ($due) {
                $this->selectedAmounts[$itemId] = round((float) $due['remaining'], 2);
            }
        }

        $this->dispatch('show-success-message', message: 'تم تطبيق الخصم.');
        $this->dispatchStatsRefresh();
    }


    public function removeDiscountFromItem(int $itemId): void
    {
        $due = $this->findDue($itemId);
        if (!$due) return;

        if ($due['paid'] > 0) {
            $this->dispatch('show-error-message', message: 'مش ممكن تزيل الخصم بعد الدفع.');
            return;
        }

        DB::transaction(function () use ($itemId) {
            $item = InvoiceItem::find($itemId);
            if (!$item) return;

            $item->update([
                'discount_type'    => DiscountTypeEnum::FIXED->value,
                'discount_value'   => 0,
                'discount_amount'  => 0,
                'discount_reason'  => null,
                'discounted_total' => (float) $item->total,
            ]);

            $invoice = $item->invoice;
            if ($invoice) {
                $invoice->recalculate();
            }
        });

        $this->discountTypes[$itemId] = DiscountTypeEnum::FIXED->value;
        $this->discountValues[$itemId] = '0';
        $this->discountReasons[$itemId] = '';

        $this->refreshDueGroups();

        if (isset($this->selectedAmounts[$itemId])) {
            $due = $this->findDue($itemId);
            if ($due) {
                $this->selectedAmounts[$itemId] = round((float) $due['remaining'], 2);
            }
        }

        $this->dispatch('show-success-message', message: 'تم إزالة الخصم.');
        $this->dispatchStatsRefresh();
    }


    public function getTotalDiscountProperty(): float
    {
        $total = 0;
        foreach ($this->selectedAmounts as $itemId => $amount) {
            $due = $this->findDue($itemId);
            if ($due) {
                $total += (float) $due['item']->discount_amount;
            }
        }
        return round($total, 2);
    }


    protected function resetDiscounts(): void
    {
        $this->discountTypes = [];
        $this->discountValues = [];
        $this->discountReasons = [];
        $this->showDiscountForm = [];
    }
}
