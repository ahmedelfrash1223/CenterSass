<?php

namespace App\Http\Controllers\TeacherPanel;

use App\Models\Exam;

class ExamController extends BaseResourceController
{
    public function index()
    {
        return view('teacher-panel.pages.exam.index');
    }

    public function create()
    {
        return view('teacher-panel.pages.exam.create');
    }

    public function show(Exam $exam)
    {
        $this->ensureExamAccess($exam);

        return view('teacher-panel.pages.exam.show', compact('exam'));
    }

    public function edit(Exam $exam)
    {
        $this->ensureExamAccess($exam);

        return view('teacher-panel.pages.exam.edit', compact('exam'));
    }
}
