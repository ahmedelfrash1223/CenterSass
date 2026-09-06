<?php

namespace App\Livewire\AdminPanel\Auth;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;


class Logout extends Component
{
    #---------------------------------------------------------LOGOUT
    public function logout()
    {
        Auth::guard('admin')->logout();

        session()->forget('guard.admin');

        session()->regenerateToken();

        return to_route('admin.login');
    }

    #---------------------------------------------------------RENDER
    public function render()
    {
        return view('admin-panel.auth.logout');
    }
}
