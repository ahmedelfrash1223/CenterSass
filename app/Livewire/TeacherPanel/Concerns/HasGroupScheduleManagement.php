<?php

namespace App\Livewire\TeacherPanel\Concerns;

use App\Models\TeacherSubject;
use Carbon\Carbon;
use Illuminate\Support\Collection;

trait HasGroupScheduleManagement
{
    /**
     * Load subjects for a specific grade, scoped to the owner.
     */
    protected function loadSubjectsForGrade(string $grade, int $ownerId): Collection
    {
        if (!$grade) {
            return collect();
        }

        return TeacherSubject::query()
            ->where('teacher_id', $ownerId)
            ->where('grade', $grade)
            ->orderBy('subject_label')
            ->get();
    }

    /**
     * Compute duration in minutes from a schedule row.
     * Returns null if times are invalid or end <= start.
     */
    protected function computeScheduleMinutes(array $schedule): ?int
    {
        if (empty($schedule['start_time']) || empty($schedule['end_time'])) {
            return null;
        }

        try {
            $start = Carbon::createFromFormat('H:i', $schedule['start_time']);
            $end = Carbon::createFromFormat('H:i', $schedule['end_time']);
        } catch (\Exception $e) {
            return null;
        }

        if ($end->lessThanOrEqualTo($start)) {
            return null;
        }

        return $start->diffInMinutes($end);
    }

    /**
     * Format minutes to human-readable: "2 س 30 د".
     */
    protected function formatMinutes(int $minutes): string
    {
        $hours = intdiv($minutes, 60);
        $mins = $minutes % 60;

        if ($hours > 0 && $mins > 0) {
            return "{$hours} س {$mins} د";
        }

        if ($hours > 0) {
            return "{$hours} س";
        }

        return "{$mins} د";
    }

    /**
     * Find a duplicate row (same day + same subject) for the given index.
     * Returns the conflicting index or null.
     */
    protected function findDuplicateScheduleIndex(array $schedules, int $currentIndex): ?int
    {
        $current = $schedules[$currentIndex] ?? null;

        if (!$current || empty($current['day']) || empty($current['subject_id'])) {
            return null;
        }

        foreach ($schedules as $index => $schedule) {
            if ($index === $currentIndex) {
                continue;
            }

            if (
                ($schedule['day'] ?? '') === $current['day']
                && (string)($schedule['subject_id'] ?? '') === (string)$current['subject_id']
            ) {
                return $index;
            }
        }

        return null;
    }

    /**
     * Find all duplicate schedules (same day + same subject).
     */
    protected function findAllDuplicateSchedules(array $schedules): array
    {
        $seen = [];
        $duplicates = [];

        foreach ($schedules as $index => $schedule) {
            $day = $schedule['day'] ?? '';
            $subjectId = $schedule['subject_id'] ?? '';

            if ($day === '' || $subjectId === '') {
                continue;
            }

            $key = $day . '|' . $subjectId;

            if (isset($seen[$key])) {
                $duplicates[] = [
                    'row_index' => $index,
                    'duplicate_of' => $seen[$key],
                    'day' => $day,
                    'subject_id' => (string)$subjectId,
                ];
            } else {
                $seen[$key] = $index;
            }
        }

        return $duplicates;
    }

    /**
     * Check whether a schedule row has every required field filled with valid times.
     */
    protected function isScheduleComplete(array $schedule): bool
    {
        return !empty($schedule['day'])
            && !empty($schedule['start_time'])
            && !empty($schedule['end_time'])
            && !empty($schedule['subject_id'])
            && !empty($schedule['teacher_id'])
            && $this->computeScheduleMinutes($schedule) !== null;
    }

    /**
     * Total weekly minutes across all schedules.
     */
    protected function getTotalWeeklyMinutes(array $schedules): int
    {
        $total = 0;

        foreach ($schedules as $schedule) {
            $minutes = $this->computeScheduleMinutes($schedule);
            if ($minutes !== null) {
                $total += $minutes;
            }
        }

        return $total;
    }

    /**
     * Human-readable Arabic day label.
     */
    protected function getDayLabel(string $day): string
    {
        return [
            'Saturday' => 'السبت',
            'Sunday' => 'الأحد',
            'Monday' => 'الإثنين',
            'Tuesday' => 'الثلاثاء',
            'Wednesday' => 'الأربعاء',
            'Thursday' => 'الخميس',
            'Friday' => 'الجمعة',
        ][$day] ?? $day;
    }

    /**
     * Convert minutes to decimal hours (e.g. 90 → 1.5).
     */
    protected function minutesToHours(int $minutes): float
    {
        return round($minutes / 60, 1);
    }

    /**
     * Enrich a schedule row with computed hours.
     * Returns the row with an `hours` key.
     */
    protected function enrichScheduleWithHours(array $schedule): array
    {
        $minutes = $this->computeScheduleMinutes($schedule);

        $schedule['hours'] = $minutes !== null
            ? $this->minutesToHours($minutes)
            : 0;

        return $schedule;
    }
}
