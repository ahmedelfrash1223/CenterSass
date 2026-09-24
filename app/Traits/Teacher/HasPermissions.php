<?php

namespace App\Traits\Teacher;

use App\Enums\Teacher\TeacherPermissionEnum;
use App\Models\TeacherPermission;
use Illuminate\Database\Eloquent\Relations\HasMany;

trait HasPermissions
{
    public function permissions(): HasMany
    {
        return $this->hasMany(TeacherPermission::class, 'teacher_id');
    }

    public function hasPermission(string|TeacherPermissionEnum $permission): bool
    {
        if (method_exists($this, 'isOwner') && $this->isOwner()) {
            return true;
        }

        $permissionValue = $permission instanceof TeacherPermissionEnum
            ? $permission->value
            : $permission;

        return $this->permissions()
            ->where('permission', $permissionValue)
            ->exists();
    }

    public function hasAllPermissions(array $permissions): bool
    {
        foreach ($permissions as $permission) {
            if (! $this->hasPermission($permission)) {
                return false;
            }
        }

        return true;
    }

    public function hasAnyPermission(array $permissions): bool
    {
        foreach ($permissions as $permission) {
            if ($this->hasPermission($permission)) {
                return true;
            }
        }

        return false;
    }

    public function getAllPermissions(): array
    {
        if (method_exists($this, 'isOwner') && $this->isOwner()) {
            return TeacherPermissionEnum::values();
        }

        return $this->permissions()->pluck('permission')->toArray();
    }

    public function givePermission(string|TeacherPermissionEnum $permission): void
    {
        $permissionValue = $permission instanceof TeacherPermissionEnum
            ? $permission->value
            : $permission;

        $this->permissions()->firstOrCreate([
            'permission' => $permissionValue,
        ]);
    }

    public function revokePermission(string|TeacherPermissionEnum $permission): void
    {
        $permissionValue = $permission instanceof TeacherPermissionEnum
            ? $permission->value
            : $permission;

        $this->permissions()->where('permission', $permissionValue)->delete();
    }

    public function syncPermissions(array $permissions): void
    {
        $this->permissions()->delete();

        foreach ($permissions as $permission) {
            $permissionValue = $permission instanceof TeacherPermissionEnum
                ? $permission->value
                : $permission;

            $this->permissions()->create([
                'permission' => $permissionValue,
            ]);
        }
    }

    public function syncDefaultPermissions(): void
    {
        if (method_exists($this, 'isOwner') && $this->isOwner()) {
            return;
        }

        $defaults = TeacherPermissionEnum::forRole($this->role);

        $this->syncPermissions($defaults);
    }
}
