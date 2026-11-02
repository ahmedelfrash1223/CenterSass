<?php

namespace App\Services\Api\Student\Auth;

use App\Enums\Student\OtpPurposeEnum;
use App\Models\StudentAccount;
use App\Support\Api\Student\Auth\TokenHelper;
use Illuminate\Support\Facades\DB;

class RegisterService
{
    public function __construct(
        private readonly OtpService $otpService,
        private readonly TokenHelper $tokenHelper,
    ) {}

    public function register(array $data, string $deviceName): array
    {
        return DB::transaction(function () use ($data, $deviceName) {
            $account = StudentAccount::create([
                'account_type' => $data['account_type'],
                'name'     => $data['name'],
                'email'    => $data['email'],
                'phone'    => $data['phone'] ?? null,
                'password' => $data['password'],
            ]);

            $this->otpService->send(
                identifier: $account->email,
                purpose: OtpPurposeEnum::REGISTER,
            );

            return [
                'token'   => $this->tokenHelper->createToken($account, $deviceName),
                'account' => $account,
            ];
        });
    }
}
