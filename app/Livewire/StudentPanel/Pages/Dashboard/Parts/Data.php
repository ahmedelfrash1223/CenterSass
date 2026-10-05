<?php

namespace App\Livewire\StudentPanel\Pages\Dashboard\Parts;

use App\Models\Student;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Data extends Component
{
    public function render(): View
    {
        $student = Auth::guard('student')->user();
        abort_unless($student instanceof Student && $student->is_active, 403);

        return view('student-panel.pages.dashboard.parts.data', compact('student'));
    }
}
