<?php

namespace App\Http\Middleware\Api\Student;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureAccountIsActive
{
    public function handle(Request $request, Closure $next): Response
    {
        $account = $request->user('student_account');

        if (! $account) {
            return response()->json([
                'success' => false,
                'message' => 'غير مصرح.',
            ], 401);
        }

        if (! $account->canLogin()) {
            return response()->json([
                'success' => false,
                'message' => 'هذا الحساب غير نشط. تواصل مع الدعم.',
            ], 403);
        }

        return $next($request);
    }
}
