<?php

namespace App\Services\Api\Student\Auth;

use App\Models\StudentAccount;

class LogoutService
{
    public function logout(StudentAccount $account): void
    {
        $account->currentAccessToken()?->delete();
    }
}
