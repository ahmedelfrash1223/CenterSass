<?php

namespace App\Http\Controllers\AdminPanel;

use App\Http\Controllers\Controller;
use App\Models\Teacher;
use Illuminate\Http\Request;

class TeacherController extends Controller
{

    public function index()
    {
        return view('admin-panel.pages.teacher.index');
    }

    public function create(){
        return view('admin-panel.pages.teacher.create');
    }

    public function edit(Teacher $teacher)
    {
        return view('admin-panel.pages.teacher.edit', compact('teacher'));
    }

    public function show(Teacher $teacher)
    {
        return view('admin-panel.pages.teacher.show', compact('teacher'));
    }

}
