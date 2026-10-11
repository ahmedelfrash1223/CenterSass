<?php

namespace App\Enums\Teacher;

enum AssignmentGradeEnum: string
{
    case EXCELLENT = 'excellent';
    case VERY_GOOD = 'very_good';
    case GOOD = 'good';
    case PASS = 'pass';
    case FAIL = 'fail';

    public function label(): string
    {
        return match ($this) {
            self::EXCELLENT => 'ممتاز',
            self::VERY_GOOD => 'جيد جدًا',
            self::GOOD => 'جيد',
            self::PASS => 'مقبول',
            self::FAIL => 'ضعيف',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::EXCELLENT => 'success',
            self::VERY_GOOD => 'info',
            self::GOOD => 'primary',
            self::PASS => 'warning',
            self::FAIL => 'danger',
        };
    }

    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn (self $case) => [$case->value => $case->label()])
            ->toArray();
    }

    public static function fromPercentage(float $percentage): self
    {
        return match (true) {
            $percentage >= 90 => self::EXCELLENT,
            $percentage >= 80 => self::VERY_GOOD,
            $percentage >= 70 => self::GOOD,
            $percentage >= 50 => self::PASS,
            default => self::FAIL,
        };
    }
}
