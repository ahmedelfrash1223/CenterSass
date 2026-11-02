<div class="nb-home__body">
    {{-- KPI row --}}
    @include('teacher-panel.pages.dashboard.parts.stats-grid', [
        'stats'   => $stats,
        'isOwner' => $isOwner,
    ])

    <div class="nb-home__grid">
        {{-- Main column: today's sessions --}}
        <div class="nb-home__main">
            @include('teacher-panel.pages.dashboard.parts.today-schedules', [
                'todaySchedules'   => $todaySchedules,
                'currentSchedules' => $currentSchedules,
                'nextSchedule'     => $nextSchedule,
                'isOwner'          => $isOwner,
                'canViewGroups'    => $canViewGroups,
                'showAllSchedules' => $showAllSchedules,
            ])
        </div>

        {{-- Side column --}}
        <div class="nb-home__side">
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

    {{-- Bottom row --}}
    @if($isOwner)
        @livewire('teacher-panel.pages.dashboard.parts.branch-stats', [], key('branch-stats'))
    @endif
</div>
