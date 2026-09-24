<?php

namespace App\Livewire\TeacherPanel\Concerns\Timetable;

trait FormatsTimetableDays
{
    protected function arabicDays(): array
    {
        return [
            'Saturday'  => 'السبت',
            'Sunday'    => 'الأحد',
            'Monday'    => 'الإثنين',
            'Tuesday'   => 'الثلاثاء',
            'Wednesday' => 'الأربعاء',
            'Thursday'  => 'الخميس',
            'Friday'    => 'الجمعة',
        ];
    }

    protected function firstDayKey(): string
    {
        return array_key_first($this->arabicDays());
    }
}
