<div>
    @include('teacher-panel.pages.dashboard.parts.schedule-card', [
        'currentSchedules'  => $currentSchedules,
        'nextSchedule'      => $nextSchedule,
        'todayCount'        => $todaySchedules->count(),
        'canViewGroups'     => $canViewGroups,
        'showAllSchedules'  => $showAllSchedules,
    ])

    @include('teacher-panel.pages.dashboard.parts.stats-grid', [
        'stats'   => $stats,
        'isOwner' => $isOwner,
    ])

    <div class="row g-4">
        <div class="col-lg-8">
            @include('teacher-panel.pages.dashboard.parts.today-schedules', [
                'todaySchedules'   => $todaySchedules,
                'currentSchedules' => $currentSchedules,
                'isOwner'          => $isOwner,
                'canViewGroups'    => $canViewGroups,
            ])
        </div>

        <div class="col-lg-4">
            @if($isOwner && $ownerStats)
                @include('teacher-panel.pages.dashboard.parts.owner-panel', [
                    'ownerStats' => $ownerStats,
                    'isOwner'    => $isOwner,
                ])
            @endif

            @if(!$isOwner && $staffInfo)
                @include('teacher-panel.pages.dashboard.parts.staff-panel', [
                    'staffInfo' => $staffInfo,
                    'isOwner'   => $isOwner,
                ])
            @endif

            @include('teacher-panel.pages.dashboard.parts.weekly-schedule', [
                'weeklySchedule' => $weeklySchedule,
            ])
        </div>
    </div>

    @if($isOwner)
        @livewire('teacher-panel.pages.dashboard.parts.branch-stats', [], key('branch-stats'))
    @endif
</div>
