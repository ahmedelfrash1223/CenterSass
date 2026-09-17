<div>
    {{-- Top Action Bar --}}
    <div class="close-day-show-header mb-4">
        <div class="row g-3 align-items-end">

            {{-- Date --}}
            <div class="col-md-3">
                <label class="filter-label">
                    <i class="ph-duotone ph-calendar me-1"></i>
                    التاريخ
                </label>
                <input type="date"
                       wire:model.live="date"
                       class="form-control form-control-lg filter-input">
            </div>

            {{-- Actions --}}
            <div class="col-md-9">
                <div class="d-flex gap-2 justify-content-end align-items-center flex-wrap">

                    <a href="{{ route('teacher.attendance.close-day.index') }}"
                       wire:navigate
                       class="btn btn-light border btn-lg-custom">
                        <i class="ph-duotone ph-arrow-right me-1"></i>
                        رجوع
                    </a>

                    <button type="button"
                            wire:click="confirmCloseGroup"
                            class="btn btn-close-day-small">
                        <span class="btn-close-day-icon">
                            <i class="ph-duotone ph-lock"></i>
                        </span>
                        <span class="btn-close-day-text">
                            <span class="btn-close-day-title">تقفيل المجموعة</span>
                            <span class="btn-close-day-subtitle">تسجيل غياب للطلاب الغائبين</span>
                        </span>
                    </button>

                </div>
            </div>

        </div>
    </div>

    @livewire('teacher-panel.parts.flash-message', [
        'successKey' => 'attendance-close-success',
        'errorKey' => 'attendance-close-error',
    ])

    {{-- Stats Cards --}}
    <div class="row g-3 mb-4">
        <div class="col-md-6 col-lg-3">
            <div class="card border-0 shadow-sm stat-card-hover h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <div class="stat-icon-box bg-primary bg-opacity-10 text-primary">
                            <i class="ph-duotone ph-student"></i>
                        </div>
                    </div>
                    <h3 class="mb-1 fw-bold">{{ number_format($stats['total_students']) }}</h3>
                    <p class="text-muted mb-0 small">إجمالي الطلاب</p>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-lg-3">
            <div class="card border-0 shadow-sm stat-card-hover h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <div class="stat-icon-box bg-success bg-opacity-10 text-success">
                            <i class="ph-duotone ph-check-circle"></i>
                        </div>
                    </div>
                    <h3 class="mb-1 fw-bold text-success">{{ number_format($stats['present']) }}</h3>
                    <p class="text-muted mb-0 small">الحاضرون</p>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-lg-3">
            <div class="card border-0 shadow-sm stat-card-hover h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <div class="stat-icon-box bg-warning bg-opacity-10 text-warning">
                            <i class="ph-duotone ph-clock"></i>
                        </div>
                    </div>
                    <h3 class="mb-1 fw-bold text-warning">{{ number_format($stats['late']) }}</h3>
                    <p class="text-muted mb-0 small">المتأخرون</p>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-lg-3">
            <div class="card border-0 shadow-sm stat-card-hover h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <div class="stat-icon-box bg-danger bg-opacity-10 text-danger">
                            <i class="ph-duotone ph-x-circle"></i>
                        </div>
                    </div>
                    <h3 class="mb-1 fw-bold text-danger">{{ number_format($stats['absent']) }}</h3>
                    <p class="text-muted mb-0 small">الغائبون</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Schedules Summary --}}
    @if($stats['schedule_count'] > 0)
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header close-day-header d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center gap-2">
                    <i class="ph-duotone ph-calendar-check fs-4 text-primary"></i>
                    <h6 class="mb-0 fw-bold">
                        حصص اليوم
                        <span class="badge bg-primary bg-opacity-10 text-primary ms-2">
                            {{ $stats['schedule_count'] }}
                        </span>
                    </h6>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                        <tr>
                            <th class="ps-4">الحصة</th>
                            <th>الوقت</th>
                            <th class="text-center">حضور</th>
                            <th class="text-center">تأخير</th>
                            <th class="text-center">غياب</th>
                            <th class="text-center">لم يُسجَّل</th>
                            <th class="text-center">الحالة</th>
                            <th class="text-center">إجراءات</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($stats['schedules'] as $row)
                            <tr wire:key="schedule-{{ $row['schedule']->id }}">
                                <td class="ps-4">
                                    <span class="fw-semibold">
                                        {{ $row['schedule']->subject?->subject_label ?? 'حصة' }}
                                    </span>
                                </td>
                                <td>
                                    <small class="text-muted">
                                        {{ $row['schedule']->start_time->format('H:i') }} -
                                        {{ $row['schedule']->end_time->format('H:i') }}
                                    </small>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-success bg-opacity-10 text-success">
                                        {{ $row['present'] }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-warning bg-opacity-10 text-warning">
                                        {{ $row['late'] }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-danger bg-opacity-10 text-danger">
                                        {{ $row['absent'] }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-secondary bg-opacity-10 text-secondary">
                                        {{ $row['not_recorded'] }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    @if($row['is_closed'])
                                        <span class="badge bg-success bg-opacity-10 text-success">
                                            <i class="ph-duotone ph-lock me-1"></i>مقفلة
                                        </span>
                                    @else
                                        <span class="badge bg-secondary bg-opacity-10 text-secondary">
                                            مفتوحة
                                        </span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if($row['is_closed'])
                                        <button type="button"
                                                wire:click="confirmReopenSchedule({{ $row['schedule']->id }})"
                                                class="btn btn-sm btn-outline-warning">
                                            <i class="ph-duotone ph-lock-open"></i>
                                            فتح
                                        </button>
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

    {{-- Students List --}}
    <div class="card border-0 shadow-sm">
        <div class="card-header close-day-header d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center gap-2">
                <i class="ph-duotone ph-student fs-4 text-primary"></i>
                <h6 class="mb-0 fw-bold">
                    قائمة الطلاب
                </h6>
            </div>
            <span class="badge bg-primary bg-opacity-10 text-primary">
                {{ $students->count() }}
            </span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                    <tr>
                        <th class="ps-4" style="width: 60px;">#</th>
                        <th>الطالب</th>
                        <th>الحالة اليوم</th>
                        <th>التفاصيل</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($students as $index => $student)
                        @php
                            $studentAttendances = $attendances->get($student->id, collect());
                            $hasPresent = $studentAttendances->where('status', 'present')->isNotEmpty();
                            $hasLate = $studentAttendances->where('status', 'late')->isNotEmpty();
                            $hasAbsent = $studentAttendances->where('status', 'absent')->isNotEmpty();
                        @endphp
                        <tr wire:key="student-{{ $student->id }}">
                            <td class="ps-4">
                                <span class="text-muted fw-semibold">{{ $index + 1 }}</span>
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="student-avatar">{{ mb_substr($student->name, 0, 1) }}</div>
                                    <div>
                                        <span class="fw-semibold d-block small">{{ $student->name }}</span>
                                        <small class="text-muted">{{ $student->code }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                @if($hasPresent)
                                    <span class="badge bg-success bg-opacity-10 text-success">
                                        <i class="ph-duotone ph-check-circle me-1"></i>حاضر
                                    </span>
                                @elseif($hasLate)
                                    <span class="badge bg-warning bg-opacity-10 text-warning">
                                        <i class="ph-duotone ph-clock me-1"></i>متأخر
                                    </span>
                                @elseif($hasAbsent)
                                    <span class="badge bg-danger bg-opacity-10 text-danger">
                                        <i class="ph-duotone ph-x-circle me-1"></i>غايب
                                    </span>
                                @else
                                    <span class="badge bg-secondary bg-opacity-10 text-secondary">
                                        لم يُسجَّل
                                    </span>
                                @endif
                            </td>
                            <td>
                                @if($studentAttendances->isNotEmpty())
                                    <div class="d-flex flex-wrap gap-1">
                                        @foreach($studentAttendances as $att)
                                            <span
                                                class="badge bg-{{ $att->status_color }} bg-opacity-10 text-{{ $att->status_color }} small">
                                                {{ $att->schedule?->subject?->subject_label ?? 'حصة' }}
                                                @if($att->check_in_time)
                                                    - {{ $att->check_in_time }}
                                                @endif
                                            </span>
                                        @endforeach
                                    </div>
                                @else
                                    <small class="text-muted">—</small>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center py-5">
                                <i class="ph-duotone ph-users fs-1 d-block mb-2 text-muted"></i>
                                <h6 class="mb-1">لا يوجد طلاب نشطون</h6>
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Close Group Confirmation Modal --}}
    @if($confirmingClose)
        <div class="close-day-modal-backdrop" wire:click="cancelClose"></div>
        <div class="close-day-modal">
            <div class="close-day-modal-content">
                <div class="close-day-modal-icon">
                    <i class="ph-duotone ph-warning-circle"></i>
                </div>

                <h4 class="close-day-modal-title">تقفيل المجموعة</h4>

                <p class="close-day-modal-text">
                    هل أنت متأكد من تقفيل <strong>{{ $this->group->name }}</strong>؟
                    سيتم تسجيل غياب لكل الطالب اللي لم يحضروا.
                </p>

                <div class="close-day-modal-warning">
                    <i class="ph-duotone ph-info"></i>
                    <span>يمكنك فتح الحصة بعد التقفيل من جدول "حصص اليوم".</span>
                </div>

                <div class="close-day-modal-actions">
                    <button type="button"
                            wire:click="cancelClose"
                            class="btn btn-modal-cancel">
                        <i class="ph-duotone ph-x me-1"></i>
                        إلغاء
                    </button>
                    <button type="button"
                            wire:click="closeGroup"
                            wire:loading.attr="disabled"
                            wire:target="closeGroup"
                            class="btn btn-modal-confirm">
                        <span wire:loading.remove wire:target="closeGroup">
                            <i class="ph-duotone ph-lock me-1"></i>
                            نعم، قفّل
                        </span>
                        <span wire:loading wire:target="closeGroup">
                            <span class="spinner-border spinner-border-sm me-1"></span>
                            جاري التقفيل...
                        </span>
                    </button>
                </div>
            </div>
        </div>
    @endif

    {{-- Reopen Schedule Confirmation Modal --}}
    @if($confirmingReopenId)
        <div class="close-day-modal-backdrop" wire:click="cancelClose"></div>
        <div class="close-day-modal">
            <div class="close-day-modal-content">
                <div class="close-day-modal-icon modal-icon-warning">
                    <i class="ph-duotone ph-lock-open"></i>
                </div>

                <h4 class="close-day-modal-title">فتح الحصة</h4>

                <p class="close-day-modal-text">
                    هل أنت متأكد من فتح الحصة؟
                    <br>
                    سيتم <strong>حذف كل سجلات الغياب التلقائية</strong> المرتبطة بها.
                </p>

                <div class="close-day-modal-warning modal-warning-danger">
                    <i class="ph-duotone ph-warning"></i>
                    <span>هذا الإجراء لا يمكن التراجع عنه.</span>
                </div>

                <div class="close-day-modal-actions">
                    <button type="button"
                            wire:click="cancelClose"
                            class="btn btn-modal-cancel">
                        <i class="ph-duotone ph-x me-1"></i>
                        إلغاء
                    </button>
                    <button type="button"
                            wire:click="reopenSchedule({{ $confirmingReopenId }})"
                            wire:loading.attr="disabled"
                            wire:target="reopenSchedule"
                            class="btn btn-modal-confirm btn-modal-warning">
                        <span wire:loading.remove wire:target="reopenSchedule">
                            <i class="ph-duotone ph-lock-open me-1"></i>
                            نعم، افتح
                        </span>
                        <span wire:loading wire:target="reopenSchedule">
                            <span class="spinner-border spinner-border-sm me-1"></span>
                            جاري الفتح...
                        </span>
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
