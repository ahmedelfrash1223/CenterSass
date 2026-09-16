<div>
    <!-- List Header -->
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <p class="text-muted mb-0 small">إدارة الفريق (المدرسين والمساعدين) التابعين لحسابك</p>
        </div>

        @if($canCreate)
            @if($limit['can'])
                <a href="{{ route('teacher.staff.create') }}" wire:navigate
                   class="btn btn-primary d-flex align-items-center gap-1">
                    <i class="ph-duotone ph-user-plus"></i>
                    إضافة عضو
                </a>
            @else
                <button type="button" class="btn btn-secondary d-flex align-items-center gap-1" disabled
                        title="{{ $limit['message'] }}">
                    <i class="ph-duotone ph-lock"></i>
                    إضافة عضو
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
                        <span class="fw-semibold">حد أعضاء الفريق في اشتراكك</span>
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
                        باقي لك {{ $limit['max'] - $limit['current'] }} عضو فقط
                    </small>
                @endif
            </div>
        </div>
    @endif

    <!-- Feedback -->
    @livewire('teacher-panel.parts.flash-message', [
        'successKey' => 'staff-success',
        'errorKey' => 'staff-error',
        'warningKey' => 'staff-warning',
    ])

    <!-- Filters -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <div class="row g-2">
                <div class="col-md-4">
                    <div class="position-relative">
                        <i class="ph-duotone ph-magnifying-glass position-absolute top-50 translate-middle-y ms-3 text-muted"></i>
                        <input type="text" wire:model.live.debounce.400ms="search" class="form-control ps-5"
                               placeholder="ابحث بالاسم، البريد، اسم المستخدم...">
                    </div>
                </div>
                <div class="col-md-2">
                    <select wire:model.live="roleFilter" class="form-select">
                        <option value="">كل الأنواع</option>
                        @foreach($roles as $value => $label)
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
                    <select wire:model.live="statusFilter" class="form-select">
                        <option value="">كل الحالات</option>
                        <option value="1">نشط</option>
                        <option value="0">غير نشط</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="button" class="btn btn-light border w-100" wire:click="clearFilters">
                        <i class="ph-duotone ph-funnel-simple me-1"></i>تفريغ
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Staff Table -->
    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                <tr>
                    <th class="ps-4">#</th>
                    <th>العضو</th>
                    <th>النوع</th>
                    <th>الفروع</th>
                    <th>أضيف بواسطة</th>
                    <th>الحالة</th>
                    <th class="text-center">الإجراءات</th>
                </tr>
                </thead>
                <tbody>
                @forelse($members as $member)
                    <tr wire:key="staff-{{ $member->id }}">
                        <td class="ps-4">
                            <span class="text-muted fw-semibold">{{ $members->firstItem() + $loop->index }}</span>
                        </td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <div
                                    class="avtar avtar-s bg-{{ $member->role->color() }} bg-opacity-10 text-{{ $member->role->color() }}">
                                    <i class="ph-duotone {{ $member->role->icon() }} fs-5"></i>
                                </div>
                                <div>
                                    <h6 class="mb-0 fw-semibold">
                                        {{ $member->name }}
                                        @if($member->id === $currentTeacher->id)
                                            <span class="badge bg-success bg-opacity-10 text-success ms-1">أنت</span>
                                        @endif
                                    </h6>
                                    <small class="text-muted">{{ $member->code }}</small>
                                    <small class="text-muted d-block">
                                        <i class="ph-duotone ph-envelope me-1"></i>{{ $member->email }}
                                    </small>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span
                                class="badge bg-{{ $member->role->color() }} bg-opacity-10 text-{{ $member->role->color() }}">
                                <i class="ph-duotone {{ $member->role->icon() }} me-1"></i>
                                {{ $member->role->label() }}
                            </span>
                        </td>

                        <td>
                            @if($member->isOwner())
                                <span class="badge bg-warning bg-opacity-10 text-warning">
                                    <i class="ph-duotone ph-crown me-1"></i>
                                    كل الفروع
                                </span>
                            @elseif($member->branches->count() > 0)
                                <div class="d-flex flex-wrap gap-1" style="max-width: 250px;">
                                    @foreach($member->branches->take(2) as $branch)
                                        <span class="badge bg-primary bg-opacity-10 text-primary">
                                            <i class="ph-duotone ph-git-branch me-1"></i>
                                            {{ $branch->name }}
                                        </span>
                                    @endforeach
                                    @if($member->branches->count() > 2)
                                        <span class="badge bg-secondary bg-opacity-10 text-secondary"
                                              title="{{ $member->branches->skip(2)->pluck('name')->join(', ') }}">
                                            +{{ $member->branches->count() - 2 }}
                                        </span>
                                    @endif
                                </div>
                            @else
                                <span class="text-muted small">
                                    <i class="ph-duotone ph-minus-circle me-1"></i>
                                    بدون فروع
                                </span>
                            @endif
                        </td>

                        <td>
                            @if($member->creator)
                                <div class="d-flex align-items-center gap-1">
                                    <span
                                        class="badge bg-{{ $member->creator->role->color() }} bg-opacity-10 text-{{ $member->creator->role->color() }}">
                                        <i class="ph-duotone {{ $member->creator->role->icon() }} me-1"></i>
                                        {{ $member->creator->name }}
                                    </span>
                                </div>
                            @else
                                <span class="text-muted small">—</span>
                            @endif
                        </td>

                        <td>
                            @if($member->is_active)
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
                        </td>
                        <td>
                            <div class="d-flex align-items-center justify-content-center gap-3">
                                @if($deleteId === $member->id)
                                    <button type="button" wire:click="delete"
                                            class="border-0 bg-transparent p-0 text-success" title="تأكيد الحذف"
                                            wire:loading.attr="disabled">
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
                                        <a wire:navigate href="{{ route('teacher.staff.show', $member->id) }}"
                                           class="border-0 bg-transparent p-0 text-info" title="عرض">
                                            <i class="ph-duotone ph-eye fs-4"></i>
                                        </a>
                                    @else
                                        <span class="text-muted opacity-50" title="ليس لديك صلاحية"
                                              style="cursor: not-allowed;">
                                            <i class="ph-duotone ph-eye fs-4"></i>
                                        </span>
                                    @endif

                                    @if($member->id !== $ownerId)
                                        @if($canUpdate)
                                            <a wire:navigate href="{{ route('teacher.staff.edit', $member->id) }}"
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
                                            <button type="button" wire:click="confirmDelete({{ $member->id }})"
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
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center py-5">
                            <div class="text-muted">
                                <i class="ph-duotone ph-users fs-1 d-block mb-2"></i>
                                <h6 class="mb-1">
                                    {{ $search || $roleFilter || $statusFilter || $branchFilter ? 'لا توجد نتائج' : 'لا يوجد أعضاء في الفريق' }}
                                </h6>
                                <small>
                                    {{ $search || $roleFilter || $statusFilter || $branchFilter ? 'لم يتم العثور على نتائج مطابقة.' : 'ابدأ بإضافة أول عضو للفريق.' }}
                                </small>
                            </div>
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        @if($members->hasPages())
            <div class="card-footer bg-transparent border-top">
                {{ $members->links() }}
            </div>
        @endif
    </div>
</div>
