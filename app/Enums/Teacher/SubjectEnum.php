<?php

namespace App\Enums\Teacher;

enum SubjectEnum: string
{
    case ARABIC = 'arabic';
    case ENGLISH = 'english';
    case FRENCH = 'french';
    case MATH = 'math';
    case SCIENCE = 'science';
    case PHYSICS = 'physics';
    case CHEMISTRY = 'chemistry';
    case BIOLOGY = 'biology';
    case HISTORY = 'history';
    case GEOGRAPHY = 'geography';
    case ISLAMIC = 'islamic';
    case ART = 'art';
    case MUSIC = 'music';
    case PHYSICAL_EDUCATION = 'physical_education';
    case COMPUTER = 'computer';
    case SOCIAL_STUDIES = 'social_studies';

    public function label(): string
    {
        return match ($this) {
            self::ARABIC => 'اللغة العربية',
            self::ENGLISH => 'اللغة الإنجليزية',
            self::FRENCH => 'اللغة الفرنسية',
            self::MATH => 'الرياضيات',
            self::SCIENCE => 'العلوم',
            self::PHYSICS => 'الفيزياء',
            self::CHEMISTRY => 'الكيمياء',
            self::BIOLOGY => 'الأحياء',
            self::HISTORY => 'التاريخ',
            self::GEOGRAPHY => 'الجغرافيا',
            self::ISLAMIC => 'التربية الإسلامية',
            self::ART => 'التربية الفنية',
            self::MUSIC => 'الموسيقى',
            self::PHYSICAL_EDUCATION => 'التربية الرياضية',
            self::COMPUTER => 'الحاسب الآلي',
            self::SOCIAL_STUDIES => 'الدراسات الاجتماعية',
        };
    }

    public static function options(): array
    {
        return collect(self::cases())->mapWithKeys(fn (self $subject) => [$subject->value => $subject->label()])->toArray();
    }
}