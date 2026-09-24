<div>
    {{-- Top Action Bar --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <div class="row g-3 align-items-end">

                {{-- Date --}}
                <div class="col-md-3">
                    <label class="form-label small fw-semibold text-body-secondary">
                        <i class="ph-duotone ph-calendar me-1"></i>التاريخ
                    </label>
                    <input type="date" wire:model.live="date" class="form-control">
                </div>

                {{-- Actions --}}
                <div class="col-md-9">
                    <div class="d-flex gap-2 justify-content-end align-items-center flex-wrap">

                        <a href="{{ route('teacher.attendance.close-day.index') }}"
                           wire:navigate
                           class="btn btn-light border">
                            <i class="ph-duotone ph-arrow-right me-1"></i>
                            رجوع
                        </a>

                        @if($isClosed)
                            <button type="button" class="btn btn-secondary" disabled>
                                <i class="ph-duotone ph-lock me-1"></i>
                                المجموعة مقفلة
                            </button>
                        @else
                            <button type="button"
                                    wire:click="confirmCloseGroup"
                                    class="btn btn-warning">
                                <i class="ph-duotone ph-lock-key me-1"></i>
                                تقفيل المجموعة
                            </button>
                        @endif

                    </div>
                </div>

            </div>
        </div>
    </div>

    @livewire('teacher-panel.parts.flash-message', [
        'successKey' => 'attendance-close-success',
        'errorKey' => 'attendance-close-error',
    ])

    @if($isClosed)
        <div class="alert alert-success d-flex align-items-center gap-2 mb-4">
            <i class="ph-duotone ph-check-circle fs-4"></i>
            <div>
                <strong>المجموعة مقفلة</strong>
                <p class="small mb-0">تم تقفيل كل حصص هذه المجموعة في اليوم دا بالفعل.</p>
            </div>
        </div>
    @endif

    @if($confirmingClose)
        <div class="alert alert-warning d-flex align-items-center gap-3 mb-3">
            <i class="ph-duotone ph-warning-circle fs-3"></i>
            <div class="flex-grow-1">
                <strong>تقفيل المجموعة "{{ $this->group->name }}"؟</strong>
                <p class="small mb-0">سيتم تسجيل غياب لكل الطلاب اللي لم يحضروا.</p>
            </div>
            <div class="d-flex gap-2">
                <button type="button" wire:click="closeGroup" wire:loading.attr="disabled"
                        class="btn btn-sm btn-warning">
                    <span wire:loading.remove wire:target="closeGroup">
                        <i class="ph-duotone ph-lock me-1"></i>تأكيد
                    </span>
                    <span wire:loading wire:target="closeGroup">
                        <span class="spinner-border spinner-border-sm"></span>
                    </span>
                </button>
                <button type="button" wire:click="cancelClose" class="btn btn-sm btn-outline-secondary">
                    إلغاء
                </button>
            </div>
        </div>
    @endif

    @if($confirmingReopenId)
        <div class="alert alert-warning d-flex align-items-center gap-3 mb-3">
            <i class="ph-duotone ph-lock-open fs-3"></i>
            <div class="flex-grow-1">
                <strong>فتح الحصة؟</strong>
                <p class="small mb-0">سيتم حذف كل سجلات الغياب التلقائية. هذا الإجراء لا يمكن التراجع عنه.</p>
            </div>
            <div class="d-flex gap-2">
                <button type="button" wire:click="reopenSchedule({{ $confirmingReopenId }})"
                        wire:loading.attr="disabled" class="btn btn-sm btn-warning">
                    <span wire:loading.remove wire:target="reopenSchedule">
                        <i class="ph-duotone ph-lock-open me-1"></i>تأكيد
                    </span>
                    <span wire:loading wire:target="reopenSchedule">
                        <span class="spinner-border spinner-border-sm"></span>
                    </span>
                </button>
                <button type="button" wire:click="cancelClose" class="btn btn-sm btn-outline-secondary">
                    إلغاء
                </button>
            </div>
        </div>
    @endif

    {{-- Stats Cards --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="rounded-3 d-flex align-items-center justify-content-center bg-primary-subtle text-primary"
                         style="width: 48px; height: 48px; flex-shrink: 0;">
                        <i class="ph-duotone ph-student fs-4"></i>
                    </div>
                    <div>
                        <div class="fs-4 fw-bold lh-1">{{ number_format($stats['total_students']) }}</div>
                        <div class="text-body-secondary small mt-1">إجمالي الطلاب</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="rounded-3 d-flex align-items-center justify-content-center bg-success-subtle text-success"
                         style="width: 48px; height: 48px; flex-shrink: 0;">
                        <i class="ph-duotone ph-check-circle fs-4"></i>
                    </div>
                    <div>
                        <div class="fs-4 fw-bold lh-1 text-success">{{ number_format($stats['present']) }}</div>
                        <div class="text-body-secondary small mt-1">الحاضرون</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="rounded-3 d-flex align-items-center justify-content-center bg-warning-subtle text-warning"
                         style="width: 48px; height: 48px; flex-shrink: 0;">
                        <i class="ph-duotone ph-clock fs-4"></i>
                    </div>
                    <div>
                        <div class="fs-4 fw-bold lh-1 text-warning">{{ number_format($stats['late']) }}</div>
                        <div class="text-body-secondary small mt-1">المتأخرون</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="rounded-3 d-flex align-items-center justify-content-center bg-danger-subtle text-danger"
                         style="width: 48px; height: 48px; flex-shrink: 0;">
                        <i class="ph-duotone ph-x-circle fs-4"></i>
                    </div>
                    <div>
                        <div class="fs-4 fw-bold lh-1 text-danger">{{ number_format($stats['absent']) }}</div>
                        <div class="text-body-secondary small mt-1">الغائبون</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Schedules Summary --}}
    @if($stats['schedule_count'] > 0)
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-body d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center gap-2">
                    <i class="ph-duotone ph-calendar-check fs-4 text-primary"></i>
                    <h6 class="mb-0 fw-bold">
                        حصص اليوم
                        <span class="badge bg-primary text-white ms-2">
                            {{ $stats['schedule_count'] }}
                        </span>
                    </h6>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
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
                                    <small class="text-body-secondary">
                                        {{ $row['schedule']->start_time->format('H:i') }} -
                                        {{ $row['schedule']->end_time->format('H:i') }}
                                    </small>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-success text-white">{{ $row['present'] }}</span>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-warning text-dark">{{ $row['late'] }}</span>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-danger text-white">{{ $row['absent'] }}</span>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-secondary-subtle text-secondary-emphasis">{{ $row['not_recorded'] }}</span>
                                </td>
                                <td class="text-center">
                                    @if($row['is_closed'])
                                        <span class="badge bg-success-subtle text-success-emphasis">
                                            <i class="ph-duotone ph-lock me-1"></i>مقفلة
                                        </span>
                                    @else
                                        <span class="badge bg-secondary-subtle text-secondary-emphasis">
                                            مفتوحة
                                        </span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if($row['is_closed'])
                                        <button type="button"
                                                wire:click="confirmReopenSchedule({{ $row['schedule']->id }})"
                                                class="btn btn-sm btn-outline-warning">
                                            <i class="ph-duotone ph-lock-open me-1"></i>فتح
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
        <div class="card-header bg-body d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center gap-2">
                <i class="ph-duotone ph-student fs-4 text-primary"></i>
                <h6 class="mb-0 fw-bold">قائمة الطلاب</h6>
            </div>
            <span class="badge bg-primary text-white">
                {{ $students->count() }}
            </span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
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
                                <span class="text-body-secondary fw-semibold">{{ $index + 1 }}</span>
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="rounded-circle bg-primary-subtle text-primary d-flex align-items-center justify-content-center fw-bold flex-shrink-0"
                                         style="width: 34px; height: 34px; font-size: 14px;">
                                        {{ mb_substr($student->name, 0, 1) }}
                                    </div>
                                    <div>
                                        <span class="fw-semibold d-block small">{{ $student->name }}</span>
                                        <small class="text-body-secondary">{{ $student->code }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                @if($hasPresent)
                                    <span class="badge bg-success-subtle text-success-emphasis">
                                        <i class="ph-duotone ph-check-circle me-1"></i>حاضر
                                    </span>
                                @elseif($hasLate)
                                    <span class="badge bg-warning-subtle text-warning-emphasis">
                                        <i class="ph-duotone ph-clock me-1"></i>متأخر
                                    </span>
                                @elseif($hasAbsent)
                                    <span class="badge bg-danger-subtle text-danger-emphasis">
                                        <i class="ph-duotone ph-x-circle me-1"></i>غايب
                                    </span>
                                @else
                                    <span class="badge bg-secondary-subtle text-secondary-emphasis">
                                        لم يُسجَّل
                                    </span>
                                @endif
                            </td>
                            <td>
                                @if($studentAttendances->isNotEmpty())
                                    <div class="d-flex flex-wrap gap-1">
                                        @foreach($studentAttendances as $att)
                                            <span class="badge bg-{{ $att->status_color }}-subtle text-{{ $att->status_color }}-emphasis small">
                                                {{ $att->schedule?->subject?->subject_label ?? 'حصة' }}
                                                @if($att->check_in_time)
                                                    - {{ $att->check_in_time }}
                                                @endif
                                            </span>
                                        @endforeach
                                    </div>
                                @else
                                    <small class="text-body-secondary">—</small>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center py-5">
                                <i class="ph-duotone ph-users fs-1 d-block mb-2 text-body-secondary"></i>
                                <h6 class="mb-1">لا يوجد طلاب نشطون</h6>
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
