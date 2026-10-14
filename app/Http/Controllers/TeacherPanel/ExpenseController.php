<?php

namespace App\Http\Controllers\TeacherPanel;

use App\Http\Controllers\Controller;

class ExpenseController extends Controller
{
    public function index()
    {
        return view('teacher-panel.pages.expense.index');
    }
}
