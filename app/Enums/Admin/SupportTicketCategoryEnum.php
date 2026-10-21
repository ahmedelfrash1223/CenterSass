<?php

namespace App\Enums\Admin;

enum SupportTicketCategoryEnum: string
{
    case TECHNICAL = 'technical';
    case BILLING = 'billing';
    case FEATURE = 'feature';
    case BUG = 'bug';
    case ACCOUNT = 'account';
    case OTHER = 'other';

    public function label(): string
    {
        return match ($this) {
            self::TECHNICAL => 'مشكلة تقنية',
            self::BILLING => 'الفواتير والمدفوعات',
            self::FEATURE => 'اقتراح ميزة',
            self::BUG => 'بلاغ عن خطأ',
            self::ACCOUNT => 'الحساب والصلاحيات',
            self::OTHER => 'استفسار آخر',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::TECHNICAL => 'ph-wrench',
            self::BILLING => 'ph-receipt',
            self::FEATURE => 'ph-lightbulb',
            self::BUG => 'ph-bug',
            self::ACCOUNT => 'ph-user-circle',
            self::OTHER => 'ph-question',
        };
    }

    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn (self $category) => [$category->value => $category->label()])
            ->toArray();
    }
}
