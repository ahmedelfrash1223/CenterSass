<?php

namespace App\Livewire\TeacherPanel\Pages\Dashboard\Parts;

use Livewire\Component;

class OwnerPanel extends Component
{
    public array $ownerStats = [];
    public bool $isOwner = false;

    public function render()
    {
        return view('teacher-panel.pages.dashboard.parts.owner-panel');
    }
}
