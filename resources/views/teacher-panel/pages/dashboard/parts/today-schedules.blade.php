@php
    $homeUser = auth('teacher')->user();
    $canViewTimetable = $homeUser?->hasPermission(\App\Enums\Teacher\TeacherPermissionEnum::TIMETABLE_VIEW);

    // "5:00 – 7:00 م" (one period mark when both ends share it)
    $timeRange = function ($start, $end) {
        $s = $start->copy()->locale('ar');
        $e = $end->copy()->locale('ar');
        return $s->format('A') === $e->format('A')
            ? $s->format('g:i') . ' – ' . $e->translatedFormat('g:i A')
            : $s->translatedFormat('g:i A') . ' – ' . $e->translatedFormat('g:i A');
    };

    $visibleLimit = 6;
    $totalToday = $todaySchedules->count();
    $nowTime = now()->format('H:i');
@endphp

<section class="nb-card nb-home__sessions" aria-labelledby="nb-sessions-title">
    <header class="nb-card__head">
        <div class="nb-card__heading">
            <span class="nb-icon-tile nb-icon-tile--sm" aria-hidden="true">
                <i class="ph-duotone ph-calendar-check"></i>
            </span>
            <div>
                <h3 class="nb-card__title" id="nb-sessions-title">حصص النهارده</h3>
                <p class="nb-card__subtitle">{{ $totalToday }} حصة</p>
            </div>
        </div>

        @if ($canViewTimetable)
            <a href="{{ route('teacher.timetable.index') }}" wire:navigate class="nb-card__link">
                عرض الكل
                <i class="ph-duotone ph-caret-left" aria-hidden="true"></i>
            </a>
        @endif
    </header>

    {{-- Now / next summary --}}
    @include('teacher-panel.pages.dashboard.parts.schedule-card', [
        'currentSchedules' => $currentSchedules,
        'nextSchedule'     => $nextSchedule,
        'todayCount'       => $totalToday,
        'canViewGroups'    => $canViewGroups,
        'showAllSchedules' => $showAllSchedules,
    ])

    @if ($totalToday > 0)
        <ol class="nb-timeline" aria-label="مواعيد حصص النهارده">
            @foreach ($todaySchedules as $index => $schedule)
                @continue($index >= $visibleLimit && ! $showAllSchedules)
                @php
                    $isNow = $currentSchedules->contains('id', $schedule->id);
                    $isPast = ! $isNow && $schedule->end_time->format('H:i') < $nowTime;
                    $state = $isNow ? 'now' : ($isPast ? 'past' : 'upcoming');

                    $subjectLabel = \App\Enums\Teacher\SubjectEnum::tryFrom($schedule->subject?->subject)?->label()
                        ?? $schedule->subject?->subject_label
                        ?? 'مادة';
                    $gradeLabel = $schedule->subject?->grade_label;
                    $showTeacher = $isOwner || $schedule->teacher_id !== auth('teacher')->id();
                    $rowTag = $canViewGroups ? 'a' : 'div';
                @endphp

                <li
                    class="nb-timeline__item is-{{ $state }}"
                    data-nb-session
                    data-start="{{ $schedule->start_time->format('H:i') }}"
                    data-end="{{ $schedule->end_time->format('H:i') }}"
                    wire:key="schedule-{{ $schedule->id }}"
                >
                    <span class="nb-timeline__node" aria-hidden="true"></span>

                    <{{ $rowTag }}
                        class="nb-timeline__row"
                        @if ($canViewGroups) href="{{ route('teacher.groups.show', $schedule->group_id) }}" wire:navigate @endif
                    >
                        <span class="nb-timeline__time">{{ $timeRange($schedule->start_time, $schedule->end_time) }}</span>

                        <span class="nb-timeline__main">
                            <span class="nb-timeline__title">
                                {{ $subjectLabel }}@if ($gradeLabel) – {{ $gradeLabel }}@endif
                            </span>
                            <span class="nb-timeline__meta">
                                <span><i class="ph-duotone ph-users-three" aria-hidden="true"></i>{{ $schedule->group?->name ?? '—' }}</span>
                                <span><i class="ph-duotone ph-map-pin" aria-hidden="true"></i>{{ $schedule->group?->branch?->name ?? '—' }}</span>
                                @if ($showTeacher)
                                    <span><i class="ph-duotone ph-chalkboard-teacher" aria-hidden="true"></i>{{ $schedule->teacher?->name ?? '—' }}</span>
                                @endif
                            </span>
                        </span>

                        <span class="nb-timeline__end">
                            <span class="nb-pill {{ $isNow ? 'nb-pill--success' : ($isPast ? 'nb-pill--neutral' : 'nb-pill--info') }}" data-nb-session-pill>
                                <span class="nb-live-dot" data-nb-session-dot @unless ($isNow) hidden @endunless aria-hidden="true"></span>
                                <span data-nb-session-label>{{ $isNow ? 'شغالة دلوقتي' : ($isPast ? 'خلصت' : 'جاية') }}</span>
                            </span>
                            @if ($canViewGroups)
                                <i class="ph-duotone ph-caret-left nb-timeline__chevron" aria-hidden="true"></i>
                            @endif
                        </span>
                    </{{ $rowTag }}>
                </li>
            @endforeach
        </ol>

        @if ($totalToday > $visibleLimit)
            <div class="nb-home__more">
                <button type="button" wire:click="toggleShowAll" wire:loading.attr="disabled" class="nb-btn nb-btn--ghost nb-btn--sm">
                    @if ($showAllSchedules)
                        <i class="ph-duotone ph-caret-up" aria-hidden="true"></i>
                        عرض أقل
                    @else
                        <i class="ph-duotone ph-caret-down" aria-hidden="true"></i>
                        عرض باقي {{ $totalToday - $visibleLimit }} حصة
                    @endif
                </button>
            </div>
        @endif
    @else
        <div class="nb-empty">
            <span class="nb-empty__icon" aria-hidden="true"><i class="ph-duotone ph-calendar-x"></i></span>
            <p class="nb-empty__title">مفيش حصص النهارده</p>
            <p class="nb-empty__text">استمتع بيومك</p>
            @if ($canViewTimetable)
                <a href="{{ route('teacher.timetable.index') }}" wire:navigate class="nb-card__link">
                    جدول الأسبوع
                    <i class="ph-duotone ph-caret-left" aria-hidden="true"></i>
                </a>
            @endif
        </div>
    @endif
</section>
