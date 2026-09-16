<?php

namespace App\Traits\Teacher;

use App\Enums\Teacher\TeacherPermissionEnum;

trait HasPermissionSelection
{
    public array $permissions = [];
    public array $groupedPermissions = [];


    public function initializePermissions(): void
    {
        foreach (TeacherPermissionEnum::cases() as $permission) {
            $this->permissions[$permission->value] = false;
        }
    }


    public function getSelectedCountProperty(): int
    {
        return count(array_filter($this->permissions, fn($v) => $v === true));
    }


    public function togglePermission(string $permission): void
    {
        $this->permissions[$permission] = !($this->permissions[$permission] ?? false);
    }


    public function toggleGroup(string $groupName): void
    {
        $groupPermissions = collect($this->groupedPermissions[$groupName] ?? [])
            ->pluck('value')
            ->toArray();

        $allSelected = true;
        foreach ($groupPermissions as $key) {
            if (!($this->permissions[$key] ?? false)) {
                $allSelected = false;
                break;
            }
        }

        foreach ($groupPermissions as $key) {
            $this->permissions[$key] = !$allSelected;
        }
    }


    public function selectAll(): void
    {
        foreach ($this->permissions as $key => $value) {
            $this->permissions[$key] = true;
        }
    }


    public function deselectAll(): void
    {
        foreach ($this->permissions as $key => $value) {
            $this->permissions[$key] = false;
        }
    }


    public function getSelectedPermissions(): array
    {
        return collect($this->permissions)
            ->filter(fn($value) => $value === true)
            ->keys()
            ->toArray();
    }
}
