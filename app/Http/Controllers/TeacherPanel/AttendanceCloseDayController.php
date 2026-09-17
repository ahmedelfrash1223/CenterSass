<?php

namespace App\Http\Controllers\TeacherPanel;

use App\Enums\Teacher\TeacherPermissionEnum;
use App\Models\Group;

class AttendanceCloseDayController extends BaseResourceController
{
    public function index()
    {
        $user = $this->currentTeacher();

        abort_unless(
            $user->hasPermission(TeacherPermissionEnum::ATTENDANCE_CLOSE_DAY),
            403
        );

        return view('teacher-panel.pages.attendance-close-day.index');
    }

    public function show(Group $group)
    {
        $user = $this->currentTeacher();

        abort_unless(
            $user->hasPermission(TeacherPermissionEnum::ATTENDANCE_CLOSE_DAY),
            403
        );

        $this->ensureGroupAccess($group);

        return view('teacher-panel.pages.attendance-close-day.show', compact('group'));
    }
}
