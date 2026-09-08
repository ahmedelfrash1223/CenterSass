<?php

namespace App\Http\Controllers\TeacherPanel;

use App\Http\Controllers\Controller;
use App\Models\Group;

class GroupController extends Controller
{
    public function index() { return view('teacher-panel.pages.group.index'); }
    public function create() { return view('teacher-panel.pages.group.create'); }

    public function show(Group $group)
    {
        abort_unless($group->teacher_id === auth('teacher')->id(), 404);
        return view('teacher-panel.pages.group.show', compact('group'));
    }

    public function edit(Group $group)
    {
        abort_unless($group->teacher_id === auth('teacher')->id(), 404);
        return view('teacher-panel.pages.group.edit', compact('group'));
    }
}