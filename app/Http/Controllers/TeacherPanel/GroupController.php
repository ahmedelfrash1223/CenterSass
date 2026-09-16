<?php

namespace App\Http\Controllers\TeacherPanel;

use App\Enums\Admin\PackageFeatureKeyEnum;
use App\Models\Group;

class GroupController extends BaseResourceController
{
    public function index()
    {
        return view('teacher-panel.pages.group.index');
    }

    public function create()
    {
        $teacher = $this->currentTeacher();
        $count = Group::where('teacher_id', $teacher->getOwnerId())->count();

        return $this->checkLimitOrRedirect(
            PackageFeatureKeyEnum::MAX_GROUPS->value,
            $count,
            'group-error',
            'teacher.groups.index'
        ) ?? view('teacher-panel.pages.group.create');
    }

    public function show(Group $group)
    {
        $this->ensureGroupAccess($group);

        return view('teacher-panel.pages.group.show', compact('group'));
    }

    public function edit(Group $group)
    {
        $this->ensureGroupAccess($group);

        return view('teacher-panel.pages.group.edit', compact('group'));
    }
}
