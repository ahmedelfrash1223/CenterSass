<?php

namespace App\Livewire\TeacherPanel\Parts;

use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;

class TeacherProfileCard extends Component
{
    public bool $open = false;

    public function getTeacherProperty()
    {
        return Auth::guard('teacher')->user();
    }

    public function toggle(): void
    {
        $this->open = !$this->open;
    }

    public function close(): void
    {
        $this->open = false;
    }

    #[On('nb-teacher-logout')]
    public function logout()
    {
        Auth::guard('teacher')->logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();

        return $this->redirectRoute('teacher.login');
    }

    public function render()
    {
        return view('teacher-panel.parts.teacher-profile-card');
    }
}
