<?php

namespace App\Http\Controllers\TeacherPanel;

use App\Enums\Teacher\TeacherPermissionEnum;
use App\Models\Attendance;

class AttendanceController extends BaseResourceController
{
    public function index()
    {
        $user = $this->currentTeacher();

        abort_unless(
            $user->hasPermission(TeacherPermissionEnum::ATTENDANCE_MANAGE),
            403
        );

        return view('teacher-panel.pages.attendance.index');
    }

    public function records()
    {
        $user = $this->currentTeacher();

        abort_unless(
            $user->hasPermission(TeacherPermissionEnum::ATTENDANCE_VIEW),
            403
        );

        return view('teacher-panel.pages.attendance.records');
    }

    public function show(Attendance $attendance)
    {
        $user = $this->currentTeacher();

        abort_unless(
            $attendance->teacher_id === $user->getOwnerId(),
            404
        );

        if (!$user->isOwner()) {
            abort_unless(
                in_array($attendance->branch_id, $user->getAccessibleBranchIds()),
                404
            );
        }

        return view('teacher-panel.pages.attendance.show', compact('attendance'));
    }
}
