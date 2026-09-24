<?php

namespace App\Livewire\TeacherPanel\Concerns\Dashboard;

use App\Models\GroupSchedule;

trait BuildsScheduleStats
{
    protected function buildSchedulesQuery($user, $ownerId, bool $isOwner, array $accessibleBranchIds)
    {
        $today = now()->format('l');

        $query = GroupSchedule::query()
            ->with(['group.branch', 'subject', 'teacher'])
            ->where('day', $today)
            ->whereHas('group', function ($q) use ($ownerId, $isOwner, $accessibleBranchIds) {
                $q->where('teacher_id', $ownerId);

                if (!$isOwner && !empty($accessibleBranchIds)) {
                    $q->whereIn('branch_id', $accessibleBranchIds);
                } elseif (!$isOwner && empty($accessibleBranchIds)) {
                    $q->whereRaw('1 = 0');
                }
            });

        if (!$isOwner) {
            $query->where('teacher_id', $user->id);
        }

        return $query->orderBy('start_time');
    }

    protected function buildTodaySchedules($schedules): array
    {
        $now = now()->format('H:i');

        $current = $schedules->filter(function ($s) use ($now) {
            return $s->start_time->format('H:i') <= $now
                && $s->end_time->format('H:i') >= $now;
        })->values();

        $upcoming = $schedules->filter(function ($s) use ($now) {
            return $s->start_time->format('H:i') >= $now;
        })->values();

        return [
            'all'      => $schedules,
            'current'  => $current,
            'upcoming' => $upcoming,
            'next'     => $upcoming->first(),
        ];
    }
}
