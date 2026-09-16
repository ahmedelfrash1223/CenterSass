<?php

namespace App\Traits\Teacher;

use App\Enums\Teacher\TeacherRoleEnum;

trait HasRoles
{
    public function isOwner(): bool
    {
        return $this->role === TeacherRoleEnum::OWNER;
    }

    public function isStaff(): bool
    {
        return $this->role === TeacherRoleEnum::STAFF;
    }

    public function isAssistant(): bool
    {
        return $this->role === TeacherRoleEnum::ASSISTANT;
    }
}
