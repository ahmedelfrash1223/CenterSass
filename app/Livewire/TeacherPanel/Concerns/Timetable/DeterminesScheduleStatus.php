<?php

namespace App\Livewire\TeacherPanel\Concerns\Timetable;

use Carbon\Carbon;

trait DeterminesScheduleStatus
{
    public function getScheduleStatus($schedule, bool $isToday): string
    {
        if (!$isToday) {
            return 'upcoming';
        }

        $now = now()->format('H:i');

        $start = is_array($schedule)
            ? Carbon::parse($schedule['start_time'])->format('H:i')
            : $schedule->start_time->format('H:i');

        $end = is_array($schedule)
            ? Carbon::parse($schedule['end_time'])->format('H:i')
            : $schedule->end_time->format('H:i');

        if ($start <= $now && $end >= $now) {
            return 'running';
        }

        if ($end < $now) {
            return 'finished';
        }

        return 'upcoming';
    }

    public function getScheduleStatusColor(string $status): string
    {
        return match ($status) {
            'running'  => 'danger',
            'finished' => 'secondary',
            default    => 'primary',
        };
    }

    public function getScheduleStatusLabel(string $status): string
    {
        return match ($status) {
            'running'  => 'جارية الآن',
            'finished' => 'خلصت',
            default    => 'لم تبدأ',
        };
    }

    public function getScheduleStatusIcon(string $status): string
    {
        return match ($status) {
            'running'  => 'ph-broadcast',
            'finished' => 'ph-check',
            default    => 'ph-clock',
        };
    }
}
