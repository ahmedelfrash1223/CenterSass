<?php

namespace App\Livewire\TeacherPanel\Concerns\Shared;

use App\Models\TeacherSubject;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

trait HasGroupScheduleManagement
{
    protected function teachersForGrade(string $grade, int $ownerId): Collection
    {
        if (!$grade) return collect();

        $fromCreatedBy = TeacherSubject::query()
            ->where('teacher_id', $ownerId)
            ->where('grade', $grade)
            ->whereNotNull('created_by')
            ->pluck('created_by')
            ->toArray();

        $fromAssignments = DB::table('teacher_grade_subjects')
            ->join('teacher_subjects', 'teacher_subjects.id', '=', 'teacher_grade_subjects.teacher_subject_id')
            ->where('teacher_subjects.teacher_id', $ownerId)
            ->where('teacher_subjects.grade', $grade)
            ->pluck('teacher_grade_subjects.teacher_id')
            ->toArray();

        $teacherIds = array_unique(array_merge(
            $fromCreatedBy,
            $fromAssignments,
            [$ownerId]
        ));

        return \App\Models\Teacher::query()
            ->whereIn('id', $teacherIds)
            ->where(function ($q) use ($ownerId) {
                $q->where('id', $ownerId)
                    ->orWhere('owner_id', $ownerId);
            })
            ->where('is_active', 1)
            ->orderBy('name')
            ->get();
    }

    protected function subjectsForTeacherAndGrade(int $teacherId, string $grade, int $ownerId): Collection
    {
        if (!$teacherId || !$grade) return collect();

        $assignedIds = DB::table('teacher_grade_subjects')
            ->join('teacher_subjects', 'teacher_subjects.id', '=', 'teacher_grade_subjects.teacher_subject_id')
            ->where('teacher_grade_subjects.teacher_id', $teacherId)
            ->where('teacher_subjects.teacher_id', $ownerId)
            ->where('teacher_subjects.grade', $grade)
            ->pluck('teacher_subjects.id')
            ->toArray();

        $createdByIds = TeacherSubject::query()
            ->where('teacher_id', $ownerId)
            ->where('grade', $grade)
            ->where('created_by', $teacherId)
            ->pluck('id')
            ->toArray();

        $ownerIds = [];
        if ($teacherId === $ownerId) {
            $ownerIds = TeacherSubject::query()
                ->where('teacher_id', $ownerId)
                ->where('grade', $grade)
                ->pluck('id')
                ->toArray();
        }

        $allIds = array_unique(array_merge($assignedIds, $createdByIds, $ownerIds));

        if (empty($allIds)) return collect();

        return TeacherSubject::query()
            ->whereIn('id', $allIds)
            ->orderBy('subject')
            ->get();
    }

    protected function loadSubjectsForGrade(string $grade, int $ownerId): Collection
    {
        if (!$grade) return collect();

        return TeacherSubject::query()
            ->where('teacher_id', $ownerId)
            ->where('grade', $grade)
            ->orderBy('subject')
            ->get();
    }

    protected function expandRecurringSchedules(array $schedules): array
    {
        $expanded = [];

        foreach ($schedules as $schedule) {
            $days = $schedule['days'] ?? [];
            if (empty($days)) continue;

            foreach ($days as $day) {
                $row = $schedule;
                $row['day'] = $day;
                $expanded[] = $row;
            }
        }

        return $expanded;
    }

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

        if ($end->lessThanOrEqualTo($start)) return null;

        return $start->diffInMinutes($end);
    }

    protected function formatMinutes(int $minutes): string
    {
        $hours = intdiv($minutes, 60);
        $mins = $minutes % 60;

        if ($hours > 0 && $mins > 0) return "{$hours} س {$mins} د";
        if ($hours > 0) return "{$hours} س";
        return "{$mins} د";
    }

    protected function findDuplicateScheduleIndex(array $schedules, int $currentIndex): ?int
    {
        $current = $schedules[$currentIndex] ?? null;
        if (!$current) return null;

        $currentDays = $current['days'] ?? [];
        $currentSubject = (string)($current['subject_id'] ?? '');

        if (empty($currentDays) || $currentSubject === '') return null;

        foreach ($schedules as $index => $schedule) {
            if ($index === $currentIndex) continue;

            $days = $schedule['days'] ?? [];
            $subject = (string)($schedule['subject_id'] ?? '');

            if ($subject !== $currentSubject) continue;

            if (!empty(array_intersect($currentDays, $days))) {
                return $index;
            }
        }

        return null;
    }

    protected function findAllDuplicateSchedules(array $schedules): array
    {
        $duplicates = [];

        foreach ($schedules as $i => $scheduleA) {
            $daysA = $scheduleA['days'] ?? [];
            $subjectA = (string)($scheduleA['subject_id'] ?? '');

            if (empty($daysA) || $subjectA === '') continue;

            foreach ($schedules as $j => $scheduleB) {
                if ($j <= $i) continue;

                $daysB = $scheduleB['days'] ?? [];
                $subjectB = (string)($scheduleB['subject_id'] ?? '');

                if ($subjectB !== $subjectA) continue;

                foreach (array_intersect($daysA, $daysB) as $day) {
                    $duplicates[] = [
                        'row_index'    => $i,
                        'duplicate_of' => $j,
                        'day'          => $day,
                        'subject_id'   => $subjectA,
                    ];
                }
            }
        }

        return $duplicates;
    }

    protected function isScheduleComplete(array $schedule): bool
    {
        $days = $schedule['days'] ?? [];

        return !empty($days)
            && !empty($schedule['start_time'])
            && !empty($schedule['end_time'])
            && !empty($schedule['subject_id'])
            && !empty($schedule['teacher_id'])
            && $this->computeScheduleMinutes($schedule) !== null;
    }

    protected function getDayLabel(string $day): string
    {
        return [
            'Saturday'  => 'السبت',
            'Sunday'    => 'الأحد',
            'Monday'    => 'الإثنين',
            'Tuesday'   => 'الثلاثاء',
            'Wednesday' => 'الأربعاء',
            'Thursday'  => 'الخميس',
            'Friday'    => 'الجمعة',
        ][$day] ?? $day;
    }

    protected function hourOptions(): array
    {
        $hours = [];
        for ($h = 7; $h <= 23; $h++) {
            $val = str_pad((string)$h, 2, '0', STR_PAD_LEFT);
            $hours[$val] = $val;
        }
        return $hours;
    }


    protected function minuteOptions(): array
    {
        $minutes = [];

        for ($m = 0; $m < 60; $m += 15) {
            $val = str_pad((string)$m, 2, '0', STR_PAD_LEFT);
            $minutes[$val] = $val;
        }

        return $minutes;
    }
}
