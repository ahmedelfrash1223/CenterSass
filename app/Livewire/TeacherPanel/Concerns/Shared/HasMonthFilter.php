<?php

namespace App\Livewire\TeacherPanel\Concerns\Shared;

use Livewire\Attributes\On;

trait HasMonthFilter
{
    public string $monthFilter = '';


    protected function monthSessionKey(): string
    {
        return 'teacher.{$this->monthContext}.selected_month';
    }


    protected function monthContext(): string
    {
        return method_exists($this, 'getMonthContext')
            ? $this->getMonthContext()
            : 'invoices';
    }

    protected function initializeMonthFilter(): void
    {
        $key = "teacher.{$this->monthContext()}.selected_month";
        $this->monthFilter = session($key, now()->format('Y-m'));
    }

    #[On('month-changed')]
    public function onMonthFilterChanged(string $month): void
    {
        $this->monthFilter = $month;

        if (method_exists($this, 'resetPage')) {
            $this->resetPage();
        }
    }
}
