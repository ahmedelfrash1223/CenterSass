<div>
    <!-- List Header -->
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <p class="text-muted mb-0 small">إدارة أفرعك وعناوينها وبيانات التواصل</p>
        </div>

        @if($canCreate)
            @if($limit['can'])
                <a href="{{ route('teacher.branches.create') }}" wire:navigate class="btn btn-primary d-flex align-items-center gap-1">
                    <i class="ph-duotone ph-plus"></i>
                    إضافة فرع
                </a>
            @else
                <button type="button" class="btn btn-secondary d-flex align-items-center gap-1" disabled
                        title="{{ $limit['message'] }}">
                    <i class="ph-duotone ph-lock"></i>
                    إضافة فرع
                </button>
            @endif
        @endif
    </div>

    <!-- Subscription Limit Bar -->
    @if($limit['max'] !== null)
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between mb-2 flex-wrap gap-2">
                    <div class="d-flex align-items-center gap-2">
                        <i class="ph-duotone ph-crown text-warning fs-5"></i>
                        <span class="fw-semibold">حد الفروع في اشتراكك</span>
                    </div>
                    <span class="badge bg-{{ $limit['can'] ? 'success' : 'danger' }} bg-opacity-10 text-{{ $limit['can'] ? 'success' : 'danger' }}">
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
                @elseif($limit['max'] - $limit['current'] <= 2)
                    <small class="text-warning d-block mt-2">
                        <i class="ph-duotone ph-warning me-1"></i>
                        باقي لك {{ $limit['max'] - $limit['current'] }} فرع فقط
                    </small>
                @endif
            </div>
        </div>
    @endif

    <!-- Feedback -->
    @livewire('teacher-panel.parts.flash-message', [
        'successKey' => 'branch-success',
        'errorKey' => 'branch-error',
        'warningKey' => 'branch-warning',
    ])

    <!-- Filters -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <div class="row g-2">
                <div class="col-md-5">
                    <div class="position-relative">
                        <i class="ph-duotone ph-magnifying-glass position-absolute top-50 translate-middle-y ms-3 text-muted"></i>
                        <input type="text" wire:model.live.debounce.400ms="search" class="form-control ps-5" placeholder="ابحث بالاسم، العنوان، الهاتف...">
                    </div>
                </div>
                <div class="col-md-3">
                    <select wire:model.live="status" class="form-select">
                        <option value="">كل الحالات</option>
                        <option value="1">نشط</option>
                        <option value="0">غير نشط</option>
                    </select>
                </div>
                <div class="col-md-1">
                    <button type="button" class="btn btn-light border w-100" wire:click="$set('status', ''); $set('search', '');">
                        <i class="ph-duotone ph-funnel-simple"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Branch Table -->
    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                <tr>
                    <th class="ps-4">#</th>
                    <th>الاسم</th>
                    <th>العنوان</th>
                    <th>الهاتف</th>
                    <th>البريد الإلكتروني</th>
                    <th>أضيف بواسطة</th>
                    <th>الحالة</th>
                    <th class="text-center">الإجراءات</th>
                </tr>
                </thead>
                <tbody>
                @forelse($branches as $branch)
                    <tr wire:key="branch-{{ $branch->id }}">
                        <td class="ps-4">
                            <span class="text-muted fw-semibold">{{ $branches->firstItem() + $loop->index }}</span>
                        </td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <div class="avtar avtar-s bg-light-primary text-primary">
                                    <i class="ph-duotone ph-buildings fs-5"></i>
                                </div>
                                <span class="fw-semibold">{{ $branch->name }}</span>
                            </div>
                        </td>
                        <td>{{ $branch->address }}</td>
                        <td>
                            @if($branch->phone)
                                <a href="tel:{{ $branch->phone }}" class="text-decoration-none small">{{ $branch->phone }}</a>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td>
                            @if($branch->email)
                                <a href="mailto:{{ $branch->email }}" class="text-decoration-none small">{{ $branch->email }}</a>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>

                        <td>
                            @if($branch->creator)
                                <div class="d-flex align-items-center gap-1">
                                    <span class="badge bg-{{ $branch->creator->role->color() }} bg-opacity-10 text-{{ $branch->creator->role->color() }}">
                                        <i class="ph-duotone {{ $branch->creator->role->icon() }} me-1"></i>
                                        {{ $branch->creator->name }}
                                    </span>
                                </div>
                            @else
                                <span class="text-muted small">—</span>
                            @endif
                        </td>

                        <td>
                            @if($branch->is_active)
                                <span class="badge bg-success bg-opacity-10 text-success">
                                        <i class="ph-duotone ph-check-circle me-1"></i>نشط
                                    </span>
                            @else
                                <span class="badge bg-danger bg-opacity-10 text-danger">
                                        <i class="ph-duotone ph-x-circle me-1"></i>غير نشط
                                    </span>
                            @endif
                        </td>
                        <td>
                            <div class="d-flex align-items-center justify-content-center gap-3">
                                @if($deleteId === $branch->id)
                                    <button type="button" wire:click="delete" wire:loading.attr="disabled" wire:target="delete" class="border-0 bg-transparent p-0 text-success" title="تأكيد الحذف">
                                            <span wire:loading.remove wire:target="delete">
                                                <i class="ph-duotone ph-check-circle fs-4"></i>
                                            </span>
                                        <span wire:loading wire:target="delete" class="spinner-border spinner-border-sm"></span>
                                    </button>
                                    <button type="button" wire:click="cancelDelete" class="border-0 bg-transparent p-0 text-secondary" title="إلغاء">
                                        <i class="ph-duotone ph-x-circle fs-4"></i>
                                    </button>
                                @else
                                    @if($canUpdate)
                                        <a href="{{ route('teacher.branches.edit', $branch) }}" wire:navigate class="border-0 bg-transparent p-0 text-primary" title="تعديل">
                                            <i class="ph-duotone ph-pencil-simple fs-4"></i>
                                        </a>
                                    @else
                                        <span class="text-muted opacity-50" title="ليس لديك صلاحية" style="cursor: not-allowed;">
                                                <i class="ph-duotone ph-pencil-simple fs-4"></i>
                                            </span>
                                    @endif

                                    @if($canDelete)
                                        <button type="button" wire:click="confirmDelete({{ $branch->id }})" class="border-0 bg-transparent p-0 text-danger" title="حذف">
                                            <i class="ph-duotone ph-trash fs-4"></i>
                                        </button>
                                    @else
                                        <span class="text-muted opacity-50" title="ليس لديك صلاحية" style="cursor: not-allowed;">
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
                                <i class="ph-duotone ph-buildings fs-1 d-block mb-2"></i>
                                <h6>{{ $search || $status !== '' ? 'لا توجد نتائج' : 'لا توجد أفرع حتى الآن' }}</h6>
                                @if(!$search && $status === '')
                                    <small>ابدأ بإضافة أول فرع.</small>
                                @endif
                            </div>
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        @if($branches->hasPages())
            <div class="card-footer bg-transparent border-top">
                {{ $branches->links() }}
            </div>
        @endif
    </div>
</div>
