<?php

namespace App\Livewire\TeacherPanel\Concerns\Dashboard;

use App\Models\GroupSchedule;

trait BuildsWeeklyTimetable
{

    protected function buildWeeklyTimetable($user, $ownerId, bool $isOwner, array $accessibleBranchIds): array
    {
        $days = array_keys($this->arabicDays());

        $schedules = GroupSchedule::query()
            ->with(['group.branch', 'subject', 'teacher'])
            ->withCount(['group as students_count' => function ($q) {
                $q->where('group_student.status', 'active');
            }])
            ->whereHas('group', function ($q) use ($ownerId, $isOwner, $accessibleBranchIds) {
                $q->where('teacher_id', $ownerId);

                if (!$isOwner && !empty($accessibleBranchIds)) {
                    $q->whereIn('branch_id', $accessibleBranchIds);
                } elseif (!$isOwner && empty($accessibleBranchIds)) {
                    $q->whereRaw('1 = 0');
                }
            })
            ->when(!$isOwner, fn($q) => $q->where('teacher_id', $user->id))
            ->orderBy('start_time')
            ->get();

        $grouped = [];

        foreach ($days as $day) {
            $grouped[$day] = $schedules
                ->filter(fn($s) => $s->day === $day)
                ->values();
        }

        return $grouped;
    }


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


    protected function scheduleStatus(GroupSchedule $schedule, bool $isToday): string
    {
        if (!$isToday) {
            return 'upcoming';
        }

        $now   = now()->format('H:i');
        $start = $schedule->start_time->format('H:i');
        $end   = $schedule->end_time->format('H:i');

        if ($start <= $now && $end >= $now) {
            return 'running';
        }

        if ($end < $now) {
            return 'finished';
        }

        return 'upcoming';
    }
}
