<?php

namespace App\Http\Controllers\TeacherPanel;

use App\Http\Controllers\Controller;
use App\Models\TeacherSubject;

class SubjectController extends Controller
{
    public function index()
    {
        return view('teacher-panel.pages.subject.index');
    }

    public function create()
    {
        return view('teacher-panel.pages.subject.create');
    }

    public function edit(TeacherSubject $subject)
    {
        abort_unless($subject->teacher_id === auth('teacher')->id(), 404);

        return view('teacher-panel.pages.subject.edit', compact('subject'));
    }
}