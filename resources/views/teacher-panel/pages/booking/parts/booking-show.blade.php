<div>
    {{-- Header --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="avtar avtar-l bg-primary bg-opacity-10 text-primary">
                        <span class="fs-2 fw-bold">{{ mb_substr($booking->name, 0, 1) }}</span>
                    </div>
                    <div>
                        <h5 class="mb-1 d-flex align-items-center gap-2 flex-wrap">
                            {{ $booking->name }}
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
                        </h5>
                        <span class="text-muted small">
                            <i class="ph-duotone ph-calendar me-1"></i>
                            {{ $booking->created_at?->format('Y-m-d h:i A') }}
                        </span>
                    </div>
                </div>

                <div class="d-flex gap-2">
                    @if($canConfirm && $booking->isPending())
                        @if($confirming)
                            <div class="alert alert-warning mb-0 d-flex align-items-center gap-2">
                                <i class="ph-duotone ph-warning-circle fs-4"></i>
                                <div>
                                    <strong>تأكيد الحجز وإضافة الطالب؟</strong>
                                </div>
                                <button type="button" wire:click="confirmBooking" wire:loading.attr="disabled"
                                    class="btn btn-sm btn-warning">
                                    <span wire:loading.remove wire:target="confirmBooking">تأكيد</span>
                                    <span wire:loading wire:target="confirmBooking">
                                        <span class="spinner-border spinner-border-sm"></span>
                                    </span>
                                </button>
                                <button type="button" wire:click="cancelConfirm" class="btn btn-sm btn-outline-secondary">
                                    إلغاء
                                </button>
                            </div>
                        @else
                            <button type="button" wire:click="askConfirm" class="btn btn-success">
                                <i class="ph-duotone ph-check-circle me-1"></i>تأكيد الحجز
                            </button>
                            <button type="button" wire:click="cancelBooking" class="btn btn-outline-danger">
                                <i class="ph-duotone ph-x-circle me-1"></i>إلغاء الحجز
                            </button>
                        @endif
                    @endif
                    <a href="{{ route('teacher.bookings.index') }}" wire:navigate class="btn btn-light border">
                        <i class="ph-duotone ph-arrow-right me-1"></i>رجوع
                    </a>
                </div>
            </div>
        </div>
    </div>

    @livewire('teacher-panel.parts.flash-message', [
        'successKey' => 'booking-success',
        'errorKey' => 'booking-error',
    ])

    <div class="row g-4">
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-transparent border-bottom">
                    <h6 class="mb-0 fw-bold"><i class="ph-duotone ph-info me-1"></i>بيانات الحجز</h6>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-sm-6">
                            <small class="text-muted d-block mb-1">الاسم</small>
                            <span class="fw-semibold">{{ $booking->name }}</span>
                        </div>
                        <div class="col-sm-6">
                            <small class="text-muted d-block mb-1">الهاتف</small>
                            @if($booking->phone)
                                <a href="tel:{{ $booking->phone }}" class="text-decoration-none">{{ $booking->phone }}</a>
                            @else
                                <span class="text-muted">غير محدد</span>
                            @endif
                        </div>
                        <div class="col-sm-6">
                            <small class="text-muted d-block mb-1">المرحلة</small>
                            @if($gradeLabel)
                                <span class="badge bg-info bg-opacity-10 text-info">{{ $gradeLabel }}</span>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </div>
                        <div class="col-sm-6">
                            <small class="text-muted d-block mb-1">الفرع</small>
                            @if($booking->branch)
                                <span class="badge bg-primary bg-opacity-10 text-primary">
                                    <i class="ph-duotone ph-git-branch me-1"></i>{{ $booking->branch->name }}
                                </span>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </div>

                        @if($booking->confirmedStudent)
                            <div class="col-12">
                                <hr>
                                <small class="text-muted d-block mb-1">تم إضافة الطالب</small>
                                <a href="{{ route('teacher.students.show', $booking->confirmedStudent->id) }}" wire:navigate
                                    class="btn btn-sm btn-outline-success">
                                    <i class="ph-duotone ph-user-check me-1"></i>
                                    {{ $booking->confirmedStudent->name }} ({{ $booking->confirmedStudent->code }})
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-transparent border-bottom">
                    <h6 class="mb-0 fw-bold"><i class="ph-duotone ph-identification-card me-1"></i>بيانات إضافية</h6>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-sm-6">
                            <small class="text-muted d-block mb-1">ولي الأمر</small>
                            <span>{{ $booking->guardian_name ?: '—' }}</span>
                        </div>
                        <div class="col-sm-6">
                            <small class="text-muted d-block mb-1">هاتف ولي الأمر</small>
                            <span>{{ $booking->guardian_phone ?: '—' }}</span>
                        </div>
                        <div class="col-sm-6">
                            <small class="text-muted d-block mb-1">صلة القرابة</small>
                            <span>{{ $booking->guardian_relation ?: '—' }}</span>
                        </div>
                        <div class="col-sm-6">
                            <small class="text-muted d-block mb-1">المدرسة</small>
                            <span>{{ $booking->school_name ?: '—' }}</span>
                        </div>
                        <div class="col-sm-6">
                            <small class="text-muted d-block mb-1">المدينة</small>
                            <span>{{ $booking->city ?: '—' }}</span>
                        </div>
                        <div class="col-sm-6">
                            <small class="text-muted d-block mb-1">العنوان</small>
                            <span>{{ $booking->address ?: '—' }}</span>
                        </div>
                        <div class="col-12">
                            <small class="text-muted d-block mb-1">ملاحظات</small>
                            <span>{{ $booking->notes ?: '—' }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
