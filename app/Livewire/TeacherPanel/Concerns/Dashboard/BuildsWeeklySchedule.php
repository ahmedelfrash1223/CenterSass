<?php

namespace App\Livewire\TeacherPanel\Concerns\Dashboard;

use App\Models\GroupSchedule;

trait BuildsWeeklySchedule
{
    protected function buildWeeklySchedule($user, $ownerId, bool $isOwner, array $accessibleBranchIds): array
    {
        $days = [
            'Saturday'  => 'السبت',
            'Sunday'    => 'الأحد',
            'Monday'    => 'الإثنين',
            'Tuesday'   => 'الثلاثاء',
            'Wednesday' => 'الأربعاء',
            'Thursday'  => 'الخميس',
            'Friday'    => 'الجمعة',
        ];

        $data = [];

        foreach ($days as $dayEn => $dayAr) {
            $query = GroupSchedule::where('day', $dayEn)
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

            $data[$dayAr] = $query->count();
        }

        return $data;
    }
}
