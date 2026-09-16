<?php

namespace App\Enums\Teacher;

enum TeacherRoleEnum: int
{
    case OWNER = 1;
    case STAFF = 2;
    case ASSISTANT = 3;

    public function label(): string
    {
        return match ($this) {
            self::OWNER => 'مدرس مسئول',
            self::STAFF => 'مدرس',
            self::ASSISTANT => 'مساعد',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::OWNER => 'primary',
            self::STAFF => 'info',
            self::ASSISTANT => 'warning',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::OWNER => 'ph-crown',
            self::STAFF => 'ph-chalkboard-teacher',
            self::ASSISTANT => 'ph-user-gear',
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::OWNER => 'صاحب الحساب والاشتراك - كل الصلاحيات',
            self::STAFF => 'مدرس يشرح المواد ويدير الطلاب',
            self::ASSISTANT => 'مساعد بإمكانيات محدودة',
        };
    }

    public function isOwner(): bool
    {
        return $this === self::OWNER;
    }

    public function isStaff(): bool
    {
        return $this === self::STAFF;
    }

    public function isAssistant(): bool
    {
        return $this === self::ASSISTANT;
    }

    public static function options(): array
    {
        return collect(self::cases())->mapWithKeys(fn(self $role) => [
            $role->value => $role->label(),
        ])->toArray();
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
