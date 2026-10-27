<?php

namespace App\Http\Controllers\Api\Student\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Student\Auth\ResendOtpRequest;
use App\Http\Requests\Api\Student\Auth\VerifyOtpRequest;
use App\Http\Resources\Api\Student\Auth\AccountResource;
use App\Services\Api\Student\Auth\OtpService;
use App\Support\Api\ApiResponse;
use Illuminate\Http\JsonResponse;

class OtpController extends Controller
{
    public function __construct(
        private readonly OtpService $otpService,
    ) {}

    public function verify(VerifyOtpRequest $request): JsonResponse
    {
        $result = $this->otpService->verify(
            identifier: $request->input('identifier'),
            code: $request->input('code'),
            purpose: $request->input('purpose'),
            deviceName: $request->input('device_name', 'mobile'),
        );

        return ApiResponse::auth(
            token: $result['token'],
            accountResource: new AccountResource($result['account']),
            message: 'تم التحقق بنجاح.',
        );
    }

    public function resend(ResendOtpRequest $request): JsonResponse
    {
        $this->otpService->resend(
            identifier: $request->input('identifier'),
            purpose: $request->input('purpose'),
        );

        return ApiResponse::success(
            data: [
                'expires_in' => OtpService::OTP_TTL_MINUTES * 60,
            ],
            message: 'تم إرسال كود التحقق.',
        );
    }
}
