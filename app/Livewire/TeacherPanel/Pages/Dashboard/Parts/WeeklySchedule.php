<?php

namespace App\Livewire\TeacherPanel\Pages\Dashboard\Parts;

use Livewire\Component;

class WeeklySchedule extends Component
{
    public array $weeklySchedule = [];

    public function render()
    {
        return view('teacher-panel.pages.dashboard.parts.weekly-schedule');
    }
}
