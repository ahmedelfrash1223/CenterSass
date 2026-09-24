<div class="card border-0 shadow-sm h-100">
    <div class="card-header bg-transparent border-bottom d-flex justify-content-between align-items-center">
        <div>
            <h6 class="mb-1 fw-bold">
                <i class="ph-duotone ph-calendar-check me-1"></i>
                جدول مواعيد النهارده
            </h6>
            <small class="text-muted">
                <i class="ph-duotone ph-calendar-blank me-1"></i>
                {{ now()->locale('ar')->translatedFormat('l، d F Y') }}
            </small>
        </div>
        <span class="badge bg-primary bg-opacity-10 text-primary">
            {{ $todaySchedules->count() }} حصة
        </span>
    </div>
    <div class="card-body p-0">
        @if($todaySchedules->count() > 0)
            <div class="schedule-list">
                @foreach($todaySchedules as $schedule)
                    @php
                        $isNow = $currentSchedules->contains('id', $schedule->id);
                        $isPast = $schedule->end_time->format('H:i') < now()->format('H:i');
                        $isUpcoming = !$isNow && !$isPast;
                    @endphp
                    <div class="schedule-item {{ $isNow ? 'is-now' : ($isPast ? 'is-past' : 'is-upcoming') }}"
                         wire:key="schedule-{{ $schedule->id }}">
                        <div class="schedule-time">
                            <div class="fw-bold">{{ $schedule->start_time->format('H:i') }}</div>
                            <small class="text-muted">{{ $schedule->end_time->format('H:i') }}</small>
                        </div>

                        <div class="schedule-content">
                            <div class="d-flex align-items-center gap-2 mb-1 flex-wrap">
                                <h6 class="mb-0 fw-semibold">
                                    {{ $schedule->subject?->subject_label ?? 'مادة' }}
                                </h6>
                                @if($isNow)
                                    <span class="badge bg-danger bg-opacity-10 text-danger">
                                        <i class="ph-duotone ph-broadcast me-1"></i>الآن
                                    </span>
                                @elseif($isPast)
                                    <span class="badge bg-secondary bg-opacity-10 text-secondary">
                                        <i class="ph-duotone ph-check me-1"></i>خلصت
                                    </span>
                                @endif
                            </div>
                            <div class="d-flex gap-3 flex-wrap">
                                <small class="text-muted">
                                    <i class="ph-duotone ph-users-three me-1"></i>
                                    {{ $schedule->group?->name }}
                                </small>
                                <small class="text-muted">
                                    <i class="ph-duotone ph-git-branch me-1"></i>
                                    {{ $schedule->group?->branch?->name }}
                                </small>
                                @if($isOwner || $schedule->teacher_id !== auth('teacher')->id())
                                    <small class="text-muted">
                                        <i class="ph-duotone ph-chalkboard-teacher me-1"></i>
                                        {{ $schedule->teacher?->name }}
                                    </small>
                                @endif
                            </div>
                        </div>

                        <div class="schedule-action">
                            @if($canViewGroups)
                                <a href="{{ route('teacher.groups.show', $schedule->group_id) }}"
                                   wire:navigate
                                   class="btn btn-sm btn-light border">
                                    <i class="ph-duotone ph-arrow-left"></i>
                                </a>
                            @else
                                <span class="btn btn-sm btn-light border disabled"
                                      title="ليس لديك صلاحية">
                                    <i class="ph-duotone ph-arrow-left"></i>
                                </span>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-5">
                <i class="ph-duotone ph-calendar-x fs-1 d-block text-muted mb-2"></i>
                <h6 class="text-muted mb-1">لا توجد حصص اليوم</h6>
                <small class="text-muted">استمتع بيومك 🌴</small>
            </div>
        @endif
    </div>
</div>
