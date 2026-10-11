<?php

namespace App\Http\Controllers\TeacherPanel;

use App\Http\Controllers\Controller;
use App\Models\Assignment;
use Illuminate\Support\Facades\Auth;

class AssignmentController extends Controller
{
    public function index()
    {
        return view('teacher-panel.pages.assignment.index');
    }

    public function create()
    {
        return view('teacher-panel.pages.assignment.create');
    }

    public function show(Assignment $assignment)
    {
        $user = Auth::guard('teacher')->user();

        abort_unless($assignment->teacher_id === $user->getOwnerId(), 404);

        if (! $user->isOwner()) {
            abort_unless(in_array($assignment->branch_id, $user->getAccessibleBranchIds()), 404);
        }

        return view('teacher-panel.pages.assignment.show', compact('assignment'));
    }

    public function edit(Assignment $assignment)
    {
        $user = Auth::guard('teacher')->user();

        abort_unless($assignment->teacher_id === $user->getOwnerId(), 404);

        if (! $user->isOwner()) {
            abort_unless(in_array($assignment->branch_id, $user->getAccessibleBranchIds()), 404);
        }

        return view('teacher-panel.pages.assignment.edit', compact('assignment'));
    }
}
