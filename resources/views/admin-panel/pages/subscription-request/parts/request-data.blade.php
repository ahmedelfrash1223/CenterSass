<div>
    <!-- Success/Error Messages -->
    @if(session()->has('request-success'))
        <div class="alert alert-success d-flex align-items-center gap-2 mb-4">
            <i class="ph-duotone ph-check-circle fs-5"></i>
            <span>{{ session('request-success') }}</span>
        </div>
    @endif

    @if(session()->has('request-error'))
        <div class="alert alert-danger d-flex align-items-center gap-2 mb-4">
            <i class="ph-duotone ph-x-circle fs-5"></i>
            <span>{{ session('request-error') }}</span>
        </div>
    @endif

    <!-- Header -->
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <p class="text-muted mb-0 small">إدارة طلبات تغيير الباقات من المدرسين</p>
        </div>
    </div>

    <!-- Filters -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-6">
                    <div class="position-relative">
                        <i class="ph-duotone ph-magnifying-glass position-absolute top-50 translate-middle-y ms-3 text-muted"></i>
                        <input
                            type="text"
                            wire:model.live.debounce.400ms="search"
                            class="form-control ps-5"
                            placeholder="ابحث باسم المدرس أو البريد الإلكتروني أو اسم الباقة..."
                        >
                    </div>
                </div>
                <div class="col-md-3">
                    <select class="form-select" wire:model.live="statusFilter">
                        <option value="">جميع الحالات</option>
                        @foreach($statusOptions as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 text-md-end">
                    <button type="button" class="btn btn-light border" wire:click="$set('statusFilter', '')">
                        <i class="ph-duotone ph-funnel-simple"></i>
                        إعادة تعيين
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Table -->
    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                <tr>
                    <th class="ps-4">#</th>
                    <th>المدرس</th>
                    <th>الباقة الحالية</th>
                    <th>الباقة المطلوبة</th>
                    <th>تاريخ الطلب</th>
                    <th>الحالة</th>
                    <th class="text-center">الإجراءات</th>
                </tr>
                </thead>
                <tbody>
                @forelse($requests as $request)
                    <tr wire:key="request-{{ $request->id }}">
                        <td class="ps-4">
                            <span class="text-muted fw-semibold">#{{ $request->id }}</span>
                        </td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <div class="avtar avtar-s bg-light-primary text-primary">
                                    <i class="ph-duotone ph-user fs-5"></i>
                                </div>
                                <div>
                                    <h6 class="mb-0 fw-semibold">{{ $request->teacher->name }}</h6>
                                    <small class="text-muted">{{ $request->teacher->email }}</small>
                                </div>
                            </div>
                        </td>
                        <td>
                            @if($request->currentSubscription)
                                <span class="fw-semibold">{{ $request->currentSubscription->package->title }}</span>
                            @else
                                <span class="text-muted">لا يوجد</span>
                            @endif
                        </td>
                        <td>
                            <span class="fw-semibold text-primary">{{ $request->requestedPackage->title }}</span>
                            <br>
                            <small class="text-muted">{{ number_format($request->requestedPackage->price_after_discount, 2) }} ج.م</small>
                        </td>
                        <td>
                                <span class="text-muted small">
                                    {{ $request->created_at->format('Y-m-d') }}
                                    <br>
                                    <span class="text-muted">{{ $request->created_at->format('h:i A') }}</span>
                                </span>
                        </td>
                        <td>
                            {!! $request->status_badge !!}
                        </td>
                        <td>
                            <div class="d-flex align-items-center justify-content-center gap-2">
                                @if($request->status === \App\Enums\Admin\SubscriptionRequestStatusEnum::PENDING->value)
                                    <button
                                        type="button"
                                        class="btn btn-sm btn-success"
                                        wire:click="confirmApprove({{ $request->id }})"
                                        title="قبول"
                                    >
                                        <i class="ph-duotone ph-check"></i>
                                    </button>
                                    <button
                                        type="button"
                                        class="btn btn-sm btn-danger"
                                        wire:click="confirmReject({{ $request->id }})"
                                        title="رفض"
                                    >
                                        <i class="ph-duotone ph-x"></i>
                                    </button>
                                @else
                                    <span class="text-muted small">تم المعالجة</span>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center py-5">
                            <div class="text-muted">
                                <i class="ph-duotone ph-package fs-1 d-block mb-2"></i>
                                <h6 class="mb-1">لا توجد طلبات</h6>
                                <small>لا يوجد طلبات تغيير باقة حالياً</small>
                            </div>
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        @if($requests->hasPages())
            <div class="card-footer bg-transparent border-top">
                {{ $requests->links() }}
            </div>
        @endif
    </div>

    <!-- Approve Confirmation Modal -->
    @if($showApproveConfirm && $selectedRequestId)
        <div class="modal d-block" style="background: rgba(0,0,0,0.5);" wire:key="approve-modal">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg">
                    <div class="modal-header border-0">
                        <h5 class="modal-title fw-bold">تأكيد القبول</h5>
                        <button type="button" class="btn-close" wire:click="cancelApprove"></button>
                    </div>
                    <div class="modal-body">
                        <div class="text-center mb-3">
                            <i class="ph-duotone ph-check-circle fs-1 text-success"></i>
                        </div>
                        <p class="text-center">
                            هل أنت متأكد من قبول طلب تغيير الباقة؟
                        </p>
                        <p class="text-center text-muted small">
                            سيتم تحديث اشتراك المدرس إلى الباقة المطلوبة فوراً.
                        </p>
                    </div>
                    <div class="modal-footer border-0">
                        <button type="button" class="btn btn-light border" wire:click="cancelApprove">إلغاء</button>
                        <button type="button" class="btn btn-success" wire:click="approve" wire:loading.attr="disabled">
                            <span wire:loading.remove>
                                <i class="ph-duotone ph-check me-1"></i>
                                تأكيد القبول
                            </span>
                            <span wire:loading>
                                <span class="spinner-border spinner-border-sm me-1"></span>
                                جاري المعالجة...
                            </span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Reject Confirmation Modal -->
    @if($showRejectConfirm && $selectedRequestId)
        <div class="modal d-block" style="background: rgba(0,0,0,0.5);" wire:key="reject-modal">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg">
                    <div class="modal-header border-0">
                        <h5 class="modal-title fw-bold">تأكيد الرفض</h5>
                        <button type="button" class="btn-close" wire:click="cancelReject"></button>
                    </div>
                    <div class="modal-body">
                        <div class="text-center mb-3">
                            <i class="ph-duotone ph-x-circle fs-1 text-danger"></i>
                        </div>
                        <p class="text-center">
                            هل أنت متأكد من رفض طلب تغيير الباقة؟
                        </p>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">سبب الرفض (اختياري)</label>
                            <textarea
                                class="form-control"
                                wire:model="rejectReason"
                                rows="2"
                                placeholder="أدخل سبب الرفض..."
                            ></textarea>
                        </div>
                    </div>
                    <div class="modal-footer border-0">
                        <button type="button" class="btn btn-light border" wire:click="cancelReject">إلغاء</button>
                        <button type="button" class="btn btn-danger" wire:click="reject" wire:loading.attr="disabled">
                            <span wire:loading.remove>
                                <i class="ph-duotone ph-x me-1"></i>
                                تأكيد الرفض
                            </span>
                            <span wire:loading>
                                <span class="spinner-border spinner-border-sm me-1"></span>
                                جاري المعالجة...
                            </span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
