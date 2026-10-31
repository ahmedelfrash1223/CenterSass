<?php

namespace App\Http\Middleware\Api\Student;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureAccountIsVerified
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

        if (is_null($account->email_verified_at)) {
            return response()->json([
                'success' => false,
                'message' => 'يجب تفعيل البريد الإلكتروني أولاً. يرجى التحقق من الكود المرسل إليك.',
                'needs_verification' => true,
            ], 403);
        }

        return $next($request);
    }
}
