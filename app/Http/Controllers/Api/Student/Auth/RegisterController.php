<?php

namespace App\Http\Controllers\Api\Student\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Student\Auth\RegisterRequest;
use App\Http\Resources\Api\Student\Auth\AccountResource;
use App\Services\Api\Student\Auth\RegisterService;
use App\Support\Api\ApiResponse;
use Illuminate\Http\JsonResponse;

class RegisterController extends Controller
{
    public function __construct(
        private readonly RegisterService $registerService,
    ) {}

    public function register(RegisterRequest $request): JsonResponse
    {
        $result = $this->registerService->register(
            data: $request->validated(),
            deviceName: $request->input('device_name', 'mobile'),
        );

        return ApiResponse::auth(
            token: $result['token'],
            accountResource: new AccountResource($result['account']),
            message: 'تم إنشاء الحساب بنجاح. يرجى تفعيل البريد الإلكتروني.',
            status: 201,
            extra: [
                'needs_verification' => true,
                'identifier' => $result['account']->email,
            ],
        );
    }
}
