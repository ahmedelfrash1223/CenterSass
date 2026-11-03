<?php

namespace App\Http\Middleware\Api\Student;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureIsStudent
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

        $type = $account->account_type instanceof \App\Enums\Student\AccountTypeEnum
            ? $account->account_type->value
            : $account->account_type;

        if ($type !== 'student') {
            return response()->json([
                'success' => false,
                'message' => 'هذا المسار مخصص للطلاب فقط.',
            ], 403);
        }

        return $next($request);
    }
}
