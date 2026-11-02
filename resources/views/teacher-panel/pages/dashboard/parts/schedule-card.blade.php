{{-- Now / next summary strip at the top of "حصص النهارده" --}}
@php
    $subjectOf = fn ($schedule) => \App\Enums\Teacher\SubjectEnum::tryFrom($schedule->subject?->subject)?->label()
        ?? $schedule->subject?->subject_label
        ?? 'مادة غير محددة';
@endphp

@if ($currentSchedules->count() > 0)
    <div class="nb-home__now is-live" role="status">
        <span class="nb-icon-tile nb-icon-tile--sm" aria-hidden="true"><i class="ph-duotone ph-broadcast"></i></span>
        <div class="nb-home__now-text">
            <p class="nb-home__now-title">
                <span class="nb-live-dot" aria-hidden="true"></span>
                {{ $currentSchedules->count() }} حصة شغالة دلوقتي
            </p>
            <p class="nb-home__now-meta">
                {{ $currentSchedules->take(2)->map(fn ($s) => $subjectOf($s) . ' – ' . ($s->group?->name ?? '—'))->implode('، ') }}@if ($currentSchedules->count() > 2) و{{ $currentSchedules->count() - 2 }} كمان@endif
            </p>
        </div>
    </div>
@elseif ($nextSchedule)
    <div class="nb-home__now" role="status">
        <span class="nb-icon-tile nb-icon-tile--sm" aria-hidden="true"><i class="ph-duotone ph-lightning"></i></span>
        <div class="nb-home__now-text">
            <p class="nb-home__now-title">
                الحصة الجاية الساعة {{ $nextSchedule->start_time->copy()->locale('ar')->translatedFormat('g:i A') }}
            </p>
            <p class="nb-home__now-meta">
                {{ $subjectOf($nextSchedule) }}@if ($nextSchedule->subject?->grade_label) – {{ $nextSchedule->subject->grade_label }}@endif
                · {{ $nextSchedule->group?->name ?? '—' }}
                · {{ $nextSchedule->group?->branch?->name ?? '—' }}
            </p>
        </div>
        @if ($canViewGroups)
            <a href="{{ route('teacher.groups.show', $nextSchedule->group_id) }}" wire:navigate class="nb-btn nb-btn--ghost nb-btn--sm nb-home__now-action">
                عرض المجموعة
            </a>
        @endif
    </div>
@elseif ($todayCount > 0)
    <div class="nb-home__now is-done" role="status">
        <span class="nb-icon-tile nb-icon-tile--sm" aria-hidden="true"><i class="ph-duotone ph-check-circle"></i></span>
        <div class="nb-home__now-text">
            <p class="nb-home__now-title">خلصت حصص النهارده</p>
            <p class="nb-home__now-meta">عندك {{ $todayCount }} حصة النهارده وكلهم خلصوا</p>
        </div>
    </div>
@endif
