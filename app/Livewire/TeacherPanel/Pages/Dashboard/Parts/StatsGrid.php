<?php

namespace App\Livewire\TeacherPanel\Pages\Dashboard\Parts;

use Livewire\Component;

class StatsGrid extends Component
{
    public array $stats = [];
    public bool $isOwner = false;

    public function render()
    {
        return view('teacher-panel.pages.dashboard.parts.stats-grid');
    }
}
