<?php

namespace App\Livewire\TeacherPanel\Pages\Dashboard\Parts;

use App\Models\GroupSchedule;
use Livewire\Component;

class ScheduleCard extends Component
{
    public ?GroupSchedule $currentSchedule = null;
    public ?GroupSchedule $nextSchedule = null;
    public int $todayCount = 0;
    public bool $canViewGroups = false;

    public function render()
    {
        return view('teacher-panel.pages.dashboard.parts.schedule-card');
    }
}
