<div>
    {{-- Filters Bar --}}
    <div class="close-day-filters mb-4">
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

            {{-- Branch --}}
            <div class="col-md-3">
                <label class="filter-label">
                    <i class="ph-duotone ph-git-branch me-1"></i>
                    الفرع
                </label>
                <select wire:model.live="branchFilter" class="form-select form-select-lg filter-input">
                    <option value="">كل الفروع</option>
                    @foreach($branches as $branch)
                        <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Close All Button --}}
            <div class="col-md-6">
                <button type="button"
                        wire:click="confirmCloseAll"
                        class="btn btn-close-day w-100">
                    <span class="btn-close-day-icon">
                        <i class="ph-duotone ph-lock-key"></i>
                    </span>
                    <span class="btn-close-day-text">
                        <span class="btn-close-day-title">تقفيل اليوم كامل</span>
                        <span class="btn-close-day-subtitle">
                            تسجيل غياب لكل الطلاب اللي لم يحضروا
                        </span>
                    </span>
                </button>
            </div>

        </div>
    </div>

    {{-- Overall Stats --}}
    <div class="row g-3 mb-4">
        <div class="col-md-6 col-lg-3">
            <div class="card border-0 shadow-sm stat-card-hover h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <div class="stat-icon-box bg-primary bg-opacity-10 text-primary">
                            <i class="ph-duotone ph-student"></i>
                        </div>
                    </div>
                    <h3 class="mb-1 fw-bold">{{ number_format($overall['total']) }}</h3>
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
                    <h3 class="mb-1 fw-bold text-success">{{ number_format($overall['present']) }}</h3>
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
                    <h3 class="mb-1 fw-bold text-warning">{{ number_format($overall['late']) }}</h3>
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
                    <h3 class="mb-1 fw-bold text-danger">{{ number_format($overall['absent']) }}</h3>
                    <p class="text-muted mb-0 small">الغائبون</p>
                </div>
            </div>
        </div>
    </div>

    @livewire('teacher-panel.parts.flash-message', [
        'successKey' => 'attendance-close-success',
        'errorKey' => 'attendance-close-error',
    ])

    {{-- Groups List --}}
    <div class="card border-0 shadow-sm">
        <div class="card-header close-day-header">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 w-100">
                <div class="d-flex align-items-center gap-2">
                    <i class="ph-duotone ph-users-three fs-4 text-primary"></i>
                    <h6 class="mb-0 fw-bold">
                        المجموعات
                        <span class="badge bg-primary bg-opacity-10 text-primary ms-2">
                            {{ $groups->count() }}
                        </span>
                    </h6>
                </div>

                <button type="button"
                        wire:click="confirmCloseSelected"
                        class="btn btn-close-selected"
                    {{ empty($selectedGroupIds) ? 'disabled' : '' }}>
                    <i class="ph-duotone ph-lock me-1"></i>
                    تقفيل المختارة
                    @if(count($selectedGroupIds) > 0)
                        <span class="badge-count">{{ count($selectedGroupIds) }}</span>
                    @endif
                </button>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                    <tr>
                        <th class="ps-4" style="width: 60px;">
                            <input type="checkbox"
                                   class="group-checkbox form-check-input"
                                   id="select-all-groups"
                                   @if($groups->isNotEmpty() && count($selectedGroupIds) === $groups->count()) checked
                                   @endif
                                   wire:click="{{ count($selectedGroupIds) === $groups->count() && $groups->isNotEmpty() ? 'deselectAllGroups' : 'selectAllGroups' }}">
                        </th>
                        <th>المجموعة</th>
                        <th>الفرع</th>
                        <th class="text-center">الطلاب</th>
                        <th class="text-center">الحضور</th>
                        <th class="text-center">التأخير</th>
                        <th class="text-center">الغياب</th>
                        <th class="text-center">إجراءات</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($groups as $group)
                        <tr wire:key="group-{{ $group->id }}"
                            class="{{ in_array((string)$group->id, $selectedGroupIds) ? 'row-selected' : '' }}">
                            <td class="ps-4">
                                <input type="checkbox"
                                       class="group-checkbox form-check-input"
                                       wire:model.live="selectedGroupIds"
                                       value="{{ (string) $group->id }}">
                            </td>
                            <td>
                                <span class="fw-semibold">{{ $group->name }}</span>
                            </td>
                            <td>
                                <small class="text-muted">
                                    <i class="ph-duotone ph-git-branch me-1"></i>
                                    {{ $group->branch?->name ?? '—' }}
                                </small>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-secondary bg-opacity-10 text-secondary">
                                    {{ $group->stats['total_students'] }}
                                </span>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-success bg-opacity-10 text-success">
                                    {{ $group->stats['present'] }}
                                </span>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-warning bg-opacity-10 text-warning">
                                    {{ $group->stats['late'] }}
                                </span>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-danger bg-opacity-10 text-danger">
                                    {{ $group->stats['absent'] }}
                                </span>
                            </td>
                            <td class="text-center">
                                <a href="{{ route('teacher.attendance.close-day.show', ['group' => $group->id, 'date' => $date]) }}"
                                   wire:navigate
                                   class="btn btn-sm btn-light border">
                                    <i class="ph-duotone ph-eye me-1"></i>
                                    التفاصيل
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-5">
                                <i class="ph-duotone ph-users-three fs-1 d-block mb-2 text-muted"></i>
                                <h6 class="mb-1">لا توجد مجموعات</h6>
                                <small class="text-muted">أضف مجموعات أولاً.</small>
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Confirmation Modal --}}
    @if($confirmingCloseSelected || $confirmingCloseAll)
        <div class="close-day-modal-backdrop" wire:click="cancelClose"></div>
        <div class="close-day-modal">
            <div class="close-day-modal-content">
                <div class="close-day-modal-icon">
                    <i class="ph-duotone ph-warning-circle"></i>
                </div>

                <h4 class="close-day-modal-title">
                    {{ $confirmingCloseAll ? 'تقفيل اليوم كامل' : 'تقفيل المجموعات المختارة' }}
                </h4>

                <p class="close-day-modal-text">
                    @if($confirmingCloseAll)
                        هل أنت متأكد من تقفيل <strong>كل مجموعات اليوم</strong>؟
                        سيتم تسجيل غياب لكل الطالب اللي لم يحضروا.
                    @else
                        هل أنت متأكد من تقفيل <strong>{{ count($selectedGroupIds) }} مجموعة</strong>؟
                        سيتم تسجيل غياب لكل الطالب اللي لم يحضروا.
                    @endif
                </p>

                <div class="close-day-modal-warning">
                    <i class="ph-duotone ph-info"></i>
                    <span>يمكنك فتح الحصة بعد التقفيل من صفحة التفاصيل.</span>
                </div>

                <div class="close-day-modal-actions">
                    <button type="button"
                            wire:click="cancelClose"
                            class="btn btn-modal-cancel">
                        <i class="ph-duotone ph-x me-1"></i>
                        إلغاء
                    </button>

                    @if($confirmingCloseAll)
                        <button type="button"
                                wire:click="closeAllGroups"
                                wire:loading.attr="disabled"
                                class="btn btn-modal-confirm">
                            <span wire:loading.remove wire:target="closeAllGroups">
                                <i class="ph-duotone ph-lock me-1"></i>
                                نعم، قفّل الكل
                            </span>
                            <span wire:loading wire:target="closeAllGroups">
                                <span class="spinner-border spinner-border-sm me-1"></span>
                                جاري التقفيل...
                            </span>
                        </button>
                    @else
                        <button type="button"
                                wire:click="closeSelected"
                                wire:loading.attr="disabled"
                                class="btn btn-modal-confirm">
                            <span wire:loading.remove wire:target="closeSelected">
                                <i class="ph-duotone ph-lock me-1"></i>
                                نعم، قفّل المختارة
                            </span>
                            <span wire:loading wire:target="closeSelected">
                                <span class="spinner-border spinner-border-sm me-1"></span>
                                جاري التقفيل...
                            </span>
                        </button>
                    @endif
                </div>
            </div>
        </div>
    @endif
</div>
