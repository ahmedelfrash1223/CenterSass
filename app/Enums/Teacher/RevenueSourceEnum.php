<?php

namespace App\Enums\Teacher;

enum RevenueSourceEnum: string
{
    case STUDENT_PAYMENT = 'student_payment';
    case CARD_REQUEST = 'card_request';
    case SUBSCRIPTION = 'subscription';
    case BOOK_SALE = 'book_sale';
    case TRIP = 'trip';
    case EXAM_FEE = 'exam_fee';
    case CERTIFICATE = 'certificate';
    case OTHER = 'other';

    public function label(): string
    {
        return match ($this) {
            self::STUDENT_PAYMENT => 'دفعة طالب',
            self::CARD_REQUEST => 'طلب كروت',
            self::SUBSCRIPTION => 'اشتراك المنصة',
            self::BOOK_SALE => 'بيع كتب',
            self::TRIP => 'رحلات',
            self::EXAM_FEE => 'رسوم امتحانات',
            self::CERTIFICATE => 'شهادات',
            self::OTHER => 'أخرى',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::STUDENT_PAYMENT => 'success',
            self::CARD_REQUEST => 'primary',
            self::SUBSCRIPTION => 'info',
            self::BOOK_SALE => 'warning',
            self::TRIP => 'info',
            self::EXAM_FEE => 'primary',
            self::CERTIFICATE => 'success',
            self::OTHER => 'secondary',
        };
    }

    public function textColor(): string
    {
        return in_array($this->color(), ['warning', 'info']) ? 'text-dark' : 'text-white';
    }

    public function icon(): string
    {
        return match ($this) {
            self::STUDENT_PAYMENT => 'ph-receipt',
            self::CARD_REQUEST => 'ph-identification-card',
            self::SUBSCRIPTION => 'ph-crown',
            self::BOOK_SALE => 'ph-book',
            self::TRIP => 'ph-bus',
            self::EXAM_FEE => 'ph-exam',
            self::CERTIFICATE => 'ph-certificate',
            self::OTHER => 'ph-dots-three-circle',
        };
    }

    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn (self $s) => [$s->value => $s->label()])
            ->toArray();
    }
}
