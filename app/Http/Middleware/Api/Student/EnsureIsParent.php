<?php

namespace App\Http\Middleware\Api\Student;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureIsParent
{
    public function handle(Request $request, Closure $next): Response
    {
        $account = $request->user('student_account');

        if (! $account || $account->account_type !== 'parent') {
            return response()->json([
                'success' => false,
                'message' => 'هذا المسار مخصص لأولياء الأمور فقط.',
            ], 403);
        }

        return $next($request);
    }
}
