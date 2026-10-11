<div>
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <p class="text-muted mb-0 small">إدارة متابعات الطلاب (واجبات / تسميع / تقييمات)</p>
        </div>
        @if($canCreate)
            <a href="{{ route('teacher.assignments.create') }}" wire:navigate
                class="btn btn-primary d-flex align-items-center gap-1">
                <i class="ph-duotone ph-plus-circle"></i>
                إضافة متابعة
            </a>
        @endif
    </div>

    @livewire('teacher-panel.parts.flash-message', [
        'successKey' => 'assignment-success',
        'errorKey' => 'assignment-error',
    ])

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <div class="row g-2">
                <div class="col-md-3">
                    <div class="position-relative">
                        <i
                            class="ph-duotone ph-magnifying-glass position-absolute top-50 translate-middle-y ms-3 text-muted"></i>
                        <input type="text" wire:model.live.debounce.400ms="search" class="form-control ps-5"
                            placeholder="ابحث بالعنوان...">
                    </div>
                </div>
                <div class="col-md-2">
                    <select wire:model.live="typeFilter" class="form-select">
                        <option value="">كل الأنواع</option>
                        @foreach($types as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <select wire:model.live="statusFilter" class="form-select">
                        <option value="">كل الحالات</option>
                        @foreach($statuses as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <select wire:model.live="branchFilter" class="form-select">
                        <option value="">كل الفروع</option>
                        @foreach($branches as $branch)
                            <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <select wire:model.live="groupFilter" class="form-select">
                        <option value="">كل المجموعات</option>
                        @foreach($groups as $group)
                            <option value="{{ $group->id }}">{{ $group->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-1">
                    <button type="button" class="btn btn-light border w-100" wire:click="clearFilters">
                        <i class="ph-duotone ph-funnel-simple"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th class="ps-4">#</th>
                        <th>العنوان</th>
                        <th>النوع</th>
                        <th>المجموعة</th>
                        <th>الدرجة</th>
                        <th>الحالة</th>
                        <th>الطلاب</th>
                        <th class="text-center">الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($assignments as $assignment)
                        <tr wire:key="assignment-{{ $assignment->id }}">
                            <td class="ps-4">
                                <span class="text-muted fw-semibold">{{ $assignments->firstItem() + $loop->index }}</span>
                            </td>
                            <td>
                                <h6 class="mb-0 fw-semibold">{{ $assignment->title }}</h6>
                                @if($assignment->due_date)
                                    <small class="text-muted">
                                        <i class="ph-duotone ph-calendar me-1"></i>
                                        {{ $assignment->due_date->format('Y-m-d') }}
                                    </small>
                                @endif
                            </td>
                            <td>
                                <span
                                    class="badge bg-{{ $assignment->type->color() }} bg-opacity-10 text-{{ $assignment->type->color() }}">
                                    <i class="ph-duotone {{ $assignment->type->icon() }} me-1"></i>
                                    {{ $assignment->type->label() }}
                                </span>
                            </td>
                            <td>
                                @if($assignment->group)
                                    <span class="badge bg-primary bg-opacity-10 text-primary">
                                        {{ $assignment->group->name }}
                                    </span>
                                @else
                                    <span class="text-muted small">—</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-info bg-opacity-10 text-info">
                                    {{ $assignment->max_score }}
                                </span>
                            </td>
                            <td>
                                <span
                                    class="badge bg-{{ $assignment->status->color() }} bg-opacity-10 text-{{ $assignment->status->color() }}">
                                    {{ $assignment->status->label() }}
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-secondary bg-opacity-10 text-secondary">
                                    {{ $assignment->students_count }}
                                </span>
                            </td>
                            <td>
                                <div class="d-flex align-items-center justify-content-center gap-3">
                                    @if($deleteId === $assignment->id)
                                        <button type="button" wire:click="delete"
                                            class="border-0 bg-transparent p-0 text-success" title="تأكيد">
                                            <span wire:loading.remove wire:target="delete">
                                                <i class="ph-duotone ph-check-circle fs-4"></i>
                                            </span>
                                            <span wire:loading wire:target="delete"
                                                class="spinner-border spinner-border-sm"></span>
                                        </button>
                                        <button type="button" wire:click="cancelDelete"
                                            class="border-0 bg-transparent p-0 text-secondary" title="إلغاء">
                                            <i class="ph-duotone ph-x-circle fs-4"></i>
                                        </button>
                                    @else
                                        @if($canView)
                                            <a wire:navigate href="{{ route('teacher.assignments.show', $assignment->id) }}"
                                                class="border-0 bg-transparent p-0 text-info" title="عرض">
                                                <i class="ph-duotone ph-eye fs-4"></i>
                                            </a>
                                        @endif

                                        @if($canUpdate)
                                            <a wire:navigate href="{{ route('teacher.assignments.edit', $assignment->id) }}"
                                                class="border-0 bg-transparent p-0 text-primary" title="تعديل">
                                                <i class="ph-duotone ph-pencil-simple fs-4"></i>
                                            </a>
                                        @endif

                                        @if($canDelete)
                                            <button type="button" wire:click="confirmDelete({{ $assignment->id }})"
                                                class="border-0 bg-transparent p-0 text-danger" title="حذف">
                                                <i class="ph-duotone ph-trash fs-4"></i>
                                            </button>
                                        @endif
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-5">
                                <div class="text-muted">
                                    <i class="ph-duotone ph-check-square fs-1 d-block mb-2"></i>
                                    <h6 class="mb-1">
                                        {{ $search || $typeFilter || $statusFilter || $branchFilter || $groupFilter ? 'لا توجد نتائج' : 'لا توجد متابعات' }}
                                    </h6>
                                    <small>ابدأ بإضافة أول متابعة.</small>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($assignments->hasPages())
            <div class="card-footer bg-transparent border-top">
                {{ $assignments->links() }}
            </div>
        @endif
    </div>
</div>
