<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminPermissionMiddleware
{
    public function handle(Request $request, Closure $next, string ...$permissions): Response
    {
        $admin = Auth::guard('admin')->user();

        if (! $admin) {
            abort(403, 'غير مصرح لك بالوصول.');
        }

        if (empty($permissions)) {
            return $next($request);
        }

        foreach ($permissions as $permission) {
            if ($admin->hasPermission($permission)) {
                return $next($request);
            }
        }

        abort(403, 'ليس لديك صلاحية للوصول إلى هذه الصفحة.');
    }
}
