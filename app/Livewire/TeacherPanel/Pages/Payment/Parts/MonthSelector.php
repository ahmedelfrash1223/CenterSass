<?php

namespace App\Livewire\TeacherPanel\Pages\Payment\Parts;

use Livewire\Attributes\On;
use Livewire\Component;

class MonthSelector extends Component
{
    public string $month = '';

    public string $context = 'payments';

    public function mount(string $context = 'payments'): void
    {
        $this->context = $context;

        $sessionKey = "teacher.{$context}.selected_month";
        $this->month = session($sessionKey, now()->format('Y-m'));
    }

    public function updatedMonth(): void
    {
        $sessionKey = "teacher.{$this->context}.selected_month";
        session([$sessionKey => $this->month]);

        $this->dispatch('month-changed', month: $this->month);
    }

    #[On('set-month')]
    public function setMonth(string $month): void
    {
        $this->month = $month;
        $this->updatedMonth();
    }

    public function goToCurrentMonth(): void
    {
        $this->month = now()->format('Y-m');
        $this->updatedMonth();
    }

    public function render()
    {
        return view('teacher-panel.pages.payment.parts.month-selector', [
            'currentMonth' => now()->format('Y-m'),
            'isCurrentMonth' => $this->month === now()->format('Y-m'),
        ]);
    }
}
