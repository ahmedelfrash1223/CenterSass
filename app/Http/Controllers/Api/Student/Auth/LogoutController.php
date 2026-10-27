<?php

namespace App\Http\Controllers\Api\Student\Auth;

use App\Http\Controllers\Controller;
use App\Services\Api\Student\Auth\LogoutService;
use App\Support\Api\ApiResponse;
use Illuminate\Http\JsonResponse;

class LogoutController extends Controller
{
    public function __construct(
        private readonly LogoutService $logoutService,
    ) {}

    public function logout(): JsonResponse
    {
        $this->logoutService->logout(auth('student_account')->user());

        return ApiResponse::success(message: 'تم تسجيل الخروج بنجاح.');
    }
}
