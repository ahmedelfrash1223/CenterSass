<div>
    <!-- List Header -->
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <p class="text-muted mb-0 small">إدارة مجموعاتك ومواعيدها وطلابها</p>
        </div>
        @if($canCreate)
            @if($limit['can'])
                <a href="{{ route('teacher.groups.create') }}" wire:navigate
                   class="btn btn-primary d-flex align-items-center gap-1">
                    <i class="ph-duotone ph-plus"></i>
                    إضافة مجموعة
                </a>
            @else
                <button type="button" class="btn btn-secondary d-flex align-items-center gap-1" disabled
                        title="{{ $limit['message'] }}">
                    <i class="ph-duotone ph-lock"></i>
                    إضافة مجموعة
                </button>
            @endif
        @endif
    </div>

    <!-- Subscription Limit Bar -->
    @if($isOwner && $limit['max'] !== null)
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between mb-2 flex-wrap gap-2">
                    <div class="d-flex align-items-center gap-2">
                        <i class="ph-duotone ph-crown text-warning fs-5"></i>
                        <span class="fw-semibold">حد المجموعات في اشتراكك</span>
                    </div>
                    <span
                        class="badge bg-{{ $limit['can'] ? 'success' : 'danger' }} bg-opacity-10 text-{{ $limit['can'] ? 'success' : 'danger' }}">
                        {{ $limit['current'] }} / {{ $limit['max'] }}
                    </span>
                </div>
                @php
                    $percentage = $limit['max'] > 0 ? ($limit['current'] / $limit['max']) * 100 : 0;
                    $barColor = $percentage >= 100 ? 'danger' : ($percentage >= 80 ? 'warning' : 'success');
                @endphp
                <div class="progress" style="height: 8px;">
                    <div class="progress-bar bg-{{ $barColor }}"
                         style="width: {{ min(100, $percentage) }}%"></div>
                </div>
                @if(!$limit['can'])
                    <small class="text-danger d-block mt-2">
                        <i class="ph-duotone ph-warning-circle me-1"></i>
                        {{ $limit['message'] }}
                    </small>
                @elseif($limit['max'] - $limit['current'] <= 2 && $limit['max'] > 0)
                    <small class="text-warning d-block mt-2">
                        <i class="ph-duotone ph-warning me-1"></i>
                        باقي لك {{ $limit['max'] - $limit['current'] }} مجموعة فقط
                    </small>
                @endif
            </div>
        </div>
    @endif

    @livewire('teacher-panel.parts.flash-message', [
        'successKey' => 'group-success',
        'errorKey' => 'group-error',
        'warningKey' => 'group-warning',
    ])

    <!-- Search and Filters -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <div class="row g-2">
                <div class="col-md-5">
                    <div class="position-relative">
                        <i class="ph-duotone ph-magnifying-glass position-absolute top-50 translate-middle-y ms-3 text-muted"></i>
                        <input type="text" wire:model.live.debounce.400ms="search" class="form-control ps-5"
                               placeholder="ابحث عن مجموعة...">
                    </div>
                </div>
                <div class="col-md-3">
                    <select wire:model.live="branchFilter" class="form-select">
                        <option value="">كل الفروع</option>
                        @foreach($branches as $branch)
                            <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <select wire:model.live="statusFilter" class="form-select">
                        <option value="">كل الحالات</option>
                        @foreach($statuses as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
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

    <!-- Groups Table -->
    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                <tr>
                    <th class="ps-4">#</th>
                    <th>المجموعة</th>
                    <th>الفرع</th>
                    <th>الطلاب</th>
                    <th>أضيف بواسطة</th>
                    <th>الحالة</th>
                    <th class="text-center">الإجراءات</th>
                </tr>
                </thead>
                <tbody>
                @forelse($groups as $group)
                    <tr wire:key="group-{{ $group->id }}">
                        <td class="ps-4">
                            <span class="text-muted fw-semibold">{{ $groups->firstItem() + $loop->index }}</span>
                        </td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <div class="avtar avtar-s bg-light-primary text-primary">
                                    <i class="ph-duotone ph-users-three fs-5"></i>
                                </div>
                                <span class="fw-semibold">{{ $group->name }}</span>
                            </div>
                        </td>
                        <td>{{ $group->branch?->name ?? '—' }}</td>
                        <td>
                            <i class="ph-duotone ph-users me-1 text-primary"></i>
                            {{ $group->students_count ?? 0 }}
                            @if($group->max_students > 0)
                                <small class="text-muted">/ {{ $group->max_students }}</small>
                            @endif
                        </td>

                        <td>
                            @if($group->creator)
                                <span
                                    class="badge bg-{{ $group->creator->role->color() }} bg-opacity-10 text-{{ $group->creator->role->color() }}">
                                    <i class="ph-duotone {{ $group->creator->role->icon() }} me-1"></i>
                                    {{ $group->creator->name }}
                                </span>
                            @else
                                <span class="text-muted small">—</span>
                            @endif
                        </td>

                        <td>
                            <span
                                class="badge bg-{{ $group->status_color }} bg-opacity-10 text-{{ $group->status_color }}">
                                {{ $group->status_label }}
                            </span>
                        </td>
                        <td>
                            <div class="d-flex align-items-center justify-content-center gap-3">
                                @if($deleteId === $group->id)
                                    <button type="button" wire:click="delete" wire:loading.attr="disabled"
                                            wire:target="delete" class="border-0 bg-transparent p-0 text-success"
                                            title="تأكيد الحذف">
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
                                    <a href="{{ route('teacher.groups.show', $group) }}" wire:navigate
                                       class="border-0 bg-transparent p-0 text-info" title="عرض">
                                        <i class="ph-duotone ph-eye fs-4"></i>
                                    </a>

                                    @if($canUpdate)
                                        <a href="{{ route('teacher.groups.edit', $group) }}" wire:navigate
                                           class="border-0 bg-transparent p-0 text-primary" title="تعديل">
                                            <i class="ph-duotone ph-pencil-simple fs-4"></i>
                                        </a>
                                    @else
                                        <span class="text-muted opacity-50" title="ليس لديك صلاحية"
                                              style="cursor: not-allowed;">
                                            <i class="ph-duotone ph-pencil-simple fs-4"></i>
                                        </span>
                                    @endif

                                    @if($canDelete)
                                        <button type="button" wire:click="confirmDelete({{ $group->id }})"
                                                class="border-0 bg-transparent p-0 text-danger" title="حذف">
                                            <i class="ph-duotone ph-trash fs-4"></i>
                                        </button>
                                    @else
                                        <span class="text-muted opacity-50" title="ليس لديك صلاحية"
                                              style="cursor: not-allowed;">
                                            <i class="ph-duotone ph-trash fs-4"></i>
                                        </span>
                                    @endif
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center py-5">
                            <div class="text-muted">
                                <i class="ph-duotone ph-users-three fs-1 d-block mb-2"></i>
                                <h6>{{ $search || $branchFilter || $statusFilter ? 'لا توجد نتائج' : 'لا توجد مجموعات حتى الآن' }}</h6>
                                <small>ابدأ بإضافة أول مجموعة.</small>
                            </div>
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        @if($groups->hasPages())
            <div class="card-footer bg-transparent border-top">
                {{ $groups->links() }}
            </div>
        @endif
    </div>
</div>
