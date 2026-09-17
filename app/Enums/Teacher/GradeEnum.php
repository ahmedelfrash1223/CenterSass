<?php

namespace App\Enums\Teacher;

enum GradeEnum: string
{
    case PRIMARY_1 = 'primary_1';
    case PRIMARY_2 = 'primary_2';
    case PRIMARY_3 = 'primary_3';
    case PRIMARY_4 = 'primary_4';
    case PRIMARY_5 = 'primary_5';
    case PRIMARY_6 = 'primary_6';
    case PREP_1 = 'prep_1';
    case PREP_2 = 'prep_2';
    case PREP_3 = 'prep_3';
    case SECONDARY_1 = 'secondary_1';
    case SECONDARY_2 = 'secondary_2';
    case SECONDARY_3 = 'secondary_3';

    public function label(): string
    {
        return match ($this) {
            self::PRIMARY_1 => 'الصف الأول الابتدائي',
            self::PRIMARY_2 => 'الصف الثاني الابتدائي',
            self::PRIMARY_3 => 'الصف الثالث الابتدائي',
            self::PRIMARY_4 => 'الصف الرابع الابتدائي',
            self::PRIMARY_5 => 'الصف الخامس الابتدائي',
            self::PRIMARY_6 => 'الصف السادس الابتدائي',
            self::PREP_1 => 'الصف الأول الإعدادي',
            self::PREP_2 => 'الصف الثاني الإعدادي',
            self::PREP_3 => 'الصف الثالث الإعدادي',
            self::SECONDARY_1 => 'الصف الأول الثانوي',
            self::SECONDARY_2 => 'الصف الثاني الثانوي',
            self::SECONDARY_3 => 'الصف الثالث الثانوي',
        };
    }

    /**
     * Short numeric code per grade (2 digits).
     * Used to build student barcode numbers.
     */
    public function code(): string
    {
        return match ($this) {
            self::PRIMARY_1   => '10',
            self::PRIMARY_2   => '11',
            self::PRIMARY_3   => '12',
            self::PRIMARY_4   => '13',
            self::PRIMARY_5   => '14',
            self::PRIMARY_6   => '15',
            self::PREP_1      => '20',
            self::PREP_2      => '21',
            self::PREP_3      => '22',
            self::SECONDARY_1 => '30',
            self::SECONDARY_2 => '31',
            self::SECONDARY_3 => '32',
        };
    }

    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn (self $grade) => [$grade->value => $grade->label()])
            ->toArray();
    }
}
