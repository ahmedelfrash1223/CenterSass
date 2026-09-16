<?php

namespace App\Http\Controllers\TeacherPanel;

use App\Enums\Admin\PackageFeatureKeyEnum;
use App\Enums\Teacher\TeacherPermissionEnum;
use App\Models\Teacher;

class StaffController extends BaseResourceController
{
    public function index()
    {
        return view('teacher-panel.pages.staff.index');
    }

    public function create()
    {
        $user = $this->currentTeacher();

        abort_unless(
            $user->hasPermission(TeacherPermissionEnum::STAFF_CREATE),
            403
        );

        $count = Teacher::where('owner_id', $user->getOwnerId())->count();

        return $this->checkLimitOrRedirect(
            PackageFeatureKeyEnum::MAX_EMPLOYEES->value,
            $count,
            'staff-error',
            'teacher.staff.index'
        ) ?? view('teacher-panel.pages.staff.create');
    }

    public function show(Teacher $staff)
    {
        $this->ensureTeacherAccess($staff);

        return view('teacher-panel.pages.staff.show', compact('staff'));
    }

    public function edit(Teacher $staff)
    {
        $user = $this->currentTeacher();

        abort_unless(
            $user->hasPermission(TeacherPermissionEnum::STAFF_UPDATE),
            403
        );

        $this->ensureTeacherAccess($staff);

        return view('teacher-panel.pages.staff.edit', compact('staff'));
    }
}
