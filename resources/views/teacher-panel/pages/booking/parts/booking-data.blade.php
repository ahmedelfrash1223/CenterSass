<div>
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <p class="text-muted mb-0 small">إدارة حجوزات الطلاب الجدد</p>
        </div>

        @if($canCreate)
            <a href="{{ route('teacher.bookings.create') }}" wire:navigate
                class="btn btn-primary d-flex align-items-center gap-1">
                <i class="ph-duotone ph-plus-circle"></i>
                إضافة حجز
            </a>
        @endif
    </div>

    @livewire('teacher-panel.parts.flash-message', [
        'successKey' => 'booking-success',
        'errorKey' => 'booking-error',
    ])

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <div class="row g-2">
                <div class="col-md-5">
                    <div class="position-relative">
                        <i
                            class="ph-duotone ph-magnifying-glass position-absolute top-50 translate-middle-y ms-3 text-muted"></i>
                        <input type="text" wire:model.live.debounce.400ms="search" class="form-control ps-5"
                            placeholder="ابحث بالاسم أو الهاتف...">
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
                <div class="col-md-2">
                    <select wire:model.live="statusFilter" class="form-select">
                        <option value="">كل الحالات</option>
                        <option value="pending">قيد المراجعة</option>
                        <option value="confirmed">مؤكد</option>
                        <option value="cancelled">ملغي</option>
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

    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th class="ps-4">#</th>
                        <th>الطالب</th>
                        <th>المرحلة</th>
                        <th>الفرع</th>
                        <th>الهاتف</th>
                        <th>الحالة</th>
                        <th class="text-center">الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($bookings as $booking)
                        <tr wire:key="booking-{{ $booking->id }}">
                            <td class="ps-4">
                                <span class="text-muted fw-semibold">{{ $bookings->firstItem() + $loop->index }}</span>
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="student-avatar">{{ mb_substr($booking->name, 0, 1) }}</div>
                                    <div>
                                        <h6 class="mb-0 fw-semibold">{{ $booking->name }}</h6>
                                        <small class="text-muted">{{ $booking->created_at?->format('Y-m-d') }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                @if($booking->grade)
                                    <span class="badge bg-info bg-opacity-10 text-info">
                                        {{ \App\Enums\Teacher\GradeEnum::tryFrom($booking->grade)?->label() ?? $booking->grade }}
                                    </span>
                                @else
                                    <span class="text-muted small">—</span>
                                @endif
                            </td>
                            <td>
                                @if($booking->branch)
                                    <span class="badge bg-primary bg-opacity-10 text-primary">
                                        <i class="ph-duotone ph-git-branch me-1"></i>{{ $booking->branch->name }}
                                    </span>
                                @else
                                    <span class="text-muted small">—</span>
                                @endif
                            </td>
                            <td>
                                @if($booking->phone)
                                    <a href="tel:{{ $booking->phone }}"
                                        class="text-decoration-none small">{{ $booking->phone }}</a>
                                @else
                                    <span class="text-muted small">—</span>
                                @endif
                            </td>
                            <td>
                                @if($booking->status === 'pending')
                                    <span class="badge bg-warning bg-opacity-10 text-warning">
                                        <i class="ph-duotone ph-clock me-1"></i>قيد المراجعة
                                    </span>
                                @elseif($booking->status === 'confirmed')
                                    <span class="badge bg-success bg-opacity-10 text-success">
                                        <i class="ph-duotone ph-check-circle me-1"></i>مؤكد
                                    </span>
                                @else
                                    <span class="badge bg-danger bg-opacity-10 text-danger">
                                        <i class="ph-duotone ph-x-circle me-1"></i>ملغي
                                    </span>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex align-items-center justify-content-center gap-3">
                                    @if($deleteId === $booking->id)
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
                                            <a wire:navigate href="{{ route('teacher.bookings.show', $booking->id) }}"
                                                class="border-0 bg-transparent p-0 text-info" title="عرض">
                                                <i class="ph-duotone ph-eye fs-4"></i>
                                            </a>
                                        @endif

                                        @if($canConfirm && $booking->status === 'pending')
                                            <a wire:navigate href="{{ route('teacher.bookings.show', $booking->id) }}"
                                                class="border-0 bg-transparent p-0 text-success" title="تأكيد">
                                                <i class="ph-duotone ph-check-circle fs-4"></i>
                                            </a>
                                        @endif

                                        @if($canDelete)
                                            <button type="button" wire:click="confirmDelete({{ $booking->id }})"
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
                            <td colspan="7" class="text-center py-5">
                                <div class="text-muted">
                                    <i class="ph-duotone ph-clipboard-text fs-1 d-block mb-2"></i>
                                    <h6 class="mb-1">
                                        {{ $search || $branchFilter || $statusFilter ? 'لا توجد نتائج' : 'لا توجد حجوزات' }}
                                    </h6>
                                    <small>ابدأ بإضافة أول حجز.</small>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($bookings->hasPages())
            <div class="card-footer bg-transparent border-top">
                {{ $bookings->links() }}
            </div>
        @endif
    </div>
</div>
