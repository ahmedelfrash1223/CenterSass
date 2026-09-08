<?php

namespace App\Http\Controllers\TeacherPanel;

use App\Http\Controllers\Controller;
use App\Models\Branch;

class BranchController extends Controller
{
    public function index()
    {
        return view('teacher-panel.pages.branch.index');
    }

    public function create()
    {
        return view('teacher-panel.pages.branch.create');
    }

    public function edit(Branch $branch)
    {
        abort_unless($branch->teacher_id === auth('teacher')->id(), 404);

        return view('teacher-panel.pages.branch.edit', compact('branch'));
    }
}