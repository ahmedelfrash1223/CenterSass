<?php

namespace App\Traits\Student;

use App\Enums\Student\AuthProviderEnum;
use App\Enums\Student\StudentAccountStatusEnum;
use App\Models\StudentAccount;
use Illuminate\Support\Str;

trait HasAccountIdentityDefaults
{
    protected static function bootHasAccountIdentityDefaults(): void
    {
        static::creating(function (StudentAccount $account) {
            $account->uuid ??= (string) Str::uuid();
            $account->provider ??= AuthProviderEnum::EMAIL;
            $account->status ??= StudentAccountStatusEnum::ACTIVE;
            $account->is_active ??= true;
        });
    }
}
