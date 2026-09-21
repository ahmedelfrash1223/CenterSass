<?php

namespace App\Livewire\TeacherPanel\Concerns;

trait HasPaymentSelection
{
    public array $selectedAmounts = [];

    public array $showDiscountForm = [];


    public function toggleItem(int $itemId): void
    {
        $due = $this->findDue($itemId);
        if (!$due || $due['is_fully_paid']) return;

        $remaining = (float) $due['remaining'];
        if ($remaining <= 0) return;

        if (isset($this->selectedAmounts[$itemId])) {
            unset($this->selectedAmounts[$itemId]);
        } else {
            $this->selectedAmounts[$itemId] = round($remaining, 2);
        }
    }


    public function updatedSelectedAmounts($value, $key): void
    {
        $itemId = (int) $key;
        $amount = round((float) $value, 2);

        $due = $this->findDue($itemId);
        if (!$due) {
            unset($this->selectedAmounts[$itemId]);
            return;
        }

        $max = (float) $due['remaining'];

        if ($amount > $max) {
            $this->selectedAmounts[$itemId] = $max;
        } elseif ($amount <= 0) {
            unset($this->selectedAmounts[$itemId]);
        } else {
            $this->selectedAmounts[$itemId] = $amount;
        }
    }


    public function getSelectedCountProperty(): int
    {
        return count($this->selectedAmounts);
    }


    public function getTotalSelectedProperty(): float
    {
        return round(array_sum($this->selectedAmounts), 2);
    }


    protected function resetSelections(): void
    {
        $this->selectedAmounts = [];
        $this->showDiscountForm = [];
    }
}
