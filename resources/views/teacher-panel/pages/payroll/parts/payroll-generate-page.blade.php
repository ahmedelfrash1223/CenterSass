<div>
    @livewire('teacher-panel.parts.flash-message', [
        'successKey' => 'payroll-success',
        'errorKey' => 'payroll-error',
    ])

    <div class="row g-3">
        {{-- Form Column --}}
        <div class="col-lg-4 col-xl-3">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-primary-subtle border-0">
                    <h6 class="mb-0 fw-bold text-primary-emphasis">
                        <i class="ph-duotone ph-calendar me-1"></i>بيانات التوليد
                    </h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">الشهر <span class="text-danger">*</span></label>
                        <input type="month" wire:model.live="month" class="form-control">
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold">الفرع <span class="text-danger">*</span></label>
                        <select wire:model.live="branchId" class="form-select">
                            <option value="">— اختر الفرع —</option>
                            @foreach($branches as $branch)
                                <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold">نوع الموظف <span class="text-danger">*</span></label>
                        <div class="btn-group w-100" role="group">
                            <input type="radio" class="btn-check" name="roleFilter" id="roleStaff"
                                   wire:model.live="roleFilter" value="staff">
                            <label class="btn btn-outline-primary btn-sm" for="roleStaff">
                                <i class="ph-duotone ph-chalkboard-teacher me-1"></i>مدرس
                            </label>
                            <input type="radio" class="btn-check" name="roleFilter" id="roleAssistant"
                                   wire:model.live="roleFilter" value="assistant">
                            <label class="btn btn-outline-primary btn-sm" for="roleAssistant">
                                <i class="ph-duotone ph-user-circle me-1"></i>مساعد
                            </label>
                        </div>
                    </div>

                    <div class="d-flex align-items-center justify-content-between p-2 bg-body-secondary rounded mb-3">
                        <span class="small fw-semibold">
                            <i class="ph-duotone ph-users-three me-1"></i>المختارين
                        </span>
                        <span class="badge bg-primary text-white">{{ count($selectedTeacherIds) }}</span>
                    </div>

                    <button type="button" wire:click="generate" class="btn btn-primary w-100"
                            wire:loading.attr="disabled" wire:target="generate"
                            @if(empty($selectedTeacherIds) || $branchId === '') disabled @endif>
                        <span wire:loading.remove wire:target="generate">
                            <i class="ph-duotone ph-check me-1"></i>توليد الكشوف
                        </span>
                        <span wire:loading wire:target="generate">
                            <span class="spinner-border spinner-border-sm me-1"></span>جاري التوليد...
                        </span>
                    </button>

                    @if(!empty($results))
                        <div class="alert alert-info mt-3 mb-0 small">
                            <div class="d-flex align-items-center gap-2 mb-1">
                                <i class="ph-duotone ph-check-circle text-success fs-5"></i>
                                <strong>تم توليد {{ $results['created'] }} كشف</strong>
                            </div>
                            @if($results['skipped'] > 0)
                                <div class="text-body-secondary">تم تخطي {{ $results['skipped'] }}</div>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Teachers List --}}
        <div class="col-lg-8 col-xl-9">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-body d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <div>
                        <h6 class="mb-0 fw-bold">
                            <i class="ph-duotone ph-users-four me-1"></i>
                            اختر {{ $roleFilter === 'assistant' ? 'المساعدين' : 'المدرسين' }}
                        </h6>
                        <small class="text-body-secondary">
                            @if($branchId === '')
                                <span class="text-danger">اختر الفرع أولاً</span>
                            @else
                                {{ $teachers->count() }} متاح
                            @endif
                        </small>
                    </div>
                    @if($teachers->count() > 0)
                        <div class="d-flex gap-2">
                            <button type="button" wire:click="selectAllTeachers" class="btn btn-sm btn-outline-primary">
                                <i class="ph-duotone ph-check-square me-1"></i>تحديد الكل
                            </button>
                            <button type="button" wire:click="clearTeachers" class="btn btn-sm btn-outline-secondary">
                                <i class="ph-duotone ph-x-square me-1"></i>إلغاء
                            </button>
                        </div>
                    @endif
                </div>

                <div class="card-body p-0">
                    @if($branchId === '')
                        <div class="text-center py-5 text-body-secondary">
                            <i class="ph-duotone ph-git-branch fs-1 d-block mb-2 opacity-50"></i>
                            <h6 class="mb-1">اختر الفرع</h6>
                        </div>
                    @elseif($teachers->isEmpty())
                        <div class="text-center py-5 text-body-secondary">
                            <i class="ph-duotone ph-user-minus fs-1 d-block mb-2 opacity-50"></i>
                            <h6 class="mb-1">لا يوجد {{ $roleFilter === 'assistant' ? 'مساعدين' : 'مدرسين' }} متاحين</h6>
                            <small>كلهم اتعملهم كشوف في {{ $month }} أو مش مضافين للفرع</small>
                        </div>
                    @else
                        <div class="p-3" style="max-height: 700px; overflow-y: auto;">
                            @foreach($teachers as $teacher)
                                @php
                                    $isSelected = in_array((int) $teacher->id, array_map('intval', $selectedTeacherIds));
                                    $hasSettings = in_array((int) $teacher->id, $withSettings);
                                    $isOpenSettings = $openSettingsTeacherId === (int) $teacher->id;
                                @endphp

                                <div class="card border mb-2 {{ $isSelected ? 'border-primary' : '' }}"
                                     wire:key="tch-{{ $teacher->id }}">

                                    {{-- Row --}}
                                    <div class="card-body p-3 d-flex align-items-center gap-2">
                                        <input type="checkbox"
                                               wire:model.live="selectedTeacherIds"
                                               value="{{ $teacher->id }}"
                                               class="form-check-input m-0"
                                               id="tch-{{ $teacher->id }}"
                                               style="width: 20px; height: 20px;">

                                        <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center fw-bold flex-shrink-0"
                                             style="width: 40px; height: 40px; font-size: 15px;">
                                            {{ mb_substr($teacher->name, 0, 1) }}
                                        </div>

                                        <label for="tch-{{ $teacher->id }}"
                                               class="flex-grow-1 min-w-0 mb-0"
                                               style="cursor: pointer;">
                                            <div class="fw-semibold text-truncate">{{ $teacher->name }}</div>
                                            <small class="text-body-secondary">{{ $teacher->role?->label() ?? '—' }}</small>
                                        </label>

                                        @if($hasSettings)
                                            <span class="badge bg-success-subtle text-success-emphasis">
                                                <i class="ph-duotone ph-check-circle me-1"></i>إعدادات محفوظة
                                            </span>
                                        @else
                                            <span class="badge bg-warning-subtle text-warning-emphasis">
                                                <i class="ph-duotone ph-warning-circle me-1"></i>مفيش إعدادات
                                            </span>
                                        @endif

                                        <button type="button"
                                                wire:click="{{ $isOpenSettings ? 'closeSettings' : 'openSettingsFor' }}({{ $teacher->id }})"
                                                class="btn btn-sm btn-{{ $isOpenSettings ? 'dark' : 'outline-dark' }}"
                                                title="إعدادات الأجر">
                                            <i class="ph-duotone ph-{{ $isOpenSettings ? 'caret-up' : 'gear' }}"></i>
                                        </button>
                                    </div>

                                    {{-- Inline Settings --}}
                                    @if($isOpenSettings)
                                        <div class="card-body border-top bg-body-tertiary">
                                            <div class="row g-3">
                                                <div class="col-md-6">
                                                    <label class="form-label small fw-semibold">نوع الأجر</label>
                                                    <select wire:model.live="settingsPaymentType" class="form-select form-select-sm">
                                                        @foreach($paymentTypes as $value => $label)
                                                            <option value="{{ $value }}">{{ $label }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>

                                                @if(in_array($settingsPaymentType, ['monthly', 'monthly_plus_per_session']))
                                                    <div class="col-md-6">
                                                        <label class="form-label small fw-semibold">الراتب الشهري</label>
                                                        <div class="input-group input-group-sm">
                                                            <input type="number" step="0.01" min="0"
                                                                   wire:model="settingsMonthlySalary"
                                                                   class="form-control">
                                                            <span class="input-group-text">ج.م</span>
                                                        </div>
                                                    </div>
                                                @endif

                                                @if(in_array($settingsPaymentType, ['per_session', 'monthly_plus_per_session']))
                                                    <div class="col-md-6">
                                                        <label class="form-label small fw-semibold">سعر الحصة</label>
                                                        <div class="input-group input-group-sm">
                                                            <input type="number" step="0.01" min="0"
                                                                   wire:model="settingsPerSessionRate"
                                                                   class="form-control">
                                                            <span class="input-group-text">ج.م</span>
                                                        </div>
                                                    </div>
                                                @endif

                                                @if($settingsPaymentType === 'hourly')
                                                    <div class="col-md-6">
                                                        <label class="form-label small fw-semibold">سعر الساعة</label>
                                                        <div class="input-group input-group-sm">
                                                            <input type="number" step="0.01" min="0"
                                                                   wire:model="settingsHourlyRate"
                                                                   class="form-control">
                                                            <span class="input-group-text">ج.م</span>
                                                        </div>
                                                    </div>
                                                @endif

                                                <div class="col-md-6">
                                                    <label class="form-label small fw-semibold">خصم الحصة الملغية</label>
                                                    <div class="input-group input-group-sm">
                                                        <input type="number" step="0.01" min="0"
                                                               wire:model="settingsSessionDeductionRate"
                                                               class="form-control">
                                                        <span class="input-group-text">ج.م</span>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="d-flex justify-content-end gap-2 mt-3">
                                                <button type="button"
                                                        wire:click="saveSettings"
                                                        class="btn btn-sm btn-dark"
                                                        wire:loading.attr="disabled"
                                                        wire:target="saveSettings">
                                                    <span wire:loading.remove wire:target="saveSettings">
                                                        <i class="ph-duotone ph-floppy-disk me-1"></i>حفظ
                                                    </span>
                                                    <span wire:loading wire:target="saveSettings">
                                                        <span class="spinner-border spinner-border-sm me-1"></span>جاري...
                                                    </span>
                                                </button>
                                                <button type="button"
                                                        wire:click="closeSettings"
                                                        class="btn btn-sm btn-outline-secondary">
                                                    إلغاء
                                                </button>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
