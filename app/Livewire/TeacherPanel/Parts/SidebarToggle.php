<?php

namespace App\Livewire\AdminPanel\Parts;

use Livewire\Component;

class SidebarToggle extends Component
{
    public function toggle(): void
    {
        $this->dispatch('toggle-sidebar');
    }

    public function render()
    {
        return view('admin-panel.parts.sidebar-toggle');
    }
}
