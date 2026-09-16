<?php

namespace App\Http\Controllers\TeacherPanel;

use App\Enums\Admin\PackageFeatureKeyEnum;
use App\Enums\Teacher\TeacherPermissionEnum;
use App\Models\Student;

class StudentController extends BaseResourceController
{
    public function index()
    {
        return view('teacher-panel.pages.student.index');
    }

    public function create()
    {
        $teacher = $this->currentTeacher();

        abort_unless(
            $teacher->hasPermission(TeacherPermissionEnum::STUDENT_CREATE),
            403
        );

        $count = Student::where('teacher_id', $teacher->getOwnerId())->count();

        return $this->checkLimitOrRedirect(
            PackageFeatureKeyEnum::MAX_STUDENTS->value,
            $count,
            'student-error',
            'teacher.students.index'
        ) ?? view('teacher-panel.pages.student.create');
    }

    public function show(Student $student)
    {
        $this->ensureStudentAccess($student);

        return view('teacher-panel.pages.student.show', compact('student'));
    }

    public function edit(Student $student)
    {
        $teacher = $this->currentTeacher();

        abort_unless(
            $teacher->hasPermission(TeacherPermissionEnum::STUDENT_UPDATE),
            403
        );

        $this->ensureStudentAccess($student);

        return view('teacher-panel.pages.student.edit', compact('student'));
    }
}
