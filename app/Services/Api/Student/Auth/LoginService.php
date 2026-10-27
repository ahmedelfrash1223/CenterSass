<?php

namespace App\Services\Api\Student\Auth;

use App\Models\StudentAccount;
use App\Support\Api\ApiResponse;
use App\Support\Api\Student\Auth\TokenHelper;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class LoginService
{
    public function __construct(
        private readonly TokenHelper $tokenHelper,
    ) {}

    /**
     * @return array{token:string,account:StudentAccount}
     *
     * @throws ValidationException
     */
    public function login(string $login, string $password, string $deviceName): array
    {
        $field = filter_var($login, FILTER_VALIDATE_EMAIL) ? 'email' : 'phone';

        $account = StudentAccount::where($field, $login)->first();

        if (! $account || ! Hash::check($password, $account->password)) {
            throw ValidationException::withMessages([
                'login' => ['بيانات تسجيل الدخول غير صحيحة.'],
            ]);
        }

        if (! $account->canLogin()) {
            throw ValidationException::withMessages([
                'login' => ['هذا الحساب غير نشط. تواصل مع الدعم.'],
            ]);
        }

        if (! $account->isEmailVerified() && $field === 'email') {
            throw ValidationException::withMessages([
                'login' => ['يرجى تفعيل البريد الإلكتروني أولاً.'],
            ]);
        }

        return [
            'token' => $this->tokenHelper->createToken($account, $deviceName),
            'account' => $account,
        ];
    }
}
