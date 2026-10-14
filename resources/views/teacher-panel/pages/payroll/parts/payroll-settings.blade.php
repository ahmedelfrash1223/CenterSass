<div>
    @livewire('teacher-panel.parts.flash-message', [
        'successKey' => 'payroll-success',
        'errorKey' => 'payroll-error',
    ])

    {{-- General Settings --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-body">
            <h6 class="mb-0 fw-bold"><i class="ph-duotone ph-gear me-1"></i>الإعدادات العامة</h6>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">نوع الأجر <span class="text-danger">*</span></label>
                    <select wire:model.live="payment_type" class="form-select">
                        @foreach($paymentTypes as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-6">
                    <label class="form-label">يوم الصرف في الشهر</label>
                    <input type="number" min="1" max="31" wire:model="pay_day" class="form-control">
                </div>

                @if(in_array($payment_type, ['monthly', 'monthly_plus_per_session']))
                    <div class="col-md-6">
                        <label class="form-label">الراتب الشهري</label>
                        <div class="input-group">
                            <input type="number" step="0.01" min="0" wire:model="monthly_salary" class="form-control">
                            <span class="input-group-text">ج.م</span>
                        </div>
                    </div>
                @endif

                @if(in_array($payment_type, ['per_session', 'monthly_plus_per_session']))
                    <div class="col-md-6">
                        <label class="form-label">سعر الحصة</label>
                        <div class="input-group">
                            <input type="number" step="0.01" min="0" wire:model="per_session_rate" class="form-control">
                            <span class="input-group-text">ج.م</span>
                        </div>
                    </div>
                @endif

                @if($payment_type === 'hourly')
                    <div class="col-md-6">
                        <label class="form-label">سعر الساعة</label>
                        <div class="input-group">
                            <input type="number" step="0.01" min="0" wire:model="hourly_rate" class="form-control">
                            <span class="input-group-text">ج.م</span>
                        </div>
                    </div>
                @endif

                <div class="col-md-6">
                    <label class="form-label">خصم الحصة الملغية</label>
                    <div class="input-group">
                        <input type="number" step="0.01" min="0" wire:model="session_deduction_rate"
                            class="form-control">
                        <span class="input-group-text">ج.م</span>
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-end mt-3">
                <button type="button" wire:click="saveGeneral" class="btn btn-primary" wire:loading.attr="disabled"
                    wire:target="saveGeneral">
                    <span wire:loading.remove wire:target="saveGeneral">
                        <i class="ph-duotone ph-floppy-disk me-1"></i>حفظ الإعدادات العامة
                    </span>
                    <span wire:loading wire:target="saveGeneral">
                        <span class="spinner-border spinner-border-sm me-1"></span>جاري...
                    </span>
                </button>
            </div>
        </div>
    </div>

    {{-- Branch Settings --}}
    @if(count($branchSettings) > 0)
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-body">
                <h6 class="mb-0 fw-bold"><i class="ph-duotone ph-git-branch me-1"></i>إعدادات الفروع (اختياري)</h6>
                <small class="text-body-secondary">لو مدرس بيشتغل في أكتر من فرع بسعر مختلف</small>
            </div>
            <div class="card-body">
                @foreach($branchSettings as $branchId => $data)
                    <div class="card border mb-3" wire:key="branch-{{ $branchId }}">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <h6 class="mb-0 fw-bold">{{ $data['branch_name'] }}</h6>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox"
                                        wire:model.live="branchSettings.{{ $branchId }}.enabled" id="br-{{ $branchId }}">
                                    <label class="form-check-label small" for="br-{{ $branchId }}">
                                        تفعيل إعدادات خاصة
                                    </label>
                                </div>
                            </div>

                            @if($data['enabled'])
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label small">نوع الأجر</label>
                                        <select wire:model="branchSettings.{{ $branchId }}.payment_type"
                                            class="form-select form-select-sm">
                                            @foreach($paymentTypes as $value => $label)
                                                <option value="{{ $value }}">{{ $label }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label small">الراتب الشهري</label>
                                        <input type="number" step="0.01" wire:model="branchSettings.{{ $branchId }}.monthly_salary"
                                            class="form-control form-control-sm">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label small">سعر الحصة</label>
                                        <input type="number" step="0.01"
                                            wire:model="branchSettings.{{ $branchId }}.per_session_rate"
                                            class="form-control form-control-sm">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label small">سعر الساعة</label>
                                        <input type="number" step="0.01" wire:model="branchSettings.{{ $branchId }}.hourly_rate"
                                            class="form-control form-control-sm">
                                    </div>
                                </div>

                                <div class="text-end mt-3">
                                    <button type="button" wire:click="saveBranch({{ $branchId }})" class="btn btn-sm btn-primary"
                                        wire:loading.attr="disabled">
                                        <span wire:loading.remove>حفظ</span>
                                        <span wire:loading><span class="spinner-border spinner-border-sm"></span></span>
                                    </button>
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>
