<?php

namespace App\Livewire\TeacherPanel\Concerns\Dashboard;

use App\Models\GroupSchedule;

trait BuildsStaffStats
{
    protected function buildStaffInfo($user, $ownerId): array
    {
        $today = now()->format('l');

        return [
            'my_branches'        => $user->branches()->orderBy('name')->get(),
            'my_schedules_count' => GroupSchedule::where('teacher_id', $user->id)
                ->where('day', $today)
                ->whereHas('group', fn($q) => $q->where('teacher_id', $ownerId))
                ->count(),
        ];
    }
}
