<div>
    {{-- Filters Bar --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <div class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="form-label small fw-semibold text-body-secondary">
                        <i class="ph-duotone ph-calendar me-1"></i>التاريخ
                    </label>
                    <input type="date" wire:model.live="date" class="form-control">
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-semibold text-body-secondary">
                        <i class="ph-duotone ph-git-branch me-1"></i>الفرع
                    </label>
                    <select wire:model.live="branchFilter" class="form-select">
                        <option value="">كل الفروع</option>
                        @foreach($branches as $branch)
                            <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    @if($confirmingCloseAll)
                        <div class="alert alert-warning mb-0 d-flex align-items-center gap-2">
                            <i class="ph-duotone ph-warning-circle fs-4"></i>
                            <div class="flex-grow-1">
                                <strong>تقفيل كل مجموعات اليوم؟</strong>
                                <p class="small mb-0">سيتم تسجيل غياب لكل الطلاب اللي لم يحضروا في الحصص المكتملة وغير
                                    الملغية.</p>
                            </div>
                            <div class="d-flex gap-2">
                                <button type="button" wire:click="closeAllGroups" wire:loading.attr="disabled"
                                    class="btn btn-sm btn-warning">
                                    <span wire:loading.remove wire:target="closeAllGroups">تأكيد</span>
                                    <span wire:loading wire:target="closeAllGroups"><span
                                            class="spinner-border spinner-border-sm"></span></span>
                                </button>
                                <button type="button" wire:click="cancelClose"
                                    class="btn btn-sm btn-outline-secondary">إلغاء</button>
                            </div>
                        </div>
                    @else
                        <button type="button" wire:click="confirmCloseAll" class="btn btn-warning w-100"
                            @if($closableGroups->isEmpty()) disabled @endif>
                            <i class="ph-duotone ph-lock-key me-1"></i>
                            تقفيل اليوم كامل ({{ $closableGroups->count() }} مجموعة قابلة للتقفيل)
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Overall Stats --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="rounded-3 d-flex align-items-center justify-content-center bg-primary-subtle text-primary"
                        style="width:48px;height:48px;flex-shrink:0;">
                        <i class="ph-duotone ph-student fs-4"></i>
                    </div>
                    <div>
                        <div class="fs-4 fw-bold lh-1">{{ number_format($overall['total']) }}</div>
                        <div class="text-body-secondary small mt-1">إجمالي الطلاب</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="rounded-3 d-flex align-items-center justify-content-center bg-success-subtle text-success"
                        style="width:48px;height:48px;flex-shrink:0;">
                        <i class="ph-duotone ph-check-circle fs-4"></i>
                    </div>
                    <div>
                        <div class="fs-4 fw-bold lh-1 text-success">{{ number_format($overall['present']) }}</div>
                        <div class="text-body-secondary small mt-1">الحاضرون</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="rounded-3 d-flex align-items-center justify-content-center bg-warning-subtle text-warning"
                        style="width:48px;height:48px;flex-shrink:0;">
                        <i class="ph-duotone ph-clock fs-4"></i>
                    </div>
                    <div>
                        <div class="fs-4 fw-bold lh-1 text-warning">{{ number_format($overall['late']) }}</div>
                        <div class="text-body-secondary small mt-1">المتأخرون</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="rounded-3 d-flex align-items-center justify-content-center bg-danger-subtle text-danger"
                        style="width:48px;height:48px;flex-shrink:0;">
                        <i class="ph-duotone ph-x-circle fs-4"></i>
                    </div>
                    <div>
                        <div class="fs-4 fw-bold lh-1 text-danger">{{ number_format($overall['absent']) }}</div>
                        <div class="text-body-secondary small mt-1">الغائبون</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @livewire('teacher-panel.parts.flash-message', [
        'successKey' => 'attendance-close-success',
        'errorKey' => 'attendance-close-error',
    ])

    {{-- Cancel Schedule Form --}}
    @if($cancellingScheduleId)
        <div class="card border-0 shadow-sm border-start border-4 border-danger mb-4">
            <div class="card-header bg-body d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-2">
                    <i class="ph-duotone ph-calendar-x text-danger fs-4"></i>
                    <h6 class="mb-0 fw-bold">إلغاء الحصة</h6>
                </div>
                <button type="button" wire:click="$set('cancellingScheduleId', null)"
                    class="btn btn-sm btn-outline-secondary">
                    <i class="ph-duotone ph-x me-1"></i>إغلاق
                </button>
            </div>
            <div class="card-body">
                <div class="alert alert-warning d-flex align-items-center gap-2">
                    <i class="ph-duotone ph-warning-circle fs-4"></i>
                    <span>لن يتم إلغاء الحصة إذا كانت مرتبطة بسجلات حضور أو غياب. تأكد من اختيار السبب الصحيح.</span>
                </div>
                <form wire:submit="cancelSchedule">
                    <div class="row g-3">
                        <div class="col-md-5">
                            <label class="form-label">سبب الإلغاء <span class="text-danger">*</span></label>
                            <select wire:model="cancellationReason"
                                class="form-select @error('cancellationReason') is-invalid @enderror" required>
                                <option value="">اختر سبب الإلغاء</option>
                                @foreach($cancellationReasons as $value => $label)
                                    <option value="{{ $value }}">{{ $label }}</option>
                                @endforeach
                            </select>
                            @error('cancellationReason')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-7">
                            <label class="form-label">ملاحظات <small class="text-body-secondary">(اختياري)</small></label>
                            <textarea wire:model="cancellationNotes" rows="2" maxlength="1000"
                                class="form-control @error('cancellationNotes') is-invalid @enderror"
                                placeholder="اكتب أي تفاصيل إضافية عن الإلغاء..."></textarea>
                            @error('cancellationNotes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-12 d-flex justify-content-end gap-2">
                            <button type="button" wire:click="$set('cancellingScheduleId', null)"
                                class="btn btn-light">تراجع</button>
                            <button type="submit" wire:loading.attr="disabled" wire:target="cancelSchedule"
                                class="btn btn-danger">
                                <span wire:loading.remove wire:target="cancelSchedule"><i
                                        class="ph-duotone ph-calendar-x me-1"></i>تأكيد إلغاء الحصة</span>
                                <span wire:loading wire:target="cancelSchedule"><span
                                        class="spinner-border spinner-border-sm me-1"></span>جارٍ الإلغاء...</span>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    @endif

    @if(count($selectedGroupIds) > 0)
        <div class="alert alert-info d-flex align-items-center gap-2 mb-3">
            <i class="ph-duotone ph-info fs-5"></i>
            <span>تم اختيار <strong>{{ count($selectedGroupIds) }}</strong> مجموعة</span>
        </div>
    @endif

    {{-- Pending Groups --}}
    @if($pendingGroups->count() > 0)
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-body d-flex align-items-center justify-content-between flex-wrap gap-2">
                <div class="d-flex align-items-center gap-2">
                    <i class="ph-duotone ph-broadcast text-warning fs-4"></i>
                    <h6 class="mb-0 fw-bold">قيد التنفيذ <span
                            class="badge bg-warning text-dark ms-2">{{ $pendingGroups->count() }}</span></h6>
                    <small class="text-body-secondary">مجموعات لسه حصصها النهارده ما خلصتش</small>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4" style="width:50px;">#</th>
                                <th>المجموعة</th>
                                <th>الفرع</th>
                                <th class="text-center">الحصص وإدارتها</th>
                                <th class="text-center">الطلاب</th>
                                <th class="text-center">حضور</th>
                                <th class="text-center">تأخير</th>
                                <th class="text-center">غياب</th>
                                <th class="text-center">إجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($pendingGroups as $group)
                                <tr wire:key="pending-{{ $group->id }}">
                                    <td class="ps-4 text-body-secondary small fw-semibold">{{ $loop->iteration }}</td>
                                    <td><span class="fw-semibold">{{ $group->name }}</span></td>
                                    <td><small class="text-body-secondary"><i
                                                class="ph-duotone ph-git-branch me-1"></i>{{ $group->branch?->name ?? '—' }}</small>
                                    </td>
                                    <td class="text-center">
                                        <div class="d-flex flex-column align-items-center gap-2">
                                            <span class="badge bg-info text-white">{{ $group->active_schedule_count }} حصة
                                                نشطة</span>
                                            @foreach($group->schedules->where('is_cancelled', false) as $schedule)
                                                <div class="d-flex align-items-center gap-1 flex-wrap justify-content-center">
                                                    <small class="text-body-secondary">{{ $schedule->start_time?->format('H:i') }} -
                                                        {{ $schedule->end_time?->format('H:i') }}</small>
                                                    <button type="button"
                                                        wire:click="startCancelSchedule({{ $group->id }}, {{ $schedule->id }})"
                                                        class="btn btn-sm btn-outline-danger" title="إلغاء الحصة">
                                                        <i class="ph-duotone ph-calendar-x"></i>
                                                    </button>
                                                </div>
                                            @endforeach
                                        </div>
                                    </td>
                                    <td class="text-center"><span
                                            class="badge bg-secondary-subtle text-secondary-emphasis">{{ $group->stats['total_students'] }}</span>
                                    </td>
                                    <td class="text-center"><span
                                            class="badge bg-success text-white">{{ $group->stats['present'] }}</span></td>
                                    <td class="text-center"><span
                                            class="badge bg-warning text-dark">{{ $group->stats['late'] }}</span></td>
                                    <td class="text-center"><span
                                            class="badge bg-danger text-white">{{ $group->stats['absent'] }}</span></td>
                                    <td class="text-center">
                                        <a href="{{ route('teacher.attendance.close-day.show', ['group' => $group->id, 'date' => $date]) }}"
                                            wire:navigate class="btn btn-sm btn-outline-primary">
                                            <i class="ph-duotone ph-eye me-1"></i>التفاصيل
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif

    {{-- Completed Groups --}}
    @if($completedGroups->count() > 0)
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-body d-flex align-items-center justify-content-between flex-wrap gap-2">
                <div class="d-flex align-items-center gap-2">
                    <i class="ph-duotone ph-check-circle text-success fs-4"></i>
                    <h6 class="mb-0 fw-bold">مكتملة <span
                            class="badge bg-success text-white ms-2">{{ $completedGroups->count() }}</span></h6>
                    <small class="text-body-secondary">مجموعات حصصها غير الملغية خلصت ولسه مقفلة يومها</small>
                </div>
                <div>
                    @if($confirmingCloseSelected)
                        <div class="d-flex gap-2 align-items-center">
                            <small class="text-warning-emphasis"><i class="ph-duotone ph-warning-circle me-1"></i>تأكيد تقفيل
                                {{ count($selectedGroupIds) }} مجموعة؟</small>
                            <button type="button" wire:click="closeSelected" wire:loading.attr="disabled"
                                class="btn btn-sm btn-warning">
                                <span wire:loading.remove wire:target="closeSelected">تأكيد</span>
                                <span wire:loading wire:target="closeSelected"><span
                                        class="spinner-border spinner-border-sm"></span></span>
                            </button>
                            <button type="button" wire:click="cancelClose"
                                class="btn btn-sm btn-outline-secondary">إلغاء</button>
                        </div>
                    @else
                        <button type="button" wire:click="confirmCloseSelected" class="btn btn-sm btn-warning"
                            @if(empty($selectedGroupIds)) disabled @endif>
                            <i class="ph-duotone ph-lock me-1"></i>تقفيل المختارة @if(count($selectedGroupIds) > 0)
                            ({{ count($selectedGroupIds) }}) @endif
                        </button>
                    @endif
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4" style="width:50px;">
                                    <input type="checkbox" class="form-check-input"
                                        wire:click="{{ count($selectedGroupIds) === $completedGroups->count() && $completedGroups->isNotEmpty() ? 'deselectAllGroups' : 'selectAllGroups' }}"
                                        @if($completedGroups->isNotEmpty() && count($selectedGroupIds) === $completedGroups->count()) checked @endif>
                                </th>
                                <th>المجموعة</th>
                                <th>الفرع</th>
                                <th class="text-center">الحصص وإدارتها</th>
                                <th class="text-center">الطلاب</th>
                                <th class="text-center">حضور</th>
                                <th class="text-center">تأخير</th>
                                <th class="text-center">غياب</th>
                                <th class="text-center">إجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($completedGroups as $group)
                                @php $isSelected = in_array((string) $group->id, $selectedGroupIds, true); @endphp
                                <tr wire:key="completed-{{ $group->id }}" class="{{ $isSelected ? 'table-primary' : '' }}">
                                    <td class="ps-4">
                                        @if($group->is_closed)
                                            <i class="ph-duotone ph-check-circle text-success fs-5" title="مقفلة بالفعل"></i>
                                        @else
                                            <input type="checkbox" class="form-check-input"
                                                wire:click="toggleGroupSelection({{ $group->id }})" @if($isSelected) checked @endif>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="fw-semibold">{{ $group->name }}</span>
                                        @if($group->is_closed)
                                            <span class="badge bg-success-subtle text-success-emphasis ms-2"><i
                                                    class="ph-duotone ph-lock me-1"></i>مقفلة</span>
                                        @endif
                                    </td>
                                    <td><small class="text-body-secondary"><i
                                                class="ph-duotone ph-git-branch me-1"></i>{{ $group->branch?->name ?? '—' }}</small>
                                    </td>
                                    <td class="text-center">
                                        <div class="d-flex flex-column align-items-center gap-2">
                                            <span class="badge bg-info text-white">{{ $group->active_schedule_count }} حصة
                                                نشطة</span>
                                            @foreach($group->schedules->where('is_cancelled', false) as $schedule)
                                                <div class="d-flex align-items-center gap-1 flex-wrap justify-content-center">
                                                    <small class="text-body-secondary">{{ $schedule->start_time?->format('H:i') }} -
                                                        {{ $schedule->end_time?->format('H:i') }}</small>
                                                    <button type="button"
                                                        wire:click="startCancelSchedule({{ $group->id }}, {{ $schedule->id }})"
                                                        class="btn btn-sm btn-outline-danger" title="إلغاء الحصة">
                                                        <i class="ph-duotone ph-calendar-x"></i>
                                                    </button>
                                                </div>
                                            @endforeach
                                        </div>
                                    </td>
                                    <td class="text-center"><span
                                            class="badge bg-secondary-subtle text-secondary-emphasis">{{ $group->stats['total_students'] }}</span>
                                    </td>
                                    <td class="text-center"><span
                                            class="badge bg-success text-white">{{ $group->stats['present'] }}</span></td>
                                    <td class="text-center"><span
                                            class="badge bg-warning text-dark">{{ $group->stats['late'] }}</span></td>
                                    <td class="text-center"><span
                                            class="badge bg-danger text-white">{{ $group->stats['absent'] }}</span></td>
                                    <td class="text-center">
                                        <a href="{{ route('teacher.attendance.close-day.show', ['group' => $group->id, 'date' => $date]) }}"
                                            wire:navigate class="btn btn-sm btn-outline-primary">
                                            <i class="ph-duotone ph-eye me-1"></i>التفاصيل
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif

    {{-- Closed Groups --}}
    @if($closedGroups->count() > 0)
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-body d-flex align-items-center justify-content-between flex-wrap gap-2">
                <div class="d-flex align-items-center gap-2">
                    <i class="ph-duotone ph-lock text-secondary fs-4"></i>
                    <h6 class="mb-0 fw-bold">مقفلة <span
                            class="badge bg-secondary text-white ms-2">{{ $closedGroups->count() }}</span></h6>
                    <small class="text-body-secondary">مجموعات تم تقفيل يومها بالفعل</small>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4" style="width:50px;">#</th>
                                <th>المجموعة</th>
                                <th>الفرع</th>
                                <th class="text-center">الحصص</th>
                                <th class="text-center">الطلاب</th>
                                <th class="text-center">حضور</th>
                                <th class="text-center">تأخير</th>
                                <th class="text-center">غياب</th>
                                <th class="text-center">إجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($closedGroups as $group)
                                <tr wire:key="closed-{{ $group->id }}" class="opacity-75">
                                    <td class="ps-4 text-body-secondary small fw-semibold">{{ $loop->iteration }}</td>
                                    <td>
                                        <span class="fw-semibold">{{ $group->name }}</span>
                                        <span class="badge bg-success-subtle text-success-emphasis ms-2"><i
                                                class="ph-duotone ph-lock me-1"></i>مقفلة</span>
                                    </td>
                                    <td><small class="text-body-secondary"><i
                                                class="ph-duotone ph-git-branch me-1"></i>{{ $group->branch?->name ?? '—' }}</small>
                                    </td>
                                    <td class="text-center"><span
                                            class="badge bg-info text-white">{{ $group->active_schedule_count }}</span></td>
                                    <td class="text-center"><span
                                            class="badge bg-secondary-subtle text-secondary-emphasis">{{ $group->stats['total_students'] }}</span>
                                    </td>
                                    <td class="text-center"><span
                                            class="badge bg-success text-white">{{ $group->stats['present'] }}</span></td>
                                    <td class="text-center"><span
                                            class="badge bg-warning text-dark">{{ $group->stats['late'] }}</span></td>
                                    <td class="text-center"><span
                                            class="badge bg-danger text-white">{{ $group->stats['absent'] }}</span></td>
                                    <td class="text-center">
                                        <a href="{{ route('teacher.attendance.close-day.show', ['group' => $group->id, 'date' => $date]) }}"
                                            wire:navigate class="btn btn-sm btn-outline-secondary">
                                            <i class="ph-duotone ph-eye me-1"></i>التفاصيل
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif

    {{-- Cancelled Schedules Section --}}
    @if($cancelledSchedules->isNotEmpty())
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-body d-flex align-items-center justify-content-between flex-wrap gap-2">
                <div class="d-flex align-items-center gap-2">
                    <i class="ph-duotone ph-calendar-x text-danger fs-4"></i>
                    <h6 class="mb-0 fw-bold">الحصص الملغية <span
                            class="badge bg-danger text-white ms-2">{{ $cancelledSchedules->count() }}</span></h6>
                    <small class="text-body-secondary">الحصص الملغية في التاريخ والفرع المحددين</small>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4">#</th>
                                <th>المجموعة</th>
                                <th>الفرع</th>
                                <th class="text-center">موعد الحصة</th>
                                <th>سبب الإلغاء</th>
                                <th>الملاحظات</th>
                                <th class="text-center">تاريخ الإلغاء</th>
                                <th class="text-center">إجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($cancelledSchedules as $item)
                                @php
                                    $cancelledGroup = $item['group'];
                                    $cancelledSchedule = $item['schedule'];
                                @endphp
                                <tr wire:key="cancelled-schedule-{{ $cancelledSchedule->id }}">
                                    <td class="ps-4 text-body-secondary small fw-semibold">{{ $loop->iteration }}</td>
                                    <td><span class="fw-semibold">{{ $cancelledGroup->name }}</span></td>
                                    <td><small class="text-body-secondary">{{ $cancelledGroup->branch?->name ?? '—' }}</small>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-secondary-subtle text-secondary-emphasis">
                                            {{ $cancelledSchedule->start_time?->format('H:i') }} -
                                            {{ $cancelledSchedule->end_time?->format('H:i') }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-danger-subtle text-danger-emphasis">
                                            {{ $cancelledSchedule->cancellation_reason?->label() ?? 'غير محدد' }}
                                        </span>
                                    </td>
                                    <td><small
                                            class="text-body-secondary">{{ $cancelledSchedule->cancellation_notes ?: '—' }}</small>
                                    </td>
                                    <td class="text-center">
                                        <small
                                            class="text-body-secondary">{{ $cancelledSchedule->cancelled_at?->format('Y-m-d H:i') ?? '—' }}</small>
                                    </td>
                                    <td class="text-center">
                                        @if(!$cancelledGroup->is_closed)
                                            <button type="button"
                                                wire:click="restoreCancelledSchedule({{ $cancelledGroup->id }}, {{ $cancelledSchedule->id }})"
                                                wire:loading.attr="disabled" class="btn btn-sm btn-outline-success">
                                                <i class="ph-duotone ph-arrow-counter-clockwise me-1"></i>إعادة الحصة
                                            </button>
                                        @else
                                            <span class="badge bg-secondary-subtle text-secondary-emphasis">المجموعة مقفلة</span>
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

    {{-- Empty State --}}
    @if($groups->isEmpty())
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center py-5">
                <i class="ph-duotone ph-calendar-x fs-1 d-block text-body-secondary mb-2"></i>
                <h6 class="text-body-secondary mb-1">لا توجد مجموعات ليها حصص في اليوم دا</h6>
                <small class="text-body-secondary">اختر تاريخ تاني أو أضف مواعيد للمجموعات</small>
            </div>
        </div>
    @elseif($pendingGroups->isEmpty() && $completedGroups->isEmpty() && $closedGroups->isEmpty() && $cancelledSchedules->isEmpty())
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center py-5">
                <i class="ph-duotone ph-calendar-check fs-1 d-block text-body-secondary mb-2"></i>
                <h6 class="text-body-secondary mb-1">لا توجد حصص تحتاج إلى إجراء</h6>
                <small class="text-body-secondary">لا توجد حصص نشطة أو ملغية لعرضها في هذا اليوم.</small>
            </div>
        </div>
    @endif
</div>
