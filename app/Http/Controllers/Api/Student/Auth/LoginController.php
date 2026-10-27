<?php

namespace App\Http\Controllers\Api\Student\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Student\Auth\LoginRequest;
use App\Http\Resources\Api\Student\Auth\AccountResource;
use App\Services\Api\Student\Auth\LoginService;
use App\Support\Api\ApiResponse;
use Illuminate\Http\JsonResponse;

class LoginController extends Controller
{
    public function __construct(
        private readonly LoginService $loginService,
    ) {}

    public function login(LoginRequest $request): JsonResponse
    {
        $result = $this->loginService->login(
            login: $request->input('login'),
            password: $request->input('password'),
            deviceName: $request->input('device_name', 'mobile'),
        );

        return ApiResponse::auth(
            token: $result['token'],
            accountResource: new AccountResource($result['account']),
            message: 'تم تسجيل الدخول بنجاح.',
        );
    }

    public function me(): JsonResponse
    {
        $account = auth('student_account')->user();

        return ApiResponse::resource(
            resource: new AccountResource($account),
            message: 'بيانات الحساب.',
        );
    }
}
