<?php

namespace App\Http\Middleware;

use App\Enums\Teacher\TeacherPermissionEnum;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class TeacherPermissionMiddleware
{

    public function handle(Request $request, Closure $next, string ...$permissions): Response
    {
        $teacher = Auth::guard('teacher')->user();

        if (!$teacher) {
            abort(403, 'غير مصرح لك بالوصول.');
        }

        if (empty($permissions)) {
            return $next($request);
        }

        if ($teacher->isOwner()) {
            return $next($request);
        }

        foreach ($permissions as $permission) {
            if ($teacher->hasPermission($permission)) {
                return $next($request);
            }
        }

        abort(403, 'ليس لديك صلاحية للوصول إلى هذه الصفحة.');
    }
}
