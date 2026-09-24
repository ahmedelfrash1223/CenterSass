<?php

namespace App\Livewire\TeacherPanel\Pages\Dashboard\Parts;

use App\Models\GroupSchedule;
use Illuminate\Support\Collection;
use Livewire\Component;

class TodaySchedules extends Component
{
    public $todaySchedules;

    public ?GroupSchedule $currentSchedule = null;

    public bool $isOwner = false;

    public bool $canViewGroups = false;

    public function mount(
        $todaySchedules = [],
        ?GroupSchedule $currentSchedule = null,
        bool $isOwner = false,
        bool $canViewGroups = false
    ): void {
        $this->todaySchedules = $todaySchedules instanceof Collection
            ? $todaySchedules
            : collect($todaySchedules);

        $this->currentSchedule = $currentSchedule;
        $this->isOwner = $isOwner;
        $this->canViewGroups = $canViewGroups;
    }

    public function render()
    {
        return view('teacher-panel.pages.dashboard.parts.today-schedules');
    }
}
