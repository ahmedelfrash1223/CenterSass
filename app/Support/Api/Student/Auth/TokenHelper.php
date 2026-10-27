<?php

namespace App\Support\Api\Student\Auth;

use App\Models\StudentAccount;

class TokenHelper
{
    /**
     * Create a Sanctum token for the given account.
     */
    public function createToken(StudentAccount $account, string $deviceName = 'mobile'): string
    {
        return $account->createToken($deviceName)->plainTextToken;
    }
}
