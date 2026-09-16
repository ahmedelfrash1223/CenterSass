<div>
    {{-- Success Message --}}
    @if(session()->has('staff-success'))
        <div class="alert alert-success d-flex align-items-center gap-2 mb-4">
            <i class="ph-duotone ph-check-circle fs-5"></i>
            <span>{{ session('staff-success') }}</span>
        </div>
    @endif

    {{-- Error Message --}}
    @if(session()->has('staff-error'))
        <div class="alert alert-danger d-flex align-items-center gap-2 mb-4">
            <i class="ph-duotone ph-x-circle fs-5"></i>
            <span>{{ session('staff-error') }}</span>
        </div>
    @endif

    {{-- Staff Header --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
                <div class="d-flex align-items-center gap-3">
                    <div
                        class="avtar avtar-l bg-{{ $teacher->role->color() }} bg-opacity-10 text-{{ $teacher->role->color() }}">
                        <i class="ph-duotone {{ $teacher->role->icon() }} fs-2"></i>
                    </div>
                    <div>
                        <h5 class="mb-1 d-flex align-items-center gap-2">
                            {{ $teacher->name }}
                            <span
                                class="badge bg-{{ $teacher->role->color() }} bg-opacity-10 text-{{ $teacher->role->color() }}">
                                <i class="ph-duotone {{ $teacher->role->icon() }} me-1"></i>
                                {{ $teacher->role->label() }}
                            </span>
                        </h5>
                        <span class="text-muted small">
                            <i class="ph-duotone ph-identification-card me-1"></i>
                            {{ $teacher->code }}
                        </span>
                    </div>
                </div>
                <div class="d-flex gap-2">
                    @if($canUpdate)
                        <a href="{{ route('teacher.staff.edit', $teacher) }}" wire:navigate class="btn btn-primary">
                            <i class="ph-duotone ph-pencil-simple me-1"></i>
                            تعديل
                        </a>
                    @endif
                    <a href="{{ route('teacher.staff.index') }}" wire:navigate class="btn btn-light border">
                        <i class="ph-duotone ph-arrow-right me-1"></i>
                        رجوع
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        {{-- Staff Details --}}
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-transparent border-bottom">
                    <h6 class="mb-0 fw-bold">
                        <i class="ph-duotone ph-info me-1"></i>
                        بيانات العضو
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-sm-6">
                            <small class="text-muted d-block mb-1">الاسم</small>
                            <span class="fw-semibold">{{ $teacher->name }}</span>
                        </div>
                        <div class="col-sm-6">
                            <small class="text-muted d-block mb-1">اسم المستخدم</small>
                            <span class="fw-semibold">{{ $teacher->username }}</span>
                        </div>
                        <div class="col-sm-6">
                            <small class="text-muted d-block mb-1">البريد الإلكتروني</small>
                            <a href="mailto:{{ $teacher->email }}" class="text-decoration-none small">
                                {{ $teacher->email }}
                            </a>
                        </div>
                        <div class="col-sm-6">
                            <small class="text-muted d-block mb-1">الهاتف</small>
                            @if($teacher->phone)
                                <a href="tel:{{ $teacher->phone }}"
                                   class="text-decoration-none small">{{ $teacher->phone }}</a>
                            @else
                                <span class="text-muted">غير محدد</span>
                            @endif
                        </div>
                        <div class="col-sm-6">
                            <small class="text-muted d-block mb-1">نوع الحساب</small>
                            <span
                                class="badge bg-{{ $teacher->role->color() }} bg-opacity-10 text-{{ $teacher->role->color() }}">
                                <i class="ph-duotone {{ $teacher->role->icon() }} me-1"></i>
                                {{ $teacher->role->label() }}
                            </span>
                        </div>
                        <div class="col-sm-6">
                            <small class="text-muted d-block mb-1">تابع لـ</small>
                            @if($teacher->owner)
                                <span class="small">
                                    <i class="ph-duotone ph-crown text-warning me-1"></i>
                                    {{ $teacher->owner->name }}
                                </span>
                            @else
                                <span class="small text-muted">
                                    <i class="ph-duotone ph-crown text-warning me-1"></i>
                                    صاحب الحساب
                                </span>
                            @endif
                        </div>

                        <div class="col-sm-6">
                            <small class="text-muted d-block mb-1">أضيف بواسطة</small>
                            @if($teacher->creator)
                                <span
                                    class="badge bg-{{ $teacher->creator->role->color() }} bg-opacity-10 text-{{ $teacher->creator->role->color() }}">
                                    <i class="ph-duotone {{ $teacher->creator->role->icon() }} me-1"></i>
                                    {{ $teacher->creator->name }}
                                </span>
                            @else
                                <span class="text-muted small">—</span>
                            @endif
                        </div>

                        <div class="col-sm-6">
                            <small class="text-muted d-block mb-1">الحالة</small>
                            @if($teacher->is_active)
                                <span class="badge bg-success bg-opacity-10 text-success">
                                    <i class="ph-duotone ph-check-circle me-1"></i>
                                    نشط
                                </span>
                            @else
                                <span class="badge bg-danger bg-opacity-10 text-danger">
                                    <i class="ph-duotone ph-x-circle me-1"></i>
                                    غير نشط
                                </span>
                            @endif
                        </div>
                        <div class="col-sm-6">
                            <small class="text-muted d-block mb-1">تاريخ الإنشاء</small>
                            <span class="small">
                                <i class="ph-duotone ph-calendar me-1 text-muted"></i>
                                {{ $teacher->created_at?->format('Y-m-d h:i A') }}
                            </span>
                        </div>
                        <div class="col-sm-6">
                            <small class="text-muted d-block mb-1">آخر تحديث</small>
                            <span class="small">
                                <i class="ph-duotone ph-clock me-1 text-muted"></i>
                                {{ $teacher->updated_at?->format('Y-m-d h:i A') }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Statistics --}}
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-transparent border-bottom">
                    <h6 class="mb-0 fw-bold">
                        <i class="ph-duotone ph-chart-bar me-1"></i>
                        الإحصائيات
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-6">
                            <div class="stat-card stat-primary">
                                <div class="stat-icon"><i class="ph-duotone ph-student"></i></div>
                                <div class="stat-info">
                                    <span class="stat-num">{{ $teacher->students->count() }}</span>
                                    <span class="stat-label">الطلاب</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="stat-card stat-success">
                                <div class="stat-icon"><i class="ph-duotone ph-users-three"></i></div>
                                <div class="stat-info">
                                    <span class="stat-num">{{ $teacher->groups->count() }}</span>
                                    <span class="stat-label">المجموعات</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="stat-card stat-warning">
                                <div class="stat-icon"><i class="ph-duotone ph-books"></i></div>
                                <div class="stat-info">
                                    <span class="stat-num">{{ $teacher->subjects->count() }}</span>
                                    <span class="stat-label">المواد</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="stat-card stat-info">
                                <div class="stat-icon"><i class="ph-duotone ph-git-branch"></i></div>
                                <div class="stat-info">
                                    <span class="stat-num">{{ $teacher->branches->count() }}</span>
                                    <span class="stat-label">الفروع</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Permissions --}}
        @if(!$teacher->isOwner())
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div
                        class="card-header bg-transparent border-bottom d-flex justify-content-between align-items-center">
                        <h6 class="mb-0 fw-bold">
                            <i class="ph-duotone ph-shield-check me-1"></i>
                            الصلاحيات
                        </h6>
                        <span class="badge bg-primary bg-opacity-10 text-primary">
                            {{ count($userPermissions) }} صلاحية
                        </span>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            @foreach($groupedPermissions as $groupName => $groupPermissions)
                                <div class="col-md-6 col-lg-4">
                                    <div class="permission-view-group">
                                        <div class="permission-view-header">
                                            <span class="badge bg-primary bg-opacity-10 text-primary">
                                                {{ $groupName }}
                                            </span>
                                        </div>
                                        <div class="permission-view-body">
                                            @foreach($groupPermissions as $permission)
                                                <div
                                                    class="permission-view-item {{ in_array($permission->value, $userPermissions) ? 'is-granted' : 'is-denied' }}">
                                                    <i class="ph-duotone {{ in_array($permission->value, $userPermissions) ? 'ph-check-circle' : 'ph-x-circle' }}"></i>
                                                    <span>{{ $permission->label() }}</span>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        @endif

        {{-- Branches --}}
        @if(!$teacher->isOwner())
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div
                        class="card-header bg-transparent border-bottom d-flex justify-content-between align-items-center">
                        <h6 class="mb-0 fw-bold">
                            <i class="ph-duotone ph-git-branch me-1"></i>
                            الفروع
                        </h6>
                        <span class="badge bg-primary bg-opacity-10 text-primary">
                            {{ $teacher->branches->count() }}
                        </span>
                    </div>
                    <div class="card-body">
                        @if($teacher->branches->count() > 0)
                            <div class="row g-3">
                                @foreach($teacher->branches as $branch)
                                    <div class="col-md-6 col-lg-4">
                                        <div class="d-flex align-items-center gap-3 p-3 bg-light rounded-3 border">
                                            <div class="avtar avtar-s bg-primary bg-opacity-10 text-primary">
                                                <i class="ph-duotone ph-git-branch fs-5"></i>
                                            </div>
                                            <div class="flex-grow-1">
                                                <h6 class="mb-1 fw-semibold">{{ $branch->name }}</h6>
                                                <small class="text-muted d-block">
                                                    @if($branch->phone)
                                                        <i class="ph-duotone ph-phone me-1"></i>{{ $branch->phone }}
                                                    @else
                                                        لا يوجد هاتف
                                                    @endif
                                                </small>
                                            </div>
                                            @if($branch->is_active)
                                                <span class="badge bg-success bg-opacity-10 text-success">نشط</span>
                                            @else
                                                <span class="badge bg-danger bg-opacity-10 text-danger">غير نشط</span>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-4">
                                <i class="ph-duotone ph-git-branch fs-1 d-block text-muted mb-2"></i>
                                <p class="text-muted mb-0">لم يتم ربط العضو بأي فرع بعد</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @endif

        {{-- Schedules --}}
        @if($schedules->count() > 0)
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div
                        class="card-header bg-transparent border-bottom d-flex justify-content-between align-items-center">
                        <h6 class="mb-0 fw-bold">
                            <i class="ph-duotone ph-calendar-check me-1"></i>
                            المواعيد الأسبوعية
                        </h6>
                        <span class="badge bg-primary bg-opacity-10 text-primary">
                            {{ $schedules->flatten()->count() }} موعد
                        </span>
                    </div>
                    <div class="card-body">
                        @php
                            $dayLabels = [
                                'Saturday' => 'السبت',
                                'Sunday' => 'الأحد',
                                'Monday' => 'الإثنين',
                                'Tuesday' => 'الثلاثاء',
                                'Wednesday' => 'الأربعاء',
                                'Thursday' => 'الخميس',
                                'Friday' => 'الجمعة',
                            ];
                            $dayColors = [
                                'Saturday' => 'primary',
                                'Sunday' => 'success',
                                'Monday' => 'info',
                                'Tuesday' => 'warning',
                                'Wednesday' => 'danger',
                                'Thursday' => 'secondary',
                                'Friday' => 'dark',
                            ];
                            $today = now()->format('l');
                        @endphp

                        <div class="row g-3">
                            @foreach($schedules as $day => $daySchedules)
                                @php
                                    $isToday = $day === $today;
                                @endphp
                                <div class="col-md-6">
                                    <div class="schedule-day-card {{ $isToday ? 'is-today' : '' }}">
                                        <div class="schedule-day-header">
                                            <span
                                                class="badge bg-{{ $dayColors[$day] ?? 'secondary' }} bg-opacity-10 text-{{ $dayColors[$day] ?? 'secondary' }}">
                                                <i class="ph-duotone ph-calendar-blank me-1"></i>
                                                {{ $dayLabels[$day] ?? $day }}
                                            </span>
                                            @if($isToday)
                                                <span class="badge bg-danger text-white">
                                                    <i class="ph-duotone ph-broadcast me-1"></i>
                                                    النهارده
                                                </span>
                                            @endif
                                            <span class="badge bg-light text-dark border ms-auto">
                                                {{ $daySchedules->count() }} حصة
                                            </span>
                                        </div>
                                        <div class="schedule-day-body">
                                            @foreach($daySchedules as $schedule)
                                                <div class="schedule-mini-item">
                                                    <div class="schedule-mini-time">
                                                        <i class="ph-duotone ph-clock text-primary"></i>
                                                        <span
                                                            class="fw-semibold">{{ $schedule->start_time->format('H:i') }}</span>
                                                        <span class="text-muted">-</span>
                                                        <span
                                                            class="text-muted">{{ $schedule->end_time->format('H:i') }}</span>
                                                    </div>
                                                    <div class="schedule-mini-details">
                                                        <div class="d-flex align-items-center gap-2 flex-wrap">
                                                            <span
                                                                class="fw-semibold small">{{ $schedule->subject?->subject_label ?? '—' }}</span>
                                                            <span class="text-muted small">•</span>
                                                            <span
                                                                class="text-muted small">{{ $schedule->subject?->grade_label ?? '—' }}</span>
                                                        </div>
                                                        <div class="d-flex align-items-center gap-2 mt-1 flex-wrap">
                                                            <small class="text-muted">
                                                                <i class="ph-duotone ph-users-three me-1"></i>
                                                                {{ $schedule->group?->name ?? '—' }}
                                                            </small>
                                                            <small class="text-muted">
                                                                <i class="ph-duotone ph-git-branch me-1"></i>
                                                                {{ $schedule->group?->branch?->name ?? '—' }}
                                                            </small>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        @else
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-transparent border-bottom">
                        <h6 class="mb-0 fw-bold">
                            <i class="ph-duotone ph-calendar-check me-1"></i>
                            المواعيد الأسبوعية
                        </h6>
                    </div>
                    <div class="card-body text-center py-4">
                        <i class="ph-duotone ph-calendar-x fs-1 d-block text-muted mb-2"></i>
                        <p class="text-muted mb-0">لا توجد مواعيد مسجلة لهذا العضو</p>
                    </div>
                </div>
            </div>
        @endif

        {{-- Groups --}}
        @if($teacher->groups->count() > 0)
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm h-100">
                    <div
                        class="card-header bg-transparent border-bottom d-flex justify-content-between align-items-center">
                        <h6 class="mb-0 fw-bold">
                            <i class="ph-duotone ph-users-three me-1"></i>
                            المجموعات
                        </h6>
                        <span class="badge bg-primary bg-opacity-10 text-primary">
                            {{ $teacher->groups->count() }}
                        </span>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-sm table-hover align-middle mb-0">
                                <thead>
                                <tr>
                                    <th class="ps-3">المجموعة</th>
                                    <th>الطلاب</th>
                                    <th>الحالة</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($teacher->groups as $group)
                                    <tr>
                                        <td class="ps-3">
                                            <span class="fw-semibold">{{ $group->name }}</span>
                                        </td>
                                        <td>
                                            <small class="text-muted">{{ $group->students->count() ?? 0 }}</small>
                                        </td>
                                        <td>
                                            @php
                                                $statusEnum = \App\Enums\Teacher\GroupStatusEnum::tryFrom($group->status);
                                                $color = $statusEnum?->color() ?? 'secondary';
                                                $label = $statusEnum?->label() ?? $group->status;
                                            @endphp
                                            <span class="badge bg-{{ $color }} bg-opacity-10 text-{{ $color }}">
                                                {{ $label }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        {{-- Subjects --}}
        @if($teacher->subjects->count() > 0)
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm h-100">
                    <div
                        class="card-header bg-transparent border-bottom d-flex justify-content-between align-items-center">
                        <h6 class="mb-0 fw-bold">
                            <i class="ph-duotone ph-notebook me-1"></i>
                            المواد الدراسية
                        </h6>
                        <span class="badge bg-primary bg-opacity-10 text-primary">
                            {{ $teacher->subjects->count() }}
                        </span>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-sm table-hover align-middle mb-0">
                                <thead>
                                <tr>
                                    <th class="ps-3">المرحلة</th>
                                    <th>المادة</th>
                                    <th>الحالة</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($teacher->subjects as $subject)
                                    <tr>
                                        <td class="ps-3">
                                            <small class="fw-semibold">{{ $subject->grade_label }}</small>
                                        </td>
                                        <td>
                                            <small>{{ $subject->subject_label }}</small>
                                        </td>
                                        <td>
                                            @if($subject->is_active)
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
            </div>
        @endif

        {{-- Students --}}
        @if($teacher->students->count() > 0)
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div
                        class="card-header bg-transparent border-bottom d-flex justify-content-between align-items-center">
                        <h6 class="mb-0 fw-bold">
                            <i class="ph-duotone ph-student me-1"></i>
                            الطلاب
                        </h6>
                        <span class="badge bg-primary bg-opacity-10 text-primary">
                            {{ $teacher->students->count() }}
                        </span>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead>
                                <tr>
                                    <th class="ps-4" style="width: 60px;">#</th>
                                    <th>الطالب</th>
                                    <th>البريد الإلكتروني</th>
                                    <th>الهاتف</th>
                                    <th>الحالة</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($teacher->students as $index => $student)
                                    <tr>
                                        <td class="ps-4">
                                            <span class="text-muted fw-semibold">{{ $index + 1 }}</span>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center gap-2">
                                                <div class="student-avatar">
                                                    {{ mb_substr($student->name, 0, 1) }}
                                                </div>
                                                <span class="fw-semibold">{{ $student->name }}</span>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="text-muted small">{{ $student->email }}</span>
                                        </td>
                                        <td>
                                            @if($student->phone)
                                                <span class="text-muted small">{{ $student->phone }}</span>
                                            @else
                                                <span class="text-muted">—</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($student->is_active)
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
            </div>
        @endif
    </div>

    {{-- Delete Section --}}
    @if($canDelete)
        <div class="d-flex justify-content-end gap-2 mt-4">
            @if($confirmingDelete)
                <button type="button" wire:click="delete" wire:loading.attr="disabled" wire:target="delete"
                        class="btn btn-danger">
                    <span wire:loading.remove wire:target="delete">
                        <i class="ph-duotone ph-trash me-1"></i>
                        تأكيد الحذف
                    </span>
                    <span wire:loading wire:target="delete">
                        <span class="spinner-border spinner-border-sm me-1"></span>
                        جاري الحذف...
                    </span>
                </button>
                <button type="button" wire:click="cancelDelete" class="btn btn-light border">
                    إلغاء
                </button>
            @else
                <button type="button" wire:click="confirmDelete" class="btn btn-outline-danger">
                    <i class="ph-duotone ph-trash me-1"></i>
                    حذف العضو
                </button>
            @endif
        </div>
    @endif
</div>
