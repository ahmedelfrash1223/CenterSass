<?php

namespace App\Livewire\TeacherPanel\Concerns\Dashboard;

use App\Models\Branch;
use App\Models\GroupSchedule;
use App\Models\Teacher;

trait BuildsOwnerStats
{
    protected function buildOwnerStats($user, $ownerId): array
    {
        $today = now()->format('l');

        $branchStats = Branch::where('teacher_id', $ownerId)
            ->withCount([
                'students as students_count',
                'groups as groups_count',
            ])
            ->orderBy('name')
            ->get()
            ->map(function ($branch) use ($today) {
                $branch->today_schedules_count = GroupSchedule::where('day', $today)
                    ->whereHas('group', fn($q) => $q->where('branch_id', $branch->id))
                    ->count();
                return $branch;
            });

        return [
            'staff_count'        => Teacher::where('owner_id', $user->id)->count(),
            'active_staff_count' => Teacher::where('owner_id', $user->id)
                ->where('is_active', 1)
                ->count(),
            'subscription'       => $user->activeSubscription,
            'branch_stats'       => $branchStats,
        ];
    }
}
