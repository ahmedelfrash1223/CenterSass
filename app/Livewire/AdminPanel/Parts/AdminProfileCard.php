<?php

namespace App\Livewire\AdminPanel\Parts;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class AdminProfileCard extends Component
{
    public bool $open = false;

    public function getAdminProperty()
    {
        return Auth::guard('admin')->user();
    }

    public function toggle(): void
    {
        $this->open = !$this->open;
    }

    public function close(): void
    {
        $this->open = false;
    }

    public function logout()
    {
        Auth::guard('admin')->logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();

        return $this->redirectRoute('admin.login');
    }

    public function render()
    {
        return view('admin-panel.parts.admin-profile-card');
    }
}
