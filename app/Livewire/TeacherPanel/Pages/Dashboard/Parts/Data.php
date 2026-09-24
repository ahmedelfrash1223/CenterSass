<?php

namespace App\Livewire\TeacherPanel\Pages\Dashboard\Parts;

use App\Enums\Teacher\TeacherPermissionEnum;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use App\Livewire\TeacherPanel\Concerns\Dashboard\BuildsDashboardStats;
use App\Livewire\TeacherPanel\Concerns\Dashboard\BuildsScheduleStats;
use App\Livewire\TeacherPanel\Concerns\Dashboard\BuildsWeeklySchedule;
use App\Livewire\TeacherPanel\Concerns\Dashboard\BuildsOwnerStats;
use App\Livewire\TeacherPanel\Concerns\Dashboard\BuildsStaffStats;

class Data extends Component
{
    use BuildsDashboardStats;
    use BuildsScheduleStats;
    use BuildsWeeklySchedule;
    use BuildsOwnerStats;
    use BuildsStaffStats;

    public bool $showAllSchedules = false;

    public function toggleShowAll(): void
    {
        $this->showAllSchedules = !$this->showAllSchedules;
    }

    public function render()
    {
        $user = Auth::guard('teacher')->user();
        $ownerId = $user->getOwnerId();
        $isOwner = $user->isOwner();
        $accessibleBranchIds = $user->getAccessibleBranchIds();

        $stats = $this->buildStats($ownerId, $isOwner, $accessibleBranchIds);

        $schedules = $this->buildSchedulesQuery($user, $ownerId, $isOwner, $accessibleBranchIds)->get();
        $scheduleStats = $this->buildTodaySchedules($schedules);

        $ownerStats = $isOwner ? $this->buildOwnerStats($user, $ownerId) : null;
        $staffInfo = !$isOwner ? $this->buildStaffInfo($user, $ownerId) : null;

        $weeklySchedule = $this->buildWeeklySchedule($user, $ownerId, $isOwner, $accessibleBranchIds);

        $canViewGroups = $user->hasPermission(TeacherPermissionEnum::GROUP_VIEW);

        return view('teacher-panel.pages.dashboard.parts.data', [
            'isOwner'           => $isOwner,
            'stats'             => $stats,
            'todaySchedules'    => $scheduleStats['all'],
            'currentSchedules'  => $scheduleStats['current'],
            'nextSchedule'      => $scheduleStats['next'],
            'upcomingSchedules' => $scheduleStats['upcoming'],
            'ownerStats'        => $ownerStats,
            'staffInfo'         => $staffInfo,
            'weeklySchedule'    => $weeklySchedule,
            'canViewGroups'     => $canViewGroups,
        ]);
    }
}
