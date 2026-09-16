<?php

namespace App\Http\Controllers\TeacherPanel;

use App\Http\Controllers\Controller;
use App\Http\Controllers\TeacherPanel\Concerns\EnsuresBranchAccess;
use App\Http\Controllers\TeacherPanel\Concerns\EnsuresGroupAccess;
use App\Http\Controllers\TeacherPanel\Concerns\EnsuresStudentAccess;
use App\Http\Controllers\TeacherPanel\Concerns\EnsuresTeacherAccess;
use App\Models\Teacher;
use Illuminate\Support\Facades\Auth;

abstract class BaseResourceController extends Controller
{
    use EnsuresBranchAccess;
    use EnsuresGroupAccess;
    use EnsuresStudentAccess;
    use EnsuresTeacherAccess;

    protected function currentTeacher(): Teacher
    {
        return Auth::guard('teacher')->user();
    }

    protected function checkLimitOrRedirect(int $featureKey, int $count, string $errorKey, string $route)
    {
        $teacher = $this->currentTeacher();
        $limit = $teacher->checkFeatureLimit($featureKey, $count);

        if (!$limit['can']) {
            return redirect()->route($route)->with($errorKey, $limit['message']);
        }

        return null;
    }
}
