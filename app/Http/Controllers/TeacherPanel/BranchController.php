<?php

namespace App\Http\Controllers\TeacherPanel;

use App\Enums\Admin\PackageFeatureKeyEnum;
use App\Enums\Teacher\TeacherPermissionEnum;
use App\Models\Branch;

class BranchController extends BaseResourceController
{
    public function index()
    {
        return view('teacher-panel.pages.branch.index');
    }

    public function create()
    {
        $teacher = $this->currentTeacher();

        abort_unless(
            $teacher->hasPermission(TeacherPermissionEnum::BRANCH_CREATE),
            403
        );

        // Redirect before loading view if the plan is full
        $count = Branch::where('teacher_id', $teacher->getOwnerId())->count();

        return $this->checkLimitOrRedirect(
            PackageFeatureKeyEnum::MAX_BRANCHES->value,
            $count,
            'branch-error',
            'teacher.branches.index'
        ) ?? view('teacher-panel.pages.branch.create');
    }

    public function edit(Branch $branch)
    {
        $this->ensureBranchAccess($branch);

        $teacher = $this->currentTeacher();

        abort_unless(
            $teacher->hasPermission(TeacherPermissionEnum::BRANCH_UPDATE),
            403
        );

        return view('teacher-panel.pages.branch.edit', compact('branch'));
    }
}
