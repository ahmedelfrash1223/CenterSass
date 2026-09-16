<div>
    @if($currentSchedule)
        <div class="card border-0 shadow-sm mb-4 schedule-current">
            <div class="card-body p-4">
                <div class="d-flex align-items-center gap-3 flex-wrap">
                    <div class="schedule-pulse">
                        <div class="pulse-ring"></div>
                        <i class="ph-duotone ph-broadcast fs-1"></i>
                    </div>
                    <div class="flex-grow-1">
                        <div class="d-flex align-items-center gap-2 mb-1 flex-wrap">
                            <span class="badge bg-danger text-white">
                                <i class="ph-duotone ph-broadcast me-1"></i>
                                حصة جارية الآن
                            </span>
                            <small class="text-muted">
                                <i class="ph-duotone ph-clock me-1"></i>
                                {{ $currentSchedule->start_time->format('H:i') }}
                                - {{ $currentSchedule->end_time->format('H:i') }}
                            </small>
                        </div>
                        <h5 class="mb-1 fw-bold">
                            {{ $currentSchedule->subject?->subject_label ?? 'مادة غير محددة' }}
                            <span class="text-muted">-</span>
                            {{ $currentSchedule->subject?->grade_label ?? '' }}
                        </h5>
                        <div class="d-flex gap-3 flex-wrap">
                            <small class="text-muted">
                                <i class="ph-duotone ph-users-three me-1"></i>
                                {{ $currentSchedule->group?->name ?? '—' }}
                            </small>
                            <small class="text-muted">
                                <i class="ph-duotone ph-git-branch me-1"></i>
                                {{ $currentSchedule->group?->branch?->name ?? '—' }}
                            </small>
                            <small class="text-muted">
                                <i class="ph-duotone ph-chalkboard-teacher me-1"></i>
                                {{ $currentSchedule->teacher?->name ?? '—' }}
                            </small>
                        </div>
                    </div>
                    @if($canViewGroups)
                        <a href="{{ route('teacher.groups.show', $currentSchedule->group_id) }}"
                           wire:navigate
                           class="btn btn-primary">
                            <i class="ph-duotone ph-arrow-left me-1"></i>
                            عرض المجموعة
                        </a>
                    @endif
                </div>
            </div>
        </div>
    @elseif($nextSchedule)
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
                            {{ $nextSchedule->subject?->subject_label ?? 'مادة غير محددة' }}
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
                        <a href="{{ route('teacher.groups.show', $nextSchedule->group_id) }}"
                           wire:navigate
                           class="btn btn-success">
                            <i class="ph-duotone ph-arrow-left me-1"></i>
                            عرض المجموعة
                        </a>
                    @endif
                </div>
            </div>
        </div>
    @elseif($todaySchedules->count() > 0)
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body p-4 text-center">
                <div class="schedule-icon-wrapper bg-info bg-opacity-10 text-info mx-auto mb-2">
                    <i class="ph-duotone ph-check-circle fs-1"></i>
                </div>
                <h6 class="mb-1 fw-bold">خلصت حصص النهارده 🎉</h6>
                <p class="text-muted mb-0 small">عندك {{ $todaySchedules->count() }} حصة النهارده — كلهم خلصوا</p>
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

    <div class="row g-3 mb-4">
        <div class="col-md-6 col-lg-3">
            <div class="card border-0 shadow-sm stat-card-hover h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="stat-icon-box bg-primary bg-opacity-10 text-primary">
                            <i class="ph-duotone ph-student"></i>
                        </div>
                        <i class="ph-duotone ph-trend-up text-success"></i>
                    </div>
                    <h3 class="mb-1 fw-bold">{{ number_format($stats['students_count']) }}</h3>
                    <p class="text-muted mb-0 small">
                        <i class="ph-duotone ph-student me-1"></i>
                        إجمالي الطلاب
                    </p>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-lg-3">
            <div class="card border-0 shadow-sm stat-card-hover h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="stat-icon-box bg-success bg-opacity-10 text-success">
                            <i class="ph-duotone ph-users-three"></i>
                        </div>
                        <i class="ph-duotone ph-trend-up text-success"></i>
                    </div>
                    <h3 class="mb-1 fw-bold">{{ number_format($stats['groups_count']) }}</h3>
                    <p class="text-muted mb-0 small">
                        <i class="ph-duotone ph-users-three me-1"></i>
                        إجمالي المجموعات
                    </p>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-lg-3">
            <div class="card border-0 shadow-sm stat-card-hover h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="stat-icon-box bg-warning bg-opacity-10 text-warning">
                            <i class="ph-duotone ph-git-branch"></i>
                        </div>
                        <i class="ph-duotone ph-check-circle text-success"></i>
                    </div>
                    <h3 class="mb-1 fw-bold">{{ number_format($stats['branches_count']) }}</h3>
                    <p class="text-muted mb-0 small">
                        <i class="ph-duotone ph-git-branch me-1"></i>
                        {{ $isOwner ? 'إجمالي الفروع' : 'الفروع المتاحة لك' }}
                    </p>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-lg-3">
            <div class="card border-0 shadow-sm stat-card-hover h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="stat-icon-box bg-info bg-opacity-10 text-info">
                            <i class="ph-duotone ph-notebook"></i>
                        </div>
                        <i class="ph-duotone ph-check-circle text-success"></i>
                    </div>
                    <h3 class="mb-1 fw-bold">{{ number_format($stats['subjects_count']) }}</h3>
                    <p class="text-muted mb-0 small">
                        <i class="ph-duotone ph-notebook me-1"></i>
                        إجمالي المواد
                    </p>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-8">
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
                            @foreach($todaySchedules as $index => $schedule)
                                @php
                                    $isNow = $currentSchedule && $currentSchedule->id === $schedule->id;
                                    $isPast = $schedule->end_time->format('H:i') < now()->format('H:i');
                                    $isUpcoming = !$isNow && !$isPast;
                                @endphp
                                <div
                                    class="schedule-item {{ $isNow ? 'is-now' : ($isPast ? 'is-past' : 'is-upcoming') }}"
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
                                            @if($isOwner || $schedule->teacher_id !== Auth::guard('teacher')->id())
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
        </div>

        <div class="col-lg-4">
            @if($isOwner && $ownerStats)
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body">
                        <div class="d-flex align-items-center gap-3 mb-3">
                            <div class="stat-icon-box bg-primary bg-opacity-10 text-primary">
                                <i class="ph-duotone ph-users-four"></i>
                            </div>
                            <div class="flex-grow-1">
                                <h3 class="mb-0 fw-bold">{{ $ownerStats['staff_count'] }}</h3>
                                <small class="text-muted">أعضاء الفريق</small>
                            </div>
                        </div>
                        <div class="d-flex justify-content-between pt-3 border-top">
                            <small class="text-muted">
                                <i class="ph-duotone ph-check-circle text-success me-1"></i>
                                نشط: <strong>{{ $ownerStats['active_staff_count'] }}</strong>
                            </small>
                            <a href="{{ route('teacher.staff.index') }}" wire:navigate
                               class="text-primary small text-decoration-none">
                                عرض الكل <i class="ph-duotone ph-arrow-left"></i>
                            </a>
                        </div>
                    </div>
                </div>

                @if($ownerStats['subscription'])
                    @php $sub = $ownerStats['subscription']; @endphp
                    <div class="card border-0 shadow-sm mb-4 subscription-card">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <div class="d-flex align-items-center gap-2">
                                    <i class="ph-duotone ph-crown text-warning fs-4"></i>
                                    <h6 class="mb-0 fw-bold">اشتراكك</h6>
                                </div>
                                <span class="badge bg-success bg-opacity-10 text-success">
                                    <i class="ph-duotone ph-check-circle me-1"></i>نشط
                                </span>
                            </div>
                            <div class="mb-2">
                                <small class="text-muted d-block">الباقة</small>
                                <strong>{{ $sub->package?->name ?? 'غير محددة' }}</strong>
                            </div>
                            @if($sub->end_date)
                                <div class="mb-3">
                                    <small class="text-muted d-block">تاريخ الانتهاء</small>
                                    <strong>{{ $sub->end_date->format('Y-m-d') }}</strong>
                                    <small class="text-muted d-block mt-1">
                                        ({{ (int) now()->diffInDays($sub->end_date, false) }} يوم متبقي)
                                    </small>
                                </div>
                            @endif
                            <a href="{{ route('teacher.profile.subscription') }}"
                               wire:navigate
                               class="btn btn-sm btn-primary w-100">
                                <i class="ph-duotone ph-crown me-1"></i>
                                إدارة الاشتراك
                            </a>
                        </div>
                    </div>
                @endif
            @endif

            @if(!$isOwner && $staffInfo)
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-transparent border-bottom">
                        <h6 class="mb-0 fw-bold">
                            <i class="ph-duotone ph-git-branch me-1"></i>
                            فروعي
                            <span class="badge bg-primary bg-opacity-10 text-primary ms-2">
                                {{ $staffInfo['my_branches']->count() }}
                            </span>
                        </h6>
                    </div>
                    <div class="card-body p-0">
                        @if($staffInfo['my_branches']->count() > 0)
                            <div class="list-group list-group-flush">
                                @foreach($staffInfo['my_branches'] as $branch)
                                    <div class="list-group-item d-flex align-items-center gap-2">
                                        <div class="stat-icon-box stat-icon-sm bg-primary bg-opacity-10 text-primary">
                                            <i class="ph-duotone ph-git-branch"></i>
                                        </div>
                                        <div class="flex-grow-1">
                                            <strong class="d-block small">{{ $branch->name }}</strong>
                                            @if($branch->phone)
                                                <small class="text-muted">
                                                    <i class="ph-duotone ph-phone me-1"></i>{{ $branch->phone }}
                                                </small>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-4">
                                <i class="ph-duotone ph-git-branch fs-1 text-muted d-block mb-2"></i>
                                <small class="text-muted">لم يتم إضافتك لأي فرع بعد</small>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body text-center">
                        <div class="stat-icon-box bg-success bg-opacity-10 text-success mx-auto mb-2">
                            <i class="ph-duotone ph-calendar-check fs-3"></i>
                        </div>
                        <h3 class="mb-1 fw-bold">{{ $staffInfo['my_schedules_count'] }}</h3>
                        <small class="text-muted">حصصي النهارده</small>
                    </div>
                </div>
            @endif

            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent border-bottom">
                    <h6 class="mb-0 fw-bold">
                        <i class="ph-duotone ph-chart-bar me-1"></i>
                        حصص الأسبوع
                    </h6>
                </div>
                <div class="card-body">
                    @php $maxWeekly = max(array_values($weeklySchedule)) ?: 1; @endphp
                    @foreach($weeklySchedule as $day => $count)
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <small class="text-muted" style="min-width: 50px;">{{ $day }}</small>
                            <div class="progress flex-grow-1" style="height: 8px;">
                                <div class="progress-bar bg-primary"
                                     style="width: {{ ($count / $maxWeekly) * 100 }}%"></div>
                            </div>
                            <small class="text-muted fw-bold"
                                   style="min-width: 25px; text-align: end;">{{ $count }}</small>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    @if($isOwner && $ownerStats && $ownerStats['branch_stats']->count() > 0)
        <div class="card border-0 shadow-sm mt-4">
            <div class="card-header bg-transparent border-bottom d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-bold">
                    <i class="ph-duotone ph-buildings me-1"></i>
                    إحصائيات الفروع
                </h6>
                <a href="{{ route('teacher.branches.index') }}" wire:navigate class="btn btn-sm btn-light border">
                    عرض الفروع <i class="ph-duotone ph-arrow-left ms-1"></i>
                </a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                        <tr>
                            <th class="ps-4">الفرع</th>
                            <th class="text-center">الطلاب</th>
                            <th class="text-center">المجموعات</th>
                            <th class="text-center">حصص النهارده</th>
                            <th class="text-center">الحالة</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($ownerStats['branch_stats'] as $branch)
                            <tr>
                                <td class="ps-4">
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="stat-icon-box stat-icon-sm bg-primary bg-opacity-10 text-primary">
                                            <i class="ph-duotone ph-git-branch"></i>
                                        </div>
                                        <div>
                                            <strong class="d-block small">{{ $branch->name }}</strong>
                                            @if($branch->phone)
                                                <small class="text-muted">
                                                    <i class="ph-duotone ph-phone me-1"></i>{{ $branch->phone }}
                                                </small>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-primary bg-opacity-10 text-primary">
                                        {{ $branch->students_count }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-success bg-opacity-10 text-success">
                                        {{ $branch->groups_count }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    @if($branch->today_schedules_count > 0)
                                        <span class="badge bg-warning bg-opacity-10 text-warning">
                                            <i class="ph-duotone ph-broadcast me-1"></i>
                                            {{ $branch->today_schedules_count }}
                                        </span>
                                    @else
                                        <span class="text-muted small">—</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if($branch->is_active)
                                        <span class="badge bg-success bg-opacity-10 text-success">نشط</span>
                                    @else
                                        <span class="badge bg-danger bg-opacity-10 text-danger">غير نشط</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif
</div>
