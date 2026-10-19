<?php

namespace App\Http\Controllers\TeacherPanel;

use App\Http\Controllers\Controller;
use App\Models\Teacher;
use App\Models\TeacherPayroll;
use Illuminate\Support\Facades\Auth;

class PayrollController extends Controller
{
    public function index()
    {
        return view('teacher-panel.pages.payroll.index');
    }

    public function generate()
    {
        return view('teacher-panel.pages.payroll.generate');
    }

    public function settings(Teacher $teacher)
    {
        $user = Auth::guard('teacher')->user();
        abort_unless($teacher->getOwnerId() === $user->getOwnerId(), 404);

        return view('teacher-panel.pages.payroll.settings', compact('teacher'));
    }

    public function show(TeacherPayroll $payroll)
    {
        $user = Auth::guard('teacher')->user();
        abort_unless(
            $payroll->teacher_id === $user->getOwnerId() ||
            $payroll->teacher?->getOwnerId() === $user->getOwnerId(),
            404
        );

        return view('teacher-panel.pages.payroll.show', compact('payroll'));
    }

    public function review()
    {
        return view('teacher-panel.pages.payroll.review');
    }
}
