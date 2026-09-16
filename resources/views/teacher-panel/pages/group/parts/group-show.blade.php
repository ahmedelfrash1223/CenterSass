<div>
    {{-- Success Message --}}
    @if(session()->has('group-success'))
        <div class="alert alert-success d-flex align-items-center gap-2 mb-4">
            <i class="ph-duotone ph-check-circle fs-5"></i>
            <span>{{ session('group-success') }}</span>
        </div>
    @endif

    {{-- Error Message --}}
    @if(session()->has('group-error'))
        <div class="alert alert-danger d-flex align-items-center gap-2 mb-4">
            <i class="ph-duotone ph-x-circle fs-5"></i>
            <span>{{ session('group-error') }}</span>
        </div>
    @endif

    {{-- Group Header --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="avtar avtar-l bg-primary bg-opacity-10 text-primary">
                        <i class="ph-duotone ph-users-three fs-2"></i>
                    </div>
                    <div>
                        <h5 class="mb-1 d-flex align-items-center gap-2 flex-wrap">
                            {{ $group->name }}
                            @php
                                $statusEnum = \App\Enums\Teacher\GroupStatusEnum::tryFrom($group->status);
                                $statusColor = $statusEnum?->color() ?? 'secondary';
                                $statusLabel = $statusEnum?->label() ?? $group->status;
                            @endphp
                            <span class="badge bg-{{ $statusColor }} bg-opacity-10 text-{{ $statusColor }}">
                                {{ $statusLabel }}
                            </span>
                        </h5>
                        <span class="text-muted small">
                            <i class="ph-duotone ph-git-branch me-1"></i>
                            {{ $group->branch?->name ?? 'بدون فرع' }}
                        </span>
                    </div>
                </div>
                <div class="d-flex gap-2">
                    @if($canUpdate)
                        <a href="{{ route('teacher.groups.edit', $group) }}" wire:navigate class="btn btn-primary">
                            <i class="ph-duotone ph-pencil-simple me-1"></i>
                            تعديل
                        </a>
                    @endif
                    <a href="{{ route('teacher.groups.index') }}" wire:navigate class="btn btn-light border">
                        <i class="ph-duotone ph-arrow-right me-1"></i>
                        رجوع
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        {{-- Group Details --}}
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-transparent border-bottom">
                    <h6 class="mb-0 fw-bold">
                        <i class="ph-duotone ph-info me-1"></i>
                        بيانات المجموعة
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-sm-6">
                            <small class="text-muted d-block mb-1">الاسم</small>
                            <span class="fw-semibold">{{ $group->name }}</span>
                        </div>
                        <div class="col-sm-6">
                            <small class="text-muted d-block mb-1">الفرع</small>
                            <span class="fw-semibold">{{ $group->branch?->name ?? '—' }}</span>
                        </div>
                        <div class="col-sm-6">
                            <small class="text-muted d-block mb-1">المرحلة الدراسية</small>
                            @if($group->grade)
                                @php
                                    $gradeLabel = \App\Enums\Teacher\GradeEnum::tryFrom($group->grade)?->label() ?? $group->grade;
                                    $gradeColor = str_starts_with($group->grade, 'primary_') ? 'success' : (str_starts_with($group->grade, 'prep_') ? 'primary' : 'info');
                                @endphp
                                <span class="badge bg-{{ $gradeColor }} bg-opacity-10 text-{{ $gradeColor }}">
                                    <i class="ph-duotone ph-student me-1"></i>
                                    {{ $gradeLabel }}
                                </span>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </div>
                        <div class="col-sm-6">
                            <small class="text-muted d-block mb-1">الحالة</small>
                            <span class="badge bg-{{ $statusColor }} bg-opacity-10 text-{{ $statusColor }}">
                                {{ $statusLabel }}
                            </span>
                        </div>
                        <div class="col-sm-6">
                            <small class="text-muted d-block mb-1">الطلاب</small>
                            <span class="fw-semibold">
                                <i class="ph-duotone ph-users me-1 text-primary"></i>
                                {{ $activeStudentsCount ?? $group->students->count() }}
                            </span>
                            @if($group->max_students > 0)
                                <span class="text-muted">/ {{ $group->max_students }}</span>
                            @endif
                        </div>
                        <div class="col-sm-6">
                            <small class="text-muted d-block mb-1">أضيف بواسطة</small>
                            @if($group->creator)
                                <span
                                    class="badge bg-{{ $group->creator->role->color() }} bg-opacity-10 text-{{ $group->creator->role->color() }}">
                                    <i class="ph-duotone {{ $group->creator->role->icon() }} me-1"></i>
                                    {{ $group->creator->name }}
                                </span>
                            @else
                                <span class="text-muted small">—</span>
                            @endif
                        </div>
                        <div class="col-sm-6">
                            <small class="text-muted d-block mb-1">تاريخ البداية</small>
                            <span class="small">
                                <i class="ph-duotone ph-calendar me-1 text-muted"></i>
                                {{ $group->start_date?->format('Y-m-d') ?? 'غير محدد' }}
                            </span>
                        </div>
                        <div class="col-sm-6">
                            <small class="text-muted d-block mb-1">تاريخ النهاية</small>
                            <span class="small">
                                <i class="ph-duotone ph-calendar me-1 text-muted"></i>
                                {{ $group->end_date?->format('Y-m-d') ?? 'غير محدد' }}
                            </span>
                        </div>
                        <div class="col-sm-6">
                            <small class="text-muted d-block mb-1">تاريخ الإنشاء</small>
                            <span class="small">
                                <i class="ph-duotone ph-clock me-1 text-muted"></i>
                                {{ $group->created_at?->format('Y-m-d h:i A') }}
                            </span>
                        </div>
                        <div class="col-sm-6">
                            <small class="text-muted d-block mb-1">آخر تحديث</small>
                            <span class="small">
                                <i class="ph-duotone ph-clock me-1 text-muted"></i>
                                {{ $group->updated_at?->format('Y-m-d h:i A') }}
                            </span>
                        </div>
                        @if($group->description)
                            <div class="col-12">
                                <small class="text-muted d-block mb-1">الوصف</small>
                                <p class="mb-0 small">{{ $group->description }}</p>
                            </div>
                        @endif
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
                                <div class="stat-icon">
                                    <i class="ph-duotone ph-student"></i>
                                </div>
                                <div>
                                    <span
                                        class="stat-num">{{ $activeStudentsCount ?? $group->students->count() }}</span>
                                    <span class="stat-label">الطلاب</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="stat-card stat-success">
                                <div class="stat-icon">
                                    <i class="ph-duotone ph-calendar-check"></i>
                                </div>
                                <div>
                                    <span class="stat-num">{{ $group->schedules->count() }}</span>
                                    <span class="stat-label">المواعيد</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="stat-card stat-warning">
                                <div class="stat-icon">
                                    <i class="ph-duotone ph-books"></i>
                                </div>
                                <div>
                                    <span
                                        class="stat-num">{{ $group->schedules->pluck('subject_id')->unique()->count() }}</span>
                                    <span class="stat-label">المواد</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="stat-card stat-info">
                                <div class="stat-icon">
                                    <i class="ph-duotone ph-chalkboard-teacher"></i>
                                </div>
                                <div>
                                    <span
                                        class="stat-num">{{ $group->schedules->pluck('teacher_id')->unique()->count() }}</span>
                                    <span class="stat-label">المدرسين</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Schedules --}}
        @if($group->schedules->count() > 0)
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div
                        class="card-header bg-transparent border-bottom d-flex justify-content-between align-items-center">
                        <h6 class="mb-0 fw-bold">
                            <i class="ph-duotone ph-calendar-check me-1"></i>
                            المواعيد والمواد
                        </h6>
                        <span class="badge bg-primary bg-opacity-10 text-primary">
                            {{ $group->schedules->count() }} موعد
                        </span>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead>
                                <tr>
                                    <th class="ps-4" style="width: 60px;">#</th>
                                    <th>اليوم</th>
                                    <th>الوقت</th>
                                    <th>المادة</th>
                                    <th>المرحلة</th>
                                    <th>المدرس</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($group->schedules as $index => $schedule)
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
                                        $dayLabel = $dayLabels[$schedule->day] ?? $schedule->day;
                                        $dayColor = match($schedule->day) {
                                            'Saturday' => 'primary',
                                            'Sunday' => 'success',
                                            'Monday' => 'info',
                                            'Tuesday' => 'warning',
                                            'Wednesday' => 'danger',
                                            'Thursday' => 'secondary',
                                            'Friday' => 'dark',
                                            default => 'secondary',
                                        };
                                    @endphp
                                    <tr wire:key="schedule-{{ $schedule->id }}">
                                        <td class="ps-4">
                                            <span class="text-muted fw-semibold">{{ $index + 1 }}</span>
                                        </td>
                                        <td>
                                            <span class="badge bg-{{ $dayColor }} bg-opacity-10 text-{{ $dayColor }}">
                                                <i class="ph-duotone ph-calendar-blank me-1"></i>
                                                {{ $dayLabel }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center gap-2">
                                                <i class="ph-duotone ph-clock text-primary"></i>
                                                <span
                                                    class="fw-semibold">{{ $schedule->start_time->format('H:i') }}</span>
                                                <i class="ph-duotone ph-arrow-left text-muted"></i>
                                                <span
                                                    class="fw-semibold">{{ $schedule->end_time->format('H:i') }}</span>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center gap-2">
                                                <i class="ph-duotone ph-books text-primary"></i>
                                                <span
                                                    class="fw-semibold">{{ $schedule->subject->subject_label ?? '—' }}</span>
                                            </div>
                                        </td>
                                        <td>
                                            <span
                                                class="text-muted small">{{ $schedule->subject->grade_label ?? '—' }}</span>
                                        </td>
                                        <td>
                                            @if($schedule->teacher)
                                                <div class="d-flex align-items-center gap-2">
                                                    <div class="student-avatar">
                                                        {{ mb_substr($schedule->teacher->name, 0, 1) }}
                                                    </div>
                                                    <div>
                                                        <span
                                                            class="fw-semibold d-block small">{{ $schedule->teacher->name }}</span>
                                                        <span
                                                            class="badge bg-{{ $schedule->teacher->role->color() }} bg-opacity-10 text-{{ $schedule->teacher->role->color() }}"
                                                            style="font-size: 10px;">
                                                            {{ $schedule->teacher->role->label() }}
                                                        </span>
                                                    </div>
                                                </div>
                                            @else
                                                <span class="text-muted small">—</span>
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
        @else
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-body text-center py-4">
                        <i class="ph-duotone ph-calendar-x fs-1 d-block text-muted mb-2"></i>
                        <p class="text-muted mb-0">لا توجد مواعيد مسجلة لهذه المجموعة</p>
                    </div>
                </div>
            </div>
        @endif

        {{-- Students Section --}}
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div
                    class="card-header bg-transparent border-bottom d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <h6 class="mb-0 fw-bold">
                        <i class="ph-duotone ph-student me-1"></i>
                        طلاب المجموعة
                        <span class="badge bg-primary bg-opacity-10 text-primary ms-2">
                            {{ $activeStudentsCount ?? $group->students->count() }}
                        </span>
                    </h6>
                    @if($canUpdate)
                        <button type="button" class="btn btn-sm btn-primary" wire:click="toggleAddStudent">
                            <i class="ph-duotone ph-{{ $showAddStudent ? 'x' : 'user-plus' }} me-1"></i>
                            {{ $showAddStudent ? 'إلغاء' : 'إضافة طلاب' }}
                        </button>
                    @endif
                </div>
                <div class="card-body">
                    @if($showAddStudent)
                        <div class="p-3 bg-light rounded-3 mb-4 border">
                            {{-- ✅ Filters Row --}}
                            <div class="row g-2 mb-3">
                                <div class="col-md-5">
                                    <label class="form-label small fw-semibold">ابحث</label>
                                    <div class="position-relative">
                                        <i class="ph-duotone ph-magnifying-glass position-absolute top-50 translate-middle-y ms-3 text-muted"></i>
                                        <input type="text" wire:model.live.debounce.300ms="searchStudent"
                                               class="form-control form-control-sm ps-5"
                                               placeholder="ابحث بالاسم، البريد، الهاتف...">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label small fw-semibold">الفرع</label>
                                    <select wire:model.live="searchBranch" class="form-select form-select-sm">
                                        <option value="">كل الفروع</option>
                                        @foreach($branches as $branch)
                                            <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label small fw-semibold">المرحلة الدراسية</label>
                                    <select wire:model.live="searchGrade" class="form-select form-select-sm">
                                        <option value="">كل المراحل</option>
                                        @foreach($grades as $value => $label)
                                            <option value="{{ $value }}">{{ $label }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-1">
                                    <label class="form-label small fw-semibold d-block">&nbsp;</label>
                                    <button type="button"
                                            wire:click="$set('searchStudent', ''); $set('searchBranch', ''); $set('searchGrade', '');"
                                            class="btn btn-light border btn-sm w-100" title="تفريغ الفلاتر">
                                        <i class="ph-duotone ph-funnel-simple"></i>
                                    </button>
                                </div>
                            </div>

                            {{-- ✅ Selection actions --}}
                            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-2">
                                <div class="d-flex align-items-center gap-2">
                                    <span class="badge bg-primary bg-opacity-10 text-primary">
                                        {{ count($selectedStudentIds) }} مختار
                                    </span>
                                    <small class="text-muted">
                                        ({{ $availableStudents->count() }} متاح)
                                    </small>
                                </div>
                                <div class="d-flex gap-2">
                                    <button type="button" wire:click="selectAllVisible"
                                            class="btn btn-sm btn-outline-success">
                                        <i class="ph-duotone ph-check-square me-1"></i>تحديد المعروض
                                    </button>
                                    <button type="button" wire:click="deselectAll"
                                            class="btn btn-sm btn-outline-danger">
                                        <i class="ph-duotone ph-x-square me-1"></i>إلغاء التحديد
                                    </button>
                                </div>
                            </div>

                            {{-- ✅ Students List --}}
                            @if($availableStudents->count() > 0)
                                <div class="row g-2 mb-3" style="max-height: 350px; overflow-y: auto;">
                                    @foreach($availableStudents as $student)
                                        @php $isSelected = in_array($student->id, $selectedStudentIds); @endphp
                                        <div class="col-md-4" wire:key="avail-student-{{ $student->id }}">
                                            <label class="permission-item {{ $isSelected ? 'is-selected' : '' }} h-100"
                                                   for="std-{{ $student->id }}">
                                                <input type="checkbox"
                                                       wire:click="toggleStudentSelection({{ $student->id }})"
                                                       id="std-{{ $student->id }}"
                                                       class="permission-checkbox"
                                                    {{ $isSelected ? 'checked' : '' }}>
                                                <div class="permission-content">
                                                    <span class="permission-icon">
                                                        <i class="ph-duotone ph-student"></i>
                                                    </span>
                                                    <span class="permission-label">
                                                        <span class="d-block fw-semibold">{{ $student->name }}</span>
                                                        <small class="text-muted d-block">{{ $student->email }}</small>
                                                    </span>
                                                    <span class="permission-check">
                                                        <i class="ph-duotone ph-check"></i>
                                                    </span>
                                                </div>
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center py-4 text-muted">
                                    <i class="ph-duotone ph-users fs-1 d-block mb-2"></i>
                                    <p class="mb-0 small">لا يوجد طلاب متاحين للإضافة</p>
                                </div>
                            @endif

                            {{-- ✅ Add Button --}}
                            <div class="d-flex justify-content-end gap-2 mt-2 pt-3 border-top">
                                <button type="button" class="btn btn-light border btn-sm" wire:click="toggleAddStudent">
                                    إلغاء
                                </button>
                                <button type="button"
                                        class="btn btn-success btn-sm"
                                        wire:click="addStudents"
                                        wire:loading.attr="disabled"
                                        wire:target="addStudents"
                                    {{ empty($selectedStudentIds) ? 'disabled' : '' }}>
                                    <span wire:loading.remove wire:target="addStudents">
                                        <i class="ph-duotone ph-check me-1"></i>
                                        إضافة المحددين ({{ count($selectedStudentIds) }})
                                    </span>
                                    <span wire:loading wire:target="addStudents">
                                        <span class="spinner-border spinner-border-sm me-1"></span>
                                        جاري الإضافة...
                                    </span>
                                </button>
                            </div>
                        </div>
                    @endif

                    {{-- ✅ Students Table --}}
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead>
                            <tr>
                                <th class="ps-4" style="width: 60px;">#</th>
                                <th>الطالب</th>
                                <th>البريد الإلكتروني</th>
                                <th>تاريخ الانضمام</th>
                                @if($canUpdate)
                                    <th class="text-center" style="width: 100px;">الإجراءات</th>
                                @endif
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($group->students as $index => $student)
                                <tr wire:key="student-{{ $student->id }}">
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
                                        <span class="text-muted small">
                                            <i class="ph-duotone ph-calendar me-1"></i>
                                            {{ $student->pivot->joined_at?->format('Y-m-d') ?? '—' }}
                                        </span>
                                    </td>
                                    @if($canUpdate)
                                        <td class="text-center">
                                            @if($deleteStudentId === $student->id)
                                                {{-- ✅ Confirmation: ✓ / ✗ --}}
                                                <div class="d-flex align-items-center justify-content-center gap-3">
                                                    <button type="button"
                                                            wire:click="removeStudent"
                                                            wire:loading.attr="disabled"
                                                            wire:target="removeStudent"
                                                            class="border-0 bg-transparent p-0 text-success"
                                                            title="تأكيد الإزالة">
                                                        <span wire:loading.remove wire:target="removeStudent">
                                                            <i class="ph-duotone ph-check-circle fs-4"></i>
                                                        </span>
                                                        <span wire:loading wire:target="removeStudent"
                                                              class="spinner-border spinner-border-sm"></span>
                                                    </button>
                                                    <button type="button"
                                                            wire:click="cancelRemoveStudent"
                                                            class="border-0 bg-transparent p-0 text-secondary"
                                                            title="إلغاء">
                                                        <i class="ph-duotone ph-x-circle fs-4"></i>
                                                    </button>
                                                </div>
                                            @else
                                                <button type="button"
                                                        wire:click="confirmRemoveStudent({{ $student->id }})"
                                                        class="border-0 bg-transparent p-0 text-danger"
                                                        title="إزالة">
                                                    <i class="ph-duotone ph-user-minus fs-4"></i>
                                                </button>
                                            @endif
                                        </td>
                                    @endif
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="{{ $canUpdate ? 5 : 4 }}" class="text-center text-muted py-5">
                                        <i class="ph-duotone ph-users fs-1 d-block mb-2"></i>
                                        <h6 class="mb-1">لا يوجد طلاب في هذه المجموعة</h6>
                                        <small>اضغط على "إضافة طلاب" لإضافة أول طالب</small>
                                    </td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
