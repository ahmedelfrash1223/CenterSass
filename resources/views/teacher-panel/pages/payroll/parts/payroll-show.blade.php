<div>
    @livewire('teacher-panel.parts.flash-message', [
        'successKey' => 'payroll-success',
        'errorKey' => 'payroll-error',
    ])

    {{-- Header --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
                <div>
                    <h5 class="mb-2">{{ $payroll->reference }}</h5>
                    <div class="d-flex flex-wrap gap-2">
                        <span class="badge bg-{{ $payroll->status->color() }} {{ $payroll->status->textColor() }}">
                            <i class="ph-duotone {{ $payroll->status->icon() }} me-1"></i>
                            {{ $payroll->status->label() }}
                        </span>
                        <span class="badge bg-primary text-white">
                            {{ $payroll->teacher?->name }}
                        </span>
                        @if($payroll->branch)
                            <span class="badge bg-info text-white">
                                <i class="ph-duotone ph-git-branch me-1"></i>{{ $payroll->branch->name }}
                            </span>
                        @endif
                        <span class="badge bg-secondary text-white">
                            {{ $payroll->period_label }}
                        </span>
                        <span class="badge bg-dark text-white">
                            {{ $payroll->payment_type->label() }}
                        </span>
                    </div>
                </div>

                <div class="d-flex gap-2">
                    <a href="{{ route('teacher.payrolls.index') }}" wire:navigate class="btn btn-outline-secondary">
                        <i class="ph-duotone ph-arrow-right me-1"></i>رجوع
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- Financial Summary --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="rounded-3 d-flex align-items-center justify-content-center bg-primary-subtle text-primary flex-shrink-0"
                        style="width: 48px; height: 48px;">
                        <i class="ph-duotone ph-receipt fs-4"></i>
                    </div>
                    <div>
                        <div class="fs-4 fw-bold lh-1">
                            {{ number_format((float) $payroll->net_amount, 2) }}
                        </div>
                        <div class="text-body-secondary small mt-1">الصافي</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="rounded-3 d-flex align-items-center justify-content-center bg-info-subtle text-info flex-shrink-0"
                        style="width: 48px; height: 48px;">
                        <i class="ph-duotone ph-check-circle fs-4"></i>
                    </div>
                    <div>
                        <div class="fs-4 fw-bold lh-1 text-info">
                            {{ number_format((float) $payroll->paid_amount, 2) }}
                        </div>
                        <div class="text-body-secondary small mt-1">المدفوع</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="rounded-3 d-flex align-items-center justify-content-center bg-danger-subtle text-danger flex-shrink-0"
                        style="width: 48px; height: 48px;">
                        <i class="ph-duotone ph-warning-circle fs-4"></i>
                    </div>
                    <div>
                        <div class="fs-4 fw-bold lh-1 text-danger">
                            {{ number_format($payroll->remainingAmount(), 2) }}
                        </div>
                        <div class="text-body-secondary small mt-1">المتبقي</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="rounded-3 d-flex align-items-center justify-content-center bg-success-subtle text-success flex-shrink-0"
                        style="width: 48px; height: 48px;">
                        <i class="ph-duotone ph-users-three fs-4"></i>
                    </div>
                    <div>
                        <div class="fs-4 fw-bold lh-1 text-success">
                            {{ $payroll->payable_sessions }} / {{ $payroll->total_sessions }}
                        </div>
                        <div class="text-body-secondary small mt-1">حصص مدفوعة / إجمالي</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Breakdown --}}
    <div class="row g-4 mb-4">
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-body">
                    <h6 class="mb-0 fw-bold">تفاصيل الحساب</h6>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-body-secondary">الراتب الأساسي:</span>
                        <strong>{{ number_format((float) $payroll->base_salary, 2) }} ج.م</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-body-secondary">مبلغ الحصص:</span>
                        <strong>{{ number_format((float) $payroll->sessions_amount, 2) }} ج.م</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-body-secondary">مبلغ الساعات:</span>
                        <strong>{{ number_format((float) $payroll->hours_amount, 2) }} ج.م</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2 text-success">
                        <span>البونص:</span>
                        <strong>+ {{ number_format((float) $payroll->bonus, 2) }} ج.م</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2 text-danger">
                        <span>الخصومات:</span>
                        <strong>- {{ number_format((float) $payroll->deductions, 2) }} ج.م</strong>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between">
                        <span class="fw-bold">الصافي:</span>
                        <strong class="text-success fs-5">
                            {{ number_format((float) $payroll->net_amount, 2) }} ج.م
                        </strong>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-body">
                    <h6 class="mb-0 fw-bold">الحصص</h6>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-body-secondary">إجمالي الحصص:</span>
                        <strong>{{ $payroll->total_sessions }}</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-body-secondary">حصص ملغية:</span>
                        <strong class="text-danger">{{ $payroll->cancelled_sessions }}</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-body-secondary">حصص قابلة للدفع:</span>
                        <strong class="text-success">{{ $payroll->payable_sessions }}</strong>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="text-body-secondary">إجمالي الساعات:</span>
                        <strong>{{ number_format((float) $payroll->total_hours, 1) }} ساعة</strong>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Actions --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <div class="d-flex flex-wrap gap-2">
                @if($canAddBonus)
                    <button type="button" wire:click="$set('showBonusForm', {{ $showBonusForm ? 'false' : 'true' }})"
                        class="btn {{ $showBonusForm ? 'btn-success' : 'btn-outline-success' }}">
                        <i class="ph-duotone ph-gift me-1"></i>
                        {{ $showBonusForm ? 'إلغاء البونص' : 'إضافة بونص' }}
                    </button>
                    <button type="button"
                        wire:click="$set('showDeductionForm', {{ $showDeductionForm ? 'false' : 'true' }})"
                        class="btn {{ $showDeductionForm ? 'btn-danger' : 'btn-outline-danger' }}">
                        <i class="ph-duotone ph-minus-circle me-1"></i>
                        {{ $showDeductionForm ? 'إلغاء الخصم' : 'إضافة خصم' }}
                    </button>
                @endif

                @if($canMarkPaid && $payroll->remainingAmount() > 0)
                    <button type="button" wire:click="$set('showPayForm', {{ $showPayForm ? 'false' : 'true' }})"
                        class="btn {{ $showPayForm ? 'btn-secondary' : 'btn-success' }} ms-auto">
                        <i class="ph-duotone ph-check-circle me-1"></i>
                        {{ $showPayForm ? 'إلغاء الدفع' : 'تسجيل دفع' }}
                    </button>
                @endif
            </div>
        </div>
    </div>

    {{-- Bonus Form (Inline) --}}
    @if($showBonusForm)
        <div class="card border-success border-2 mb-4">
            <div class="card-header bg-success-subtle d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-bold text-success-emphasis">
                    <i class="ph-duotone ph-gift me-1"></i>
                    إضافة بونص
                </h6>
                <button type="button" wire:click="$set('showBonusForm', false)" class="btn btn-sm btn-outline-secondary">
                    <i class="ph-duotone ph-x"></i>
                </button>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">المبلغ <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" min="0.01" wire:model="bonusAmount"
                            class="form-control @error('bonusAmount') is-invalid @enderror">
                        @error('bonusAmount')
                        <div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">السبب <span class="text-danger">*</span></label>
                        <input type="text" wire:model="bonusReason"
                            class="form-control @error('bonusReason') is-invalid @enderror" placeholder="مثال: أداء ممتاز">
                        @error('bonusReason')
                        <div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div class="d-flex gap-2 mt-3">
                    <button type="button" wire:click="addBonus" class="btn btn-success" wire:loading.attr="disabled"
                        wire:target="addBonus">
                        <span wire:loading.remove wire:target="addBonus">
                            <i class="ph-duotone ph-check me-1"></i>إضافة
                        </span>
                        <span wire:loading wire:target="addBonus">
                            <span class="spinner-border spinner-border-sm me-1"></span>جاري...
                        </span>
                    </button>
                    <button type="button" wire:click="$set('showBonusForm', false)" class="btn btn-outline-secondary">
                        إلغاء
                    </button>
                </div>
            </div>
        </div>
    @endif

    {{-- Deduction Form (Inline) --}}
    @if($showDeductionForm)
        <div class="card border-danger border-2 mb-4">
            <div class="card-header bg-danger-subtle d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-bold text-danger-emphasis">
                    <i class="ph-duotone ph-minus-circle me-1"></i>
                    إضافة خصم
                </h6>
                <button type="button" wire:click="$set('showDeductionForm', false)"
                    class="btn btn-sm btn-outline-secondary">
                    <i class="ph-duotone ph-x"></i>
                </button>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">المبلغ <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" min="0.01" wire:model="deductionAmount"
                            class="form-control @error('deductionAmount') is-invalid @enderror">
                        @error('deductionAmount')
                        <div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">السبب <span class="text-danger">*</span></label>
                        <input type="text" wire:model="deductionReason"
                            class="form-control @error('deductionReason') is-invalid @enderror" placeholder="مثال: تأخير">
                        @error('deductionReason')
                        <div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div class="d-flex gap-2 mt-3">
                    <button type="button" wire:click="addDeduction" class="btn btn-danger" wire:loading.attr="disabled"
                        wire:target="addDeduction">
                        <span wire:loading.remove wire:target="addDeduction">
                            <i class="ph-duotone ph-check me-1"></i>إضافة
                        </span>
                        <span wire:loading wire:target="addDeduction">
                            <span class="spinner-border spinner-border-sm me-1"></span>جاري...
                        </span>
                    </button>
                    <button type="button" wire:click="$set('showDeductionForm', false)" class="btn btn-outline-secondary">
                        إلغاء
                    </button>
                </div>
            </div>
        </div>
    @endif

    {{-- Pay Form (Inline) --}}
    @if($showPayForm)
        <div class="card border-success border-2 mb-4">
            <div class="card-header bg-success-subtle d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-bold text-success-emphasis">
                    <i class="ph-duotone ph-check-circle me-1"></i>
                    تسجيل دفع
                </h6>
                <button type="button" wire:click="$set('showPayForm', false)" class="btn btn-sm btn-outline-secondary">
                    <i class="ph-duotone ph-x"></i>
                </button>
            </div>
            <div class="card-body">
                <div class="alert alert-info small mb-3">
                    <div class="d-flex justify-content-between">
                        <span>المتبقي:</span>
                        <strong>{{ number_format($payroll->remainingAmount(), 2) }} ج.م</strong>
                    </div>
                </div>

                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">المبلغ</label>
                        <input type="number" step="0.01" min="0.01" wire:model="payAmount" class="form-control"
                            placeholder="{{ $payroll->remainingAmount() }}">
                        <small class="text-body-secondary">اتركه فارغ لدفع المبلغ كاملاً</small>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">طريقة الدفع</label>
                        <select wire:model="paymentMethod" class="form-select">
                            <option value="cash">نقدي</option>
                            <option value="bank_transfer">تحويل بنكي</option>
                            <option value="wallet">محفظة</option>
                            <option value="other">أخرى</option>
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">ملاحظات</label>
                        <input type="text" wire:model="paymentNotes" class="form-control">
                    </div>
                </div>

                <div class="d-flex gap-2 mt-3">
                    <button type="button" wire:click="markAsPaid" class="btn btn-success" wire:loading.attr="disabled"
                        wire:target="markAsPaid">
                        <span wire:loading.remove wire:target="markAsPaid">
                            <i class="ph-duotone ph-check me-1"></i>تأكيد الدفع
                        </span>
                        <span wire:loading wire:target="markAsPaid">
                            <span class="spinner-border spinner-border-sm me-1"></span>جاري...
                        </span>
                    </button>
                    <button type="button" wire:click="$set('showPayForm', false)" class="btn btn-outline-secondary">
                        إلغاء
                    </button>
                </div>
            </div>
        </div>
    @endif

    {{-- Bonuses Table --}}
    @if($payroll->bonuses->count() > 0)
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-body">
                <h6 class="mb-0 fw-bold">
                    <i class="ph-duotone ph-gift me-1 text-success"></i>
                    البونص ({{ $payroll->bonuses->count() }})
                </h6>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">#</th>
                            <th>السبب</th>
                            <th class="text-center">المبلغ</th>
                            <th>التاريخ</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($payroll->bonuses as $i => $bonus)
                            <tr wire:key="bonus-{{ $bonus->id }}">
                                <td class="ps-4">{{ $i + 1 }}</td>
                                <td>{{ $bonus->reason }}</td>
                                <td class="text-center text-success fw-bold">
                                    + {{ number_format((float) $bonus->amount, 2) }}
                                </td>
                                <td><small>{{ $bonus->created_at?->format('Y-m-d') }}</small></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    {{-- Deductions Table --}}
    @if($payroll->deductionsItems->count() > 0)
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-body">
                <h6 class="mb-0 fw-bold">
                    <i class="ph-duotone ph-minus-circle me-1 text-danger"></i>
                    الخصومات ({{ $payroll->deductionsItems->count() }})
                </h6>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">#</th>
                            <th>السبب</th>
                            <th class="text-center">المبلغ</th>
                            <th>التاريخ</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($payroll->deductionsItems as $i => $deduction)
                            <tr wire:key="ded-{{ $deduction->id }}">
                                <td class="ps-4">{{ $i + 1 }}</td>
                                <td>{{ $deduction->reason }}</td>
                                <td class="text-center text-danger fw-bold">
                                    - {{ number_format((float) $deduction->amount, 2) }}
                                </td>
                                <td><small>{{ $deduction->created_at?->format('Y-m-d') }}</small></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    {{-- Payments Table --}}
    @if($payroll->payments->count() > 0)
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-body">
                <h6 class="mb-0 fw-bold">
                    <i class="ph-duotone ph-check-circle me-1 text-info"></i>
                    سجل الدفعات ({{ $payroll->payments->count() }})
                </h6>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">#</th>
                            <th>الرقم</th>
                            <th>الطريقة</th>
                            <th class="text-center">المبلغ</th>
                            <th>التاريخ</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($payroll->payments as $i => $payment)
                            <tr wire:key="pay-{{ $payment->id }}">
                                <td class="ps-4">{{ $i + 1 }}</td>
                                <td><small>{{ $payment->reference }}</small></td>
                                <td><small>{{ $payment->payment_method }}</small></td>
                                <td class="text-center text-info fw-bold">
                                    {{ number_format((float) $payment->amount, 2) }}
                                </td>
                                <td><small>{{ $payment->paid_at?->format('Y-m-d H:i') }}</small></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</div>
