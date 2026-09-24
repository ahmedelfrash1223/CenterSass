<?php

namespace App\Livewire\TeacherPanel\Pages\Dashboard\Parts;

use Livewire\Component;

class StaffPanel extends Component
{
    public array $staffInfo = [];
    public bool $isOwner = true;

    public function render()
    {
        return view('teacher-panel.pages.dashboard.parts.staff-panel');
    }
}
