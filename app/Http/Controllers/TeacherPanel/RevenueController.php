<?php

namespace App\Http\Controllers\TeacherPanel;

use App\Http\Controllers\Controller;

class RevenueController extends Controller
{
    public function index()
    {
        return view('teacher-panel.pages.revenue.index');
    }
}
