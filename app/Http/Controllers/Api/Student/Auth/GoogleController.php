<?php

namespace App\Http\Controllers\Api\Student\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Student\Auth\GoogleCallbackRequest;
use App\Http\Resources\Api\Student\Auth\AccountResource;
use App\Services\Api\Student\Auth\GoogleAuthService;
use App\Support\Api\ApiResponse;
use Illuminate\Http\JsonResponse;

class GoogleController extends Controller
{
    public function __construct(
        private readonly GoogleAuthService $googleAuthService,
    ) {}

    public function callback(GoogleCallbackRequest $request): JsonResponse
    {
        $result = $this->googleAuthService->authenticateWithIdToken(
            idToken: $request->input('id_token'),
            deviceName: $request->input('device_name', 'mobile'),
        );

        $status = $result['is_new'] ? 201 : 200;
        $message = $result['is_new']
            ? 'تم إنشاء الحساب بنجاح.'
            : 'تم تسجيل الدخول بنجاح.';

        return ApiResponse::auth(
            token: $result['token'],
            accountResource: new AccountResource($result['account']),
            message: $message,
            status: $status,
            extra: ['is_new' => $result['is_new']],
        );
    }
}
