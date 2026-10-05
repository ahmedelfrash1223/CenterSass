<?php

namespace App\Livewire\StudentPanel\Parts;

use App\Models\Student;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class StudentProfileCard extends Component
{
    public bool $open = false;

    public function getStudentProperty(): Student
    {
        $student = Auth::guard('student')->user();
        abort_unless($student instanceof Student && $student->is_active, 403);

        return $student;
    }

    public function toggle(): void
    {
        $this->open = ! $this->open;
    }

    public function close(): void
    {
        $this->open = false;
    }

    public function logout(): void
    {
        Auth::guard('student')->logout();
        session()->invalidate();
        session()->regenerateToken();

        $this->redirectRoute('student.login');
    }

    public function render(): View
    {
        return view('student-panel.parts.student-profile-card');
    }
}
