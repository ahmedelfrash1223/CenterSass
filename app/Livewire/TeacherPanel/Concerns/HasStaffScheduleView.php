<?php

namespace App\Livewire\TeacherPanel\Concerns;

use App\Models\GroupSchedule;
use App\Models\Teacher;

trait HasStaffScheduleView
{
    /**
     * Get schedules for a staff member, scoped to the viewer's accessible branches.
     * Returns a grouped collection by day, sorted by day order then start time.
     */
    protected function loadStaffSchedules(Teacher $teacher): \Illuminate\Support\Collection
    {
        $user = auth('teacher')->user();
        $ownerId = $user->getOwnerId();
        $accessibleBranchIds = $user->getAccessibleBranchIds();

        $query = GroupSchedule::query()
            ->with(['group.branch', 'subject'])
            ->where('teacher_id', $teacher->id)
            ->whereHas('group', function ($q) use ($user, $ownerId, $accessibleBranchIds) {
                $q->where('teacher_id', $ownerId);

                // Non-owner: only show schedules from accessible branches
                if (!$user->isOwner()) {
                    empty($accessibleBranchIds)
                        ? $q->whereRaw('1 = 0')
                        : $q->whereIn('branch_id', $accessibleBranchIds);
                }
            });

        $dayOrder = [
            'Saturday' => 1, 'Sunday' => 2, 'Monday' => 3,
            'Tuesday' => 4, 'Wednesday' => 5, 'Thursday' => 6, 'Friday' => 7,
        ];

        return $query->get()
            ->sortBy(fn($s) => [$dayOrder[$s->day] ?? 99, $s->start_time->format('H:i')])
            ->groupBy('day');
    }
}
