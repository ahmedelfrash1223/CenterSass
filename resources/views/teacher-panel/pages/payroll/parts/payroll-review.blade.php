<div>
    @livewire('teacher-panel.parts.flash-message', [
        'successKey' => 'payroll-success',
        'errorKey' => 'payroll-error',
    ])

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-body">
            <h6 class="mb-0 fw-bold">
                <i class="ph-duotone ph-clipboard-check me-1"></i>
                مراجعة كشوف الحسابات
            </h6>
        </div>
        <div class="card-body p-0">
            @forelse($payrolls as $payroll)
                <div class="card border-bottom rounded-0" wire:key="review-{{ $payroll->id }}">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-3 flex-wrap gap-2">
                            <div>
                                <h6 class="fw-bold mb-1">{{ $payroll->teacher?->name }}</h6>
                                <div class="d-flex gap-2 flex-wrap small">
                                    <span class="badge bg-primary text-white">{{ $payroll->period_label }}</span>
                                    @if($payroll->branch)
                                        <span class="badge bg-info text-white">{{ $payroll->branch->name }}</span>
                                    @endif
                                    <span class="badge bg-secondary text-white">{{ $payroll->reference }}</span>
                                </div>
                            </div>
                            <div class="text-end">
                                <div class="fs-4 fw-bold text-success">
                                    {{ number_format((float) $payroll->net_amount, 2) }} ج.م
                                </div>
                            </div>
                        </div>

                        {{-- Session Details --}}
                        <div class="row g-2 mb-3">
                            <div class="col-md-3">
                                <small class="text-body-secondary d-block">إجمالي الحصص</small>
                                <strong>{{ $payroll->total_sessions }}</strong>
                            </div>
                            <div class="col-md-3">
                                <small class="text-body-secondary d-block">قابلة للدفع</small>
                                <strong class="text-success">{{ $payroll->payable_sessions }}</strong>
                            </div>
                            <div class="col-md-3">
                                <small class="text-body-secondary d-block">ملغية</small>
                                <strong class="text-danger">{{ $payroll->cancelled_sessions }}</strong>
                            </div>
                            <div class="col-md-3">
                                <small class="text-body-secondary d-block">الساعات</small>
                                <strong>{{ number_format((float) $payroll->total_hours, 1) }}</strong>
                            </div>
                        </div>

                        {{-- Bonus & Deduction Forms --}}
                        <div class="row g-2 mb-3">
                            <div class="col-md-6">
                                <div class="input-group input-group-sm">
                                    <input type="number" step="0.01" placeholder="مبلغ البونص"
                                        wire:model="bonusAmounts.{{ $payroll->id }}" class="form-control">
                                    <input type="text" placeholder="السبب" wire:model="bonusReasons.{{ $payroll->id }}"
                                        class="form-control">
                                    <button type="button" wire:click="addBonus({{ $payroll->id }})"
                                        class="btn btn-outline-success">
                                        <i class="ph-duotone ph-plus"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="input-group input-group-sm">
                                    <input type="number" step="0.01" placeholder="مبلغ الخصم"
                                        wire:model="deductionAmounts.{{ $payroll->id }}" class="form-control">
                                    <input type="text" placeholder="السبب" wire:model="deductionReasons.{{ $payroll->id }}"
                                        class="form-control">
                                    <button type="button" wire:click="addDeduction({{ $payroll->id }})"
                                        class="btn btn-outline-danger">
                                        <i class="ph-duotone ph-minus"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        {{-- Actions --}}
                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('teacher.payrolls.show', $payroll->id) }}" wire:navigate
                                class="btn btn-sm btn-outline-primary">
                                <i class="ph-duotone ph-eye me-1"></i>عرض التفاصيل
                            </a>
                            <button type="button" wire:click="markAsReviewed({{ $payroll->id }})"
                                class="btn btn-sm btn-success" wire:loading.attr="disabled">
                                <i class="ph-duotone ph-check me-1"></i>تمت المراجعة
                            </button>
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center py-5">
                    <i class="ph-duotone ph-check-circle fs-1 d-block mb-2 text-success"></i>
                    <h6 class="text-body-secondary">لا توجد كشوف للمراجعة</h6>
                </div>
            @endforelse
        </div>
        @if($payrolls->hasPages())
            <div class="card-footer bg-transparent">{{ $payrolls->links() }}</div>
        @endif
    </div>
</div>
