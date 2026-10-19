<div>
    @livewire('teacher-panel.parts.flash-message', [
        'successKey' => 'payroll-success',
        'errorKey' => 'payroll-error',
    ])

    @if(!$hasSettings)
        <div class="alert alert-warning d-flex align-items-center gap-2">
            <i class="ph-duotone ph-warning-circle fs-3"></i>
            <div class="flex-grow-1">
                <strong>مفيش إعدادات أجر لهذا الموظف!</strong>
                <p class="mb-0 small">ضيف إعدادات الأجر الأول عشان الحسابات تظهر صح.</p>
            </div>
            @if($canManage)
                <button type="button" wire:click="$set('showSettingsForm', true)" class="btn btn-warning btn-sm">
                    <i class="ph-duotone ph-gear me-1"></i>ضيف الإعدادات
                </button>
            @endif
        </div>
    @endif

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
                        @if($payroll->is_reviewed)
                            <span class="badge bg-success text-white">
                                <i class="ph-duotone ph-check-circle me-1"></i>تمت المراجعة
                            </span>
                        @endif
                        <span class="badge bg-primary text-white">{{ $payroll->teacher?->name }}</span>
                        <span class="badge bg-info text-white">
                            <i class="ph-duotone ph-git-branch me-1"></i>
                            {{ $payroll->branch?->name ?? 'كل الفروع' }}
                        </span>
                        <span class="badge bg-secondary text-white">{{ $payroll->period_label }}</span>
                    </div>
                </div>

                {{-- ✅ الأزرار --}}
                <div class="d-flex gap-2 flex-wrap">
                    {{-- PDF الحساب (تحميل) --}}
                    <a href="{{ route('teacher.payrolls.pdf.download', $payroll->id) }}"
                        class="btn btn-sm btn-outline-danger">
                        <i class="ph-duotone ph-file-pdf me-1"></i>تحميل PDF
                    </a>
                    <a href="{{ route('teacher.payrolls.pdf.stream', $payroll->id) }}" target="_blank" rel="noopener"
                        class="btn btn-sm btn-outline-primary">
                        <i class="ph-duotone ph-printer me-1"></i>طباعة
                    </a>

                    <a href="{{ route('teacher.payrolls.index') }}" wire:navigate
                        class="btn btn-sm btn-outline-secondary">
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
                        <div class="fs-4 fw-bold lh-1">{{ number_format($amounts['net'], 2) }}</div>
                        <div class="text-body-secondary small mt-1">الصافي بعد الخصم</div>
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
                        <div class="fs-4 fw-bold lh-1 text-info">{{ number_format($amounts['paid'], 2) }}</div>
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
                        <div class="fs-4 fw-bold lh-1 text-danger">{{ number_format($amounts['remaining'], 2) }}</div>
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
                        <i class="ph-duotone ph-clock fs-4"></i>
                    </div>
                    <div>
                        <div class="fs-4 fw-bold lh-1 text-success">{{ number_format($amounts['until_today'], 2) }}
                        </div>
                        <div class="text-body-secondary small mt-1">حتى الآن</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Actions --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <div class="d-flex flex-wrap gap-2">
                @if($canManage)
                    <button type="button" wire:click="$set('showSettingsForm', {{ $showSettingsForm ? 'false' : 'true' }})"
                        class="btn {{ $showSettingsForm ? 'btn-dark' : 'btn-outline-dark' }}">
                        <i class="ph-duotone ph-gear me-1"></i>{{ $showSettingsForm ? 'إلغاء' : 'إعدادات الأجر' }}
                    </button>
                @endif
                @if($canAddBonus)
                    <button type="button" wire:click="$set('showBonusForm', {{ $showBonusForm ? 'false' : 'true' }})"
                        class="btn {{ $showBonusForm ? 'btn-success' : 'btn-outline-success' }}">
                        <i class="ph-duotone ph-gift me-1"></i>{{ $showBonusForm ? 'إلغاء' : 'إضافة بونص' }}
                    </button>
                    <button type="button"
                        wire:click="$set('showDeductionForm', {{ $showDeductionForm ? 'false' : 'true' }})"
                        class="btn {{ $showDeductionForm ? 'btn-danger' : 'btn-outline-danger' }}">
                        <i class="ph-duotone ph-minus-circle me-1"></i>{{ $showDeductionForm ? 'إلغاء' : 'إضافة خصم' }}
                    </button>
                @endif
                @if($canMarkPaid && $amounts['remaining'] > 0)
                    <button type="button" wire:click="$set('showPayForm', {{ $showPayForm ? 'false' : 'true' }})"
                        class="btn {{ $showPayForm ? 'btn-secondary' : 'btn-success' }} ms-auto">
                        <i class="ph-duotone ph-check-circle me-1"></i>{{ $showPayForm ? 'إلغاء' : 'تسجيل دفع' }}
                    </button>
                @endif
            </div>
        </div>
    </div>

    {{-- Settings Form --}}
    @if($showSettingsForm)
        <div class="card border-dark border-2 mb-4">
            <div class="card-header bg-dark-subtle d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-bold"><i class="ph-duotone ph-gear me-1"></i>إعدادات الأجر</h6>
                <button type="button" wire:click="$set('showSettingsForm', false)"
                    class="btn btn-sm btn-outline-secondary"><i class="ph-duotone ph-x"></i></button>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">نوع الأجر</label>
                        <select wire:model.live="payment_type" class="form-select">
                            @foreach(\App\Enums\Teacher\TeacherPaymentTypeEnum::options() as $value => $label)
                                <option value="{{ $value }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    @if(in_array($payment_type, ['monthly', 'monthly_plus_per_session']))
                        <div class="col-md-6">
                            <label class="form-label">الراتب الشهري</label>
                            <div class="input-group"><input type="number" step="0.01" wire:model="monthly_salary"
                                    class="form-control"><span class="input-group-text">ج.م</span></div>
                        </div>
                    @endif
                    @if(in_array($payment_type, ['per_session', 'monthly_plus_per_session']))
                        <div class="col-md-6">
                            <label class="form-label">سعر الحصة</label>
                            <div class="input-group"><input type="number" step="0.01" wire:model="per_session_rate"
                                    class="form-control"><span class="input-group-text">ج.م</span></div>
                        </div>
                    @endif
                    @if($payment_type === 'hourly')
                        <div class="col-md-6">
                            <label class="form-label">سعر الساعة</label>
                            <div class="input-group"><input type="number" step="0.01" wire:model="hourly_rate"
                                    class="form-control"><span class="input-group-text">ج.م</span></div>
                        </div>
                    @endif
                    <div class="col-md-6">
                        <label class="form-label">خصم الحصة الملغية</label>
                        <div class="input-group"><input type="number" step="0.01" wire:model="session_deduction_rate"
                                class="form-control"><span class="input-group-text">ج.م</span></div>
                    </div>
                </div>
                <div class="d-flex justify-content-end mt-3">
                    <button type="button" wire:click="saveSettings" class="btn btn-dark" wire:loading.attr="disabled">
                        <span wire:loading.remove wire:target="saveSettings"><i
                                class="ph-duotone ph-floppy-disk me-1"></i>حفظ</span>
                        <span wire:loading wire:target="saveSettings"><span
                                class="spinner-border spinner-border-sm"></span></span>
                    </button>
                </div>
            </div>
        </div>
    @endif

    {{-- Bonus Form --}}
    @if($showBonusForm)
        <div class="card border-success border-2 mb-4">
            <div class="card-header bg-success-subtle d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-bold text-success-emphasis"><i class="ph-duotone ph-gift me-1"></i>إضافة بونص</h6>
                <button type="button" wire:click="$set('showBonusForm', false)" class="btn btn-sm btn-outline-secondary"><i
                        class="ph-duotone ph-x"></i></button>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6"><label class="form-label">المبلغ</label><input type="number" step="0.01"
                            wire:model="bonusAmount" class="form-control"></div>
                    <div class="col-md-6"><label class="form-label">السبب</label><input type="text" wire:model="bonusReason"
                            class="form-control"></div>
                </div>
                <div class="d-flex gap-2 mt-3">
                    <button type="button" wire:click="addBonus" class="btn btn-success" wire:loading.attr="disabled">
                        <span wire:loading.remove wire:target="addBonus"><i
                                class="ph-duotone ph-check me-1"></i>إضافة</span>
                        <span wire:loading wire:target="addBonus"><span
                                class="spinner-border spinner-border-sm"></span></span>
                    </button>
                    <button type="button" wire:click="$set('showBonusForm', false)"
                        class="btn btn-outline-secondary">إلغاء</button>
                </div>
            </div>
        </div>
    @endif

    {{-- Deduction Form --}}
    @if($showDeductionForm)
        <div class="card border-danger border-2 mb-4">
            <div class="card-header bg-danger-subtle d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-bold text-danger-emphasis"><i class="ph-duotone ph-minus-circle me-1"></i>إضافة خصم</h6>
                <button type="button" wire:click="$set('showDeductionForm', false)"
                    class="btn btn-sm btn-outline-secondary"><i class="ph-duotone ph-x"></i></button>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6"><label class="form-label">المبلغ</label><input type="number" step="0.01"
                            wire:model="deductionAmount" class="form-control"></div>
                    <div class="col-md-6"><label class="form-label">السبب</label><input type="text"
                            wire:model="deductionReason" class="form-control"></div>
                </div>
                <div class="d-flex gap-2 mt-3">
                    <button type="button" wire:click="addDeduction" class="btn btn-danger" wire:loading.attr="disabled">
                        <span wire:loading.remove wire:target="addDeduction"><i
                                class="ph-duotone ph-check me-1"></i>إضافة</span>
                        <span wire:loading wire:target="addDeduction"><span
                                class="spinner-border spinner-border-sm"></span></span>
                    </button>
                    <button type="button" wire:click="$set('showDeductionForm', false)"
                        class="btn btn-outline-secondary">إلغاء</button>
                </div>
            </div>
        </div>
    @endif

    {{-- Pay Form --}}
    @if($showPayForm)
        <div class="card border-success border-2 mb-4">
            <div class="card-header bg-success-subtle d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-bold text-success-emphasis"><i class="ph-duotone ph-check-circle me-1"></i>تسجيل دفع</h6>
                <button type="button" wire:click="$set('showPayForm', false)" class="btn btn-sm btn-outline-secondary"><i
                        class="ph-duotone ph-x"></i></button>
            </div>
            <div class="card-body">
                <div class="alert alert-info small mb-3">
                    <div class="d-flex justify-content-between"><span>المتبقي
                            الكلي:</span><strong>{{ number_format($amounts['remaining'], 2) }} ج.م</strong></div>
                    @if($partialRemaining > 0 && $partialRemaining < $amounts['remaining'])
                        <div class="d-flex justify-content-between mt-1">
                            <span>المتبقي لحد النهارده ({{ $partialSessions }} حصة):</span>
                            <strong class="text-primary">{{ number_format($partialRemaining, 2) }} ج.م</strong>
                        </div>
                    @endif
                </div>
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">المبلغ</label>
                        <input type="number" step="0.01" wire:model="payAmount" class="form-control"
                            placeholder="{{ $amounts['remaining'] }}">
                        <small class="text-body-secondary">اتركه فارغ لدفع المبلغ كاملاً</small>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">طريقة الدفع</label>
                        <select wire:model="paymentMethod" class="form-select">
                            @foreach(\App\Enums\Teacher\PaymentMethodEnum::options() as $value => $label)
                                <option value="{{ $value }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">ملاحظات</label>
                        <input type="text" wire:model="paymentNotes" class="form-control">
                    </div>
                </div>
                <div class="d-flex flex-wrap gap-2 mt-3">
                    {{-- ✅ دفع المتبقي لحد النهارده (بعد الدفعات السابقة) --}}
                    @if($partialRemaining > 0 && $partialRemaining < $amounts['remaining'])
                        <button type="button" wire:click="requestPay(false)" class="btn btn-outline-primary">
                            <i class="ph-duotone ph-clock me-1"></i>دفع حتى الآن ({{ number_format($partialRemaining, 2) }})
                        </button>
                    @endif
                    {{-- ✅ دفع الحساب كامل (المتبقي) --}}
                    <button type="button" wire:click="requestPay(true)" class="btn btn-success">
                        <i class="ph-duotone ph-check-circle me-1"></i>دفع الحساب كامل
                        ({{ number_format($amounts['remaining'], 2) }})
                    </button>
                </div>
                @if($confirmPartialPay || $confirmFullPay)
                    <div class="alert alert-warning mt-3 mb-0">
                        <div class="d-flex align-items-center gap-2 mb-2"><i
                                class="ph-duotone ph-warning-circle fs-4"></i><strong>{{ $confirmFullPay ? 'تأكيد دفع كامل' : 'تأكيد الدفع الجزئي' }}</strong>
                        </div>
                        <p class="small mb-2">هتسجل دفعة بمبلغ <strong>{{ number_format((float) $payAmount, 2) }} ج.م</strong>
                        </p>
                        <div class="d-flex gap-2">
                            <button type="button" wire:click="confirmPayment" class="btn btn-sm btn-success"
                                wire:loading.attr="disabled"><span wire:loading.remove
                                    wire:target="confirmPayment">تأكيد</span><span wire:loading
                                    wire:target="confirmPayment"><span
                                        class="spinner-border spinner-border-sm"></span></span></button>
                            <button type="button" wire:click="cancelPay" class="btn btn-sm btn-outline-secondary">إلغاء</button>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    @endif

    {{-- Tabs --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-body p-0">
            <ul class="nav nav-tabs border-0">
                <li class="nav-item">
                    <button type="button" wire:click="setTab('details')"
                        class="nav-link {{ $activeTab === 'details' ? 'active' : '' }} border-0 fw-semibold">
                        <i class="ph-duotone ph-receipt me-1"></i>تفاصيل الحساب
                    </button>
                </li>
                <li class="nav-item">
                    <button type="button" wire:click="setTab('sessions')"
                        class="nav-link {{ $activeTab === 'sessions' ? 'active' : '' }} border-0 fw-semibold">
                        <i class="ph-duotone ph-calendar me-1"></i>كل الحصص ({{ $allSessions->count() }})
                    </button>
                </li>
            </ul>
        </div>
        <div class="card-body">
            {{-- Details --}}
            @if($activeTab === 'details')
                <div class="row g-4 mb-4">
                    <div class="col-lg-6">
                        <div class="card border h-100">
                            <div class="card-header bg-body">
                                <h6 class="mb-0 fw-bold">تفاصيل الحساب</h6>
                            </div>
                            <div class="card-body">
                                <div class="d-flex justify-content-between mb-2"><span class="text-body-secondary">المبلغ
                                        الكامل:</span><strong>{{ number_format($amounts['full'], 2) }} ج.م</strong></div>
                                @if($amounts['cancellation_deduction'] > 0)
                                    <div class="d-flex justify-content-between mb-2 text-danger"><span>خصم الحصص
                                            الملغية:</span><strong>- {{ number_format($amounts['cancellation_deduction'], 2) }}
                                            ج.م</strong></div>
                                @endif
                                <div class="d-flex justify-content-between mb-2 text-success"><span>البونص:</span><strong>+
                                        {{ number_format($amounts['bonus'], 2) }} ج.م</strong></div>
                                <div class="d-flex justify-content-between mb-2 text-danger"><span>الخصومات:</span><strong>-
                                        {{ number_format($amounts['deductions'], 2) }} ج.م</strong></div>
                                <hr>
                                <div class="d-flex justify-content-between"><span class="fw-bold">الصافي:</span><strong
                                        class="text-success fs-5">{{ number_format($amounts['net'], 2) }} ج.م</strong></div>
                                <div class="d-flex justify-content-between mt-2"><span
                                        class="text-body-secondary">المدفوع:</span><strong
                                        class="text-info">{{ number_format($amounts['paid'], 2) }} ج.م</strong></div>
                                <div class="d-flex justify-content-between"><span
                                        class="text-body-secondary">المتبقي:</span><strong
                                        class="text-danger">{{ number_format($amounts['remaining'], 2) }} ج.م</strong></div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-6">
                        <div class="card border h-100">
                            <div class="card-header bg-body">
                                <h6 class="mb-0 fw-bold">إحصائيات الشهر</h6>
                            </div>
                            <div class="card-body">
                                <div class="d-flex justify-content-between mb-2"><span class="text-body-secondary">إجمالي
                                        الحصص:</span><strong>{{ $stats['total'] }}</strong></div>
                                <div class="d-flex justify-content-between mb-2"><span
                                        class="text-body-secondary">مكتملة:</span><strong
                                        class="text-success">{{ $stats['completed'] }}</strong></div>
                                <div class="d-flex justify-content-between mb-2"><span
                                        class="text-body-secondary">ملغية:</span><strong
                                        class="text-danger">{{ $stats['cancelled'] }}</strong></div>
                                <div class="d-flex justify-content-between mb-2"><span class="text-body-secondary">لم
                                        تُدرّس:</span><strong class="text-secondary">{{ $stats['upcoming'] }}</strong></div>
                                <hr>
                                <div class="d-flex justify-content-between mb-2"><span class="text-body-secondary">حتى الآن
                                        (حصص):</span><strong class="text-info">{{ $statsToday['total'] }}</strong></div>
                                <div class="d-flex justify-content-between mb-2"><span class="text-body-secondary">مكتملة
                                        حتى الآن:</span><strong class="text-success">{{ $statsToday['completed'] }}</strong>
                                </div>
                                <div class="d-flex justify-content-between"><span class="text-body-secondary">ملغية حتى
                                        الآن:</span><strong class="text-danger">{{ $statsToday['cancelled'] }}</strong>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Bonuses --}}
                @if($payroll->bonuses->count() > 0)
                    <div class="card border mb-3">
                        <div class="card-header bg-body">
                            <h6 class="mb-0 fw-bold text-success"><i class="ph-duotone ph-gift me-1"></i>البونص</h6>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-sm mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>السبب</th>
                                        <th class="text-center">المبلغ</th>
                                        <th class="text-center">التاريخ</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($payroll->bonuses as $bonus)
                                        <tr>
                                            <td>{{ $bonus->reason }}</td>
                                            <td class="text-center text-success">+ {{ number_format($bonus->amount, 2) }}</td>
                                            <td class="text-center"><small>{{ $bonus->created_at?->format('Y-m-d') }}</small></td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif

                {{-- Deductions --}}
                @if($payroll->deductionsItems->count() > 0)
                    <div class="card border mb-3">
                        <div class="card-header bg-body">
                            <h6 class="mb-0 fw-bold text-danger"><i class="ph-duotone ph-minus-circle me-1"></i>الخصومات</h6>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-sm mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>السبب</th>
                                        <th class="text-center">المبلغ</th>
                                        <th class="text-center">التاريخ</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($payroll->deductionsItems as $deduction)
                                        <tr>
                                            <td>{{ $deduction->reason }}</td>
                                            <td class="text-center text-danger">- {{ number_format($deduction->amount, 2) }}</td>
                                            <td class="text-center"><small>{{ $deduction->created_at?->format('Y-m-d') }}</small>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif

                {{-- Payments --}}
                @if($payroll->payments->count() > 0)
                    <div class="card border">
                        <div class="card-header bg-body">
                            <h6 class="mb-0 fw-bold text-info"><i class="ph-duotone ph-check-circle me-1"></i>سجل الدفعات</h6>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-sm mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>الرقم</th>
                                        <th>الطريقة</th>
                                        <th class="text-center">المبلغ</th>
                                        <th class="text-center">التاريخ</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($payroll->payments as $payment)
                                        <tr>
                                            <td><small>{{ $payment->reference }}</small></td>
                                            <td><small>{{ $payment->payment_method }}</small></td>
                                            <td class="text-center text-info">{{ number_format($payment->amount, 2) }}</td>
                                            <td class="text-center"><small>{{ $payment->paid_at?->format('Y-m-d') }}</small></td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif
            @endif

            {{-- Sessions --}}
            @if($activeTab === 'sessions')
                @if($allSessions->isEmpty())
                    <div class="text-center py-5 text-body-secondary">
                        <i class="ph-duotone ph-calendar-x fs-1 d-block mb-2 opacity-50"></i>
                        <h6>لا توجد حصص</h6>
                    </div>
                @else
                    <div class="d-flex gap-3 flex-wrap small mb-3">
                        <span><span class="badge bg-success me-1">&nbsp;</span> مكتملة</span>
                        <span><span class="badge bg-danger me-1">&nbsp;</span> ملغية</span>
                        <span><span class="badge bg-secondary me-1">&nbsp;</span> لم تُدرّس</span>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-3">#</th>
                                    <th>التاريخ</th>
                                    <th>الوقت</th>
                                    <th>المجموعة</th>
                                    <th>الفرع</th>
                                    <th>المادة</th>
                                    <th class="text-center">الساعات</th>
                                    <th class="text-center">الحالة</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($allSessions as $i => $session)
                                    @php
                                        $rowClass = match ($session['status']) {
                                            'cancelled' => 'session-row-cancelled',
                                            'completed' => 'session-row-completed',
                                            default => 'session-row-upcoming',
                                        };
                                        $badgeClass = match ($session['status']) {
                                            'cancelled' => 'badge bg-danger text-white',
                                            'completed' => 'badge bg-success text-white',
                                            default => 'badge bg-secondary text-white',
                                        };
                                        $statusLabel = match ($session['status']) {
                                            'cancelled' => 'ملغية',
                                            'completed' => 'مكتملة',
                                            default => 'لم تُدرّس',
                                        };
                                    @endphp
                                    <tr class="{{ $rowClass }}" wire:key="session-{{ $i }}">
                                        <td class="ps-3 fw-semibold">{{ $i + 1 }}</td>
                                        <td><strong>{{ $session['date']->format('Y-m-d') }}</strong></td>
                                        <td>{{ $session['start_time'] }} - {{ $session['end_time'] }}</td>
                                        <td>{{ $session['group'] }}</td>
                                        <td>{{ $session['branch'] }}</td>
                                        <td>{{ $session['subject'] }}</td>
                                        <td class="text-center">{{ number_format($session['hours'], 1) }}</td>
                                        <td class="text-center"><span class="{{ $badgeClass }}">{{ $statusLabel }}</span></td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            @endif
        </div>
    </div>

    @once
        @push('styles')
            <style>
                .table .session-row-completed>td {
                    background-color: rgba(25, 135, 84, .12) !important;
                }

                .table-hover .session-row-completed:hover>td {
                    background-color: rgba(25, 135, 84, .2) !important;
                }

                .table .session-row-cancelled>td {
                    background-color: rgba(220, 53, 69, .12) !important;
                }

                .table-hover .session-row-cancelled:hover>td {
                    background-color: rgba(220, 53, 69, .2) !important;
                }

                .table .session-row-upcoming>td {
                    background-color: rgba(108, 117, 125, .12) !important;
                }

                .table-hover .session-row-upcoming:hover>td {
                    background-color: rgba(108, 117, 125, .2) !important;
                }

                [data-bs-theme="dark"] .table .session-row-completed>td {
                    background-color: rgba(25, 135, 84, .25) !important;
                }

                [data-bs-theme="dark"] .table .session-row-cancelled>td {
                    background-color: rgba(220, 53, 69, .25) !important;
                }

                [data-bs-theme="dark"] .table .session-row-upcoming>td {
                    background-color: rgba(108, 117, 125, .25) !important;
                }
            </style>
        @endpush
    @endonce
</div>
