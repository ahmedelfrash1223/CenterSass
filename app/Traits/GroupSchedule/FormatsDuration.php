<?php

namespace App\Traits\GroupSchedule;

trait FormatsDuration
{
    public function getFormattedHoursAttribute(): string
    {
        if (! $this->hours) {
            return '—';
        }

        $totalMinutes = (int) round($this->hours * 60);
        $h = intdiv($totalMinutes, 60);
        $m = $totalMinutes % 60;

        if ($h > 0 && $m > 0) {
            return "{$h} س {$m} د";
        }
        if ($h > 0) {
            return "{$h} س";
        }

        return "{$m} د";
    }
}
