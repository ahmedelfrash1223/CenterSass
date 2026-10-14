<div>
    @if($currentSchedules->count() > 0)

        {{-- Header --}}
        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
            <span class="badge bg-danger text-white fs-6 py-2 px-3">
                <i class="ph-duotone ph-broadcast me-1"></i>
                {{ $currentSchedules->count() }} حصة جارية الآن
            </span>

            @if($currentSchedules->count() > 3)
                <button type="button" wire:click="toggleShowAll" wire:loading.attr="disabled"
                    class="btn btn-sm btn-light border">
                    @if($showAllSchedules)
                        <i class="ph-duotone ph-eye-slash me-1"></i>
                        عرض أقل
                    @else
                        <i class="ph-duotone ph-eye me-1"></i>
                        عرض الكل ({{ $currentSchedules->count() }})
                    @endif
                </button>
            @endif
        </div>

        @foreach($currentSchedules as $index => $schedule)
            @if($index < 3 || $showAllSchedules)
                @php
                    $subjectLabel = \App\Enums\Teacher\SubjectEnum::tryFrom($schedule->subject?->subject)?->label()
                        ?? $schedule->subject?->subject_label
                        ?? 'مادة غير محددة';
                @endphp

                <div class="card border-0 shadow-sm mb-3 schedule-current" wire:key="current-{{ $schedule->id }}">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center gap-3 flex-wrap">
                            <div class="schedule-pulse schedule-pulse-sm">
                                <div class="pulse-ring"></div>
                                <i class="ph-duotone ph-broadcast fs-4"></i>
                            </div>
                            <div class="flex-grow-1">
                                <div class="d-flex align-items-center gap-2 mb-1 flex-wrap">
                                    <span class="badge bg-danger bg-opacity-10 text-danger">
                                        <i class="ph-duotone ph-broadcast me-1"></i>
                                        جارية الآن
                                    </span>
                                    <small class="text-muted">
                                        <i class="ph-duotone ph-clock me-1"></i>
                                        {{ $schedule->start_time->format('H:i') }}
                                        - {{ $schedule->end_time->format('H:i') }}
                                    </small>
                                </div>
                                <h6 class="mb-1 fw-bold">
                                    {{ $subjectLabel }}
                                </h6>
                                <div class="d-flex gap-3 flex-wrap">
                                    <small class="text-muted">
                                        <i class="ph-duotone ph-users-three me-1"></i>
                                        {{ $schedule->group?->name ?? '—' }}
                                    </small>
                                    <small class="text-muted">
                                        <i class="ph-duotone ph-git-branch me-1"></i>
                                        {{ $schedule->group?->branch?->name ?? '—' }}
                                    </small>
                                    <small class="text-muted">
                                        <i class="ph-duotone ph-chalkboard-teacher me-1"></i>
                                        {{ $schedule->teacher?->name ?? '—' }}
                                    </small>
                                </div>
                            </div>
                            @if($canViewGroups)
                                <a href="{{ route('teacher.groups.show', $schedule->group_id) }}" wire:navigate
                                    class="btn btn-sm btn-primary">
                                    <i class="ph-duotone ph-arrow-left me-1"></i>
                                    عرض
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            @endif
        @endforeach

        @if($currentSchedules->count() > 3 && !$showAllSchedules)
            <div class="text-center mb-3">
                <button type="button" wire:click="toggleShowAll" wire:loading.attr="disabled"
                    class="btn btn-light border btn-sm">
                    <i class="ph-duotone ph-arrow-down me-1"></i>
                    عرض باقي {{ $currentSchedules->count() - 3 }} حصة
                </button>
            </div>
        @endif

    @elseif($nextSchedule)

        @php
            $nextSubjectLabel = \App\Enums\Teacher\SubjectEnum::tryFrom($nextSchedule->subject?->subject)?->label()
                ?? $nextSchedule->subject?->subject_label
                ?? 'مادة غير محددة';
        @endphp

        <div class="card border-0 shadow-sm mb-4 schedule-next">
            <div class="card-body p-4">
                <div class="d-flex align-items-center gap-3 flex-wrap">
                    <div class="schedule-icon-wrapper bg-success bg-opacity-10 text-success">
                        <i class="ph-duotone ph-calendar-check fs-1"></i>
                    </div>
                    <div class="flex-grow-1">
                        <div class="d-flex align-items-center gap-2 mb-1 flex-wrap">
                            <span class="badge bg-success text-white">
                                <i class="ph-duotone ph-lightning me-1"></i>
                                الحصة الجاية
                            </span>
                            <small class="text-muted">
                                <i class="ph-duotone ph-clock me-1"></i>
                                {{ $nextSchedule->start_time->format('H:i') }}
                                - {{ $nextSchedule->end_time->format('H:i') }}
                            </small>
                        </div>
                        <h5 class="mb-1 fw-bold">
                            {{ $nextSubjectLabel }}
                            <span class="text-muted">-</span>
                            {{ $nextSchedule->subject?->grade_label ?? '' }}
                        </h5>
                        <div class="d-flex gap-3 flex-wrap">
                            <small class="text-muted">
                                <i class="ph-duotone ph-users-three me-1"></i>
                                {{ $nextSchedule->group?->name ?? '—' }}
                            </small>
                            <small class="text-muted">
                                <i class="ph-duotone ph-git-branch me-1"></i>
                                {{ $nextSchedule->group?->branch?->name ?? '—' }}
                            </small>
                            <small class="text-muted">
                                <i class="ph-duotone ph-chalkboard-teacher me-1"></i>
                                {{ $nextSchedule->teacher?->name ?? '—' }}
                            </small>
                        </div>
                    </div>
                    @if($canViewGroups)
                        <a href="{{ route('teacher.groups.show', $nextSchedule->group_id) }}" wire:navigate
                            class="btn btn-success">
                            <i class="ph-duotone ph-arrow-left me-1"></i>
                            عرض المجموعة
                        </a>
                    @endif
                </div>
            </div>
        </div>

    @elseif($todayCount > 0)

        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body p-4 text-center">
                <div class="schedule-icon-wrapper bg-info bg-opacity-10 text-info mx-auto mb-2">
                    <i class="ph-duotone ph-check-circle fs-1"></i>
                </div>
                <h6 class="mb-1 fw-bold">خلصت حصص النهارده 🎉</h6>
                <p class="text-muted mb-0 small">
                    عندك {{ $todayCount }} حصة النهارده — كلهم خلصوا
                </p>
            </div>
        </div>

    @else

        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body p-4 text-center">
                <div class="schedule-icon-wrapper bg-secondary bg-opacity-10 text-secondary mx-auto mb-2">
                    <i class="ph-duotone ph-coffee fs-1"></i>
                </div>
                <h6 class="mb-1 fw-bold">مفيش حصص النهارده</h6>
                <p class="text-muted mb-0 small">يوم مريح 🌴 — استمتع بيومك</p>
            </div>
        </div>

    @endif
</div>
