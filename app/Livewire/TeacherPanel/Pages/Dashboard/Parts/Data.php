<?php

namespace App\Livewire\TeacherPanel\Pages\Dashboard\Parts;

use App\Enums\Teacher\TeacherPermissionEnum;
use App\Models\Branch;
use App\Models\Group;
use App\Models\GroupSchedule;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\TeacherSubject;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Data extends Component
{
    public function render()
    {
        $user = Auth::guard('teacher')->user();
        $ownerId = $user->getOwnerId();
        $isOwner = $user->isOwner();

        $accessibleBranchIds = $user->getAccessibleBranchIds();

        $hasData = $isOwner || !empty($accessibleBranchIds);

        $studentsQuery = Student::where('teacher_id', $ownerId);
        $groupsQuery = Group::where('teacher_id', $ownerId);

        if (!$isOwner && !empty($accessibleBranchIds)) {
            $studentsQuery->whereIn('branch_id', $accessibleBranchIds);
            $groupsQuery->whereIn('branch_id', $accessibleBranchIds);
        } elseif (!$isOwner && empty($accessibleBranchIds)) {
            $studentsQuery->whereRaw('1 = 0');
            $groupsQuery->whereRaw('1 = 0');
        }

        $stats = [
            'students_count' => $studentsQuery->count(),
            'groups_count' => $groupsQuery->count(),
            'branches_count' => count($accessibleBranchIds),
            'subjects_count' => TeacherSubject::where('teacher_id', $ownerId)->count(),
        ];

        $today = now()->format('l');
        $now = now()->format('H:i');

        $schedulesQuery = GroupSchedule::query()
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
            $schedulesQuery->where('teacher_id', $user->id);
        }

        $todaySchedules = $schedulesQuery
            ->orderBy('start_time')
            ->get();

        $upcomingSchedules = $todaySchedules->filter(function ($s) use ($now) {
            return $s->start_time->format('H:i') >= $now;
        });

        $currentSchedule = $todaySchedules->first(function ($s) use ($now) {
            return $s->start_time->format('H:i') <= $now
                && $s->end_time->format('H:i') >= $now;
        });

        $nextSchedule = $upcomingSchedules->first();

        $ownerStats = null;
        if ($isOwner) {
            $ownerStats = [
                'staff_count' => Teacher::where('owner_id', $user->id)->count(),
                'active_staff_count' => Teacher::where('owner_id', $user->id)
                    ->where('is_active', 1)
                    ->count(),
                'subscription' => $user->activeSubscription,
            ];

            $branchStats = Branch::where('teacher_id', $ownerId)
                ->withCount([
                    'students as students_count',
                    'groups as groups_count',
                ])
                ->orderBy('name')
                ->get()
                ->map(function ($branch) use ($today) {
                    $branch->today_schedules_count = GroupSchedule::where('day', $today)
                        ->whereHas('group', function ($q) use ($branch) {
                            $q->where('branch_id', $branch->id);
                        })
                        ->count();
                    return $branch;
                });

            $ownerStats['branch_stats'] = $branchStats;
        }

        $staffInfo = null;
        if (!$isOwner) {
            $staffInfo = [
                'my_branches' => $user->branches()->orderBy('name')->get(),
                'my_schedules_count' => GroupSchedule::where('teacher_id', $user->id)
                    ->where('day', $today)
                    ->whereHas('group', function ($q) use ($ownerId) {
                        $q->where('teacher_id', $ownerId);
                    })
                    ->count(),
            ];
        }

        $weeklySchedule = $this->getWeeklyScheduleData($user, $ownerId, $accessibleBranchIds, $isOwner);

        $canViewGroups = $user->hasPermission(TeacherPermissionEnum::GROUP_VIEW);

        return view('teacher-panel.pages.dashboard.parts.data', [
            'isOwner' => $isOwner,
            'hasData' => $hasData,
            'stats' => $stats,
            'todaySchedules' => $todaySchedules,
            'currentSchedule' => $currentSchedule,
            'nextSchedule' => $nextSchedule,
            'upcomingSchedules' => $upcomingSchedules,
            'ownerStats' => $ownerStats,
            'staffInfo' => $staffInfo,
            'weeklySchedule' => $weeklySchedule,
            'today' => $today,
            'canViewGroups' => $canViewGroups,
        ]);
    }

    private function getWeeklyScheduleData($user, $ownerId, $accessibleBranchIds, $isOwner): array
    {
        $days = [
            'Saturday' => 'السبت',
            'Sunday' => 'الأحد',
            'Monday' => 'الإثنين',
            'Tuesday' => 'الثلاثاء',
            'Wednesday' => 'الأربعاء',
            'Thursday' => 'الخميس',
            'Friday' => 'الجمعة',
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
