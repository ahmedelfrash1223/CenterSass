<?php

namespace App\Traits\Admin;

use App\Models\Admin;
use Illuminate\Support\Str;

trait HasIdentityDefaults
{
    protected static function booted(): void
    {
        static::creating(function (Admin $admin) {
            $admin->uuid ??= (string) Str::uuid();
            $admin->code ??= 'ADM-'.strtoupper(uniqid());
        });
    }
}
