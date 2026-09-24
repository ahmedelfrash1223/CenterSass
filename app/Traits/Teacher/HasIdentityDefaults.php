<?php

namespace App\Traits\Teacher;

use App\Enums\Teacher\TeacherRoleEnum;
use App\Models\Teacher;
use Illuminate\Support\Str;

trait HasIdentityDefaults
{
    protected static function booted(): void
    {
        static::creating(function (Teacher $teacher) {
            $teacher->uuid ??= (string) Str::uuid();
            $teacher->code ??= 'TCH-'.strtoupper(uniqid());
            $teacher->role ??= TeacherRoleEnum::OWNER->value;
        });

        static::created(function (Teacher $teacher) {
            $teacher->syncDefaultPermissions();
        });
    }
}
