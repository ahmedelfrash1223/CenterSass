<div>
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <h5 class="mb-4">{{ $editMode ? 'تعديل المجموعة' : 'إضافة مجموعة جديدة' }}</h5>

            @if($editMode && isset($group))
                <div class="info-strip mb-4">
                    <div class="row g-2">
                        <div class="col-md-6">
                            <div class="info-item">
                                <div class="info-icon info-icon-primary">
                                    <i class="ph-duotone ph-user-plus"></i>
                                </div>
                                <div class="info-content">
                                    <small class="info-label">أضيف بواسطة</small>
                                    @if($group->creator)
                                        <span class="badge bg-{{ $group->creator->role->color() }} bg-opacity-10 text-{{ $group->creator->role->color() }}">
                                            <i class="ph-duotone {{ $group->creator->role->icon() }} me-1"></i>
                                            {{ $group->creator->name }}
                                            <small class="ms-1 opacity-75">({{ $group->creator->role->label() }})</small>
                                        </span>
                                    @else
                                        <span class="text-muted small">—</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-item">
                                <div class="info-icon info-icon-info">
                                    <i class="ph-duotone ph-calendar"></i>
                                </div>
                                <div class="info-content">
                                    <small class="info-label">تاريخ الإضافة</small>
                                    <span class="fw-semibold small">{{ $group->created_at?->format('Y-m-d h:i A') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            {{-- ============================================
                 SECTION 1: البيانات الرئيسية
                 ============================================ --}}
            <div class="gs-section">
                <div class="gs-section-header">
                    <div class="gs-section-icon bg-primary bg-opacity-10 text-primary">
                        <i class="ph-duotone ph-info"></i>
                    </div>
                    <div>
                        <h6 class="gs-section-title">البيانات الرئيسية</h6>
                        <p class="gs-section-subtitle">بيانات المجموعة الأساسية</p>
                    </div>
                </div>

                <div class="gs-section-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">اسم المجموعة <span class="text-danger">*</span></label>
                            <input type="text" wire:model="name" class="form-control @error('name') is-invalid @enderror" placeholder="أدخل اسم المجموعة">
                            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">الفرع <span class="text-danger">*</span></label>
                            <select wire:model="branch_id" class="form-select @error('branch_id') is-invalid @enderror">
                                <option value="">اختر الفرع</option>
                                @foreach($branches as $branch)
                                    <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                                @endforeach
                            </select>
                            @error('branch_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">المرحلة الدراسية <span class="text-danger">*</span></label>
                            <select wire:model.live="grade" class="form-select @error('grade') is-invalid @enderror">
                                <option value="">اختر المرحلة</option>
                                @foreach($grades as $value => $label)
                                    <option value="{{ $value }}">{{ $label }}</option>
                                @endforeach
                            </select>
                            @error('grade')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-2 mb-3">
                            <label class="form-label">الحد الأقصى للطلاب</label>
                            <input type="number" min="0" wire:model="max_students" class="form-control @error('max_students') is-invalid @enderror" placeholder="0 = غير محدود">
                            @error('max_students')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-2 mb-3">
                            <label class="form-label">تاريخ البداية</label>
                            <input type="date" wire:model="start_date" class="form-control @error('start_date') is-invalid @enderror">
                            @error('start_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-2 mb-3">
                            <label class="form-label">تاريخ النهاية</label>
                            <input type="date" wire:model="end_date" class="form-control @error('end_date') is-invalid @enderror">
                            @error('end_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">الحالة <span class="text-danger">*</span></label>
                            <div class="d-flex gap-2">
                                <button type="button" wire:click="$set('status', 'active')" class="status-option {{ $status === 'active' ? 'active' : '' }}">
                                    <i class="ph-duotone ph-check-circle"></i>
                                    نشط
                                </button>
                                <button type="button" wire:click="$set('status', 'inactive')" class="status-option {{ $status === 'inactive' ? 'inactive' : '' }}">
                                    <i class="ph-duotone ph-x-circle"></i>
                                    غير نشط
                                </button>
                            </div>
                            @error('status')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-12 mb-3">
                            <label class="form-label">الوصف</label>
                            <textarea rows="3" wire:model="description" class="form-control @error('description') is-invalid @enderror" placeholder="أدخل وصف المجموعة"></textarea>
                            @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </div>
            </div>

            {{-- ============================================
                 SECTION 2: المواد والمواعيد والمدرسين
                 ============================================ --}}
            <div class="gs-section">
                <div class="gs-section-header">
                    <div class="gs-section-icon bg-success bg-opacity-10 text-success">
                        <i class="ph-duotone ph-calendar-check"></i>
                    </div>
                    <div class="flex-grow-1">
                        <h6 class="gs-section-title">المواد والمواعيد والمدرسين</h6>
                        <p class="gs-section-subtitle mb-0">أضف موعد لكل مادة — اختر اليوم والوقت والمدرس</p>
                    </div>
                    <button type="button"
                            class="btn btn-sm btn-primary"
                            wire:click="addScheduleRow"
                        {{ !$grade ? 'disabled' : '' }}>
                        <i class="ph-duotone ph-plus me-1"></i>
                        إضافة موعد
                    </button>
                </div>

                <div class="gs-section-body">
                    @if(!$grade)
                        <div class="alert alert-info d-flex align-items-center gap-2 mb-0">
                            <i class="ph-duotone ph-info fs-5"></i>
                            <span>اختر المرحلة الدراسية أولاً لعرض المواد.</span>
                        </div>
                    @elseif(count($schedules) === 0)
                        <div class="empty-schedules">
                            <i class="ph-duotone ph-calendar-x fs-1 d-block mb-2"></i>
                            <p class="mb-0 small">لا توجد مواعيد. اضغط "إضافة موعد" للبدء.</p>
                        </div>
                    @else
                        @foreach($schedules as $index => $schedule)
                            @php
                                $minutes = $this->computeScheduleMinutes($schedule);
                                $hoursLabel = $minutes !== null ? $this->formatMinutes($minutes) : null;
                            @endphp

                            <div class="schedule-row {{ $hoursLabel ? 'is-valid' : 'is-incomplete' }}"
                                 wire:key="schedule-row-{{ $index }}">

                                <div class="schedule-row-header">
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="schedule-row-number">{{ $index + 1 }}</span>
                                        <span class="schedule-row-title">
                                            @if($schedule['day'] && $schedule['subject_id'])
                                                {{ $days[$schedule['day']] ?? $schedule['day'] }}
                                                -
                                                {{ $teacherSubjects->firstWhere('id', (int) $schedule['subject_id'])?->subject_label ?? 'مادة' }}
                                            @else
                                                موعد جديد
                                            @endif
                                        </span>
                                    </div>

                                    <div class="d-flex align-items-center gap-2">
                                        @if($hoursLabel)
                                            <span class="badge bg-success bg-opacity-10 text-success">
                                                <i class="ph-duotone ph-clock me-1"></i>
                                                {{ $hoursLabel }}
                                            </span>
                                        @endif

                                        <button type="button"
                                                wire:click="removeScheduleRow({{ $index }})"
                                                class="btn-remove-schedule"
                                                title="حذف الموعد">
                                            <i class="ph-duotone ph-trash"></i>
                                        </button>
                                    </div>
                                </div>

                                <div class="schedule-row-body">
                                    <div class="row g-2">
                                        <div class="col-md-2">
                                            <label class="schedule-label">اليوم</label>
                                            <select wire:model.live="schedules.{{ $index }}.day"
                                                    class="form-select form-select-sm @error('schedules.'.$index.'.day') is-invalid @enderror">
                                                <option value="">اختر اليوم</option>
                                                @foreach($days as $key => $label)
                                                    <option value="{{ $key }}">{{ $label }}</option>
                                                @endforeach
                                            </select>
                                            @error('schedules.'.$index.'.day')
                                            <div class="schedule-error">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="col-md-2">
                                            <label class="schedule-label">من</label>
                                            <input type="time"
                                                   wire:model.live="schedules.{{ $index }}.start_time"
                                                   class="form-control form-control-sm @error('schedules.'.$index.'.start_time') is-invalid @enderror">
                                            @error('schedules.'.$index.'.start_time')
                                            <div class="schedule-error">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="col-md-2">
                                            <label class="schedule-label">إلى</label>
                                            <input type="time"
                                                   wire:model.live="schedules.{{ $index }}.end_time"
                                                   class="form-control form-control-sm @error('schedules.'.$index.'.end_time') is-invalid @enderror">
                                            @error('schedules.'.$index.'.end_time')
                                            <div class="schedule-error">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="col-md-3">
                                            <label class="schedule-label">المادة</label>
                                            <select wire:model.live="schedules.{{ $index }}.subject_id"
                                                    class="form-select form-select-sm @error('schedules.'.$index.'.subject_id') is-invalid @enderror">
                                                <option value="">اختر المادة</option>
                                                @foreach($teacherSubjects as $subject)
                                                    <option value="{{ $subject->id }}">
                                                        {{ $subject->subject_label }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('schedules.'.$index.'.subject_id')
                                            <div class="schedule-error">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="col-md-3">
                                            <label class="schedule-label">المدرس</label>
                                            <select wire:model="schedules.{{ $index }}.teacher_id"
                                                    class="form-select form-select-sm @error('schedules.'.$index.'.teacher_id') is-invalid @enderror">
                                                <option value="">اختر المدرس</option>
                                                @foreach($teachers as $teacher)
                                                    <option value="{{ $teacher->id }}">
                                                        {{ $teacher->name }} ({{ $teacher->role->label() }})
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('schedules.'.$index.'.teacher_id')
                                            <div class="schedule-error">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                            </div>
                        @endforeach
                    @endif
                </div>
            </div>

            {{-- ============================================
                 SECTION 3: المالية
                 ============================================ --}}
            <div class="gs-section">
                <div class="gs-section-header">
                    <div class="gs-section-icon bg-warning bg-opacity-10 text-warning">
                        <i class="ph-duotone ph-currency-circle-dollar"></i>
                    </div>
                    <div class="flex-grow-1">
                        <h6 class="gs-section-title">تفاصيل الدفع</h6>
                        <p class="gs-section-subtitle mb-0">حدد طريقة تحصيل المصروفات لهذه المجموعة</p>
                    </div>
                    <div class="form-check form-switch mb-0">
                        <input class="form-check-input"
                               type="checkbox"
                               id="has-pricing"
                               wire:model.live="has_pricing">
                        <label class="form-check-label small fw-semibold" for="has-pricing">
                            <i class="ph-duotone ph-power me-1"></i>
                            مُفعّل
                        </label>
                    </div>
                </div>

                <div class="gs-section-body">
                    @if(!$has_pricing)
                        <div class="alert alert-info d-flex align-items-center gap-2 mb-0">
                            <i class="ph-duotone ph-info fs-5"></i>
                            <span>المجموعة دي مجانية — مش هيتولّد لها فواتير.</span>
                        </div>
                    @else
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">طريقة التسعير <span class="text-danger">*</span></label>
                                <select wire:model.live="pricing_type" class="form-select @error('pricing_type') is-invalid @enderror">
                                    @foreach(\App\Enums\Teacher\PricingTypeEnum::cases() as $type)
                                        <option value="{{ $type->value }}">{{ $type->label() }}</option>
                                    @endforeach
                                </select>
                                @error('pricing_type')<div class="invalid-feedback">{{ $message }}</div>@enderror

                                @php
                                    $selectedType = \App\Enums\Teacher\PricingTypeEnum::tryFrom($pricing_type);
                                @endphp
                                @if($selectedType)
                                    <small class="text-muted d-block mt-2">
                                        <i class="ph-duotone ph-info me-1"></i>
                                        {{ $selectedType->description() }}
                                    </small>
                                @endif
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">
                                    المبلغ <span class="text-danger">*</span>
                                    <small class="text-muted">
                                        (ج.م @if($selectedType) {{ $selectedType->label() }} @endif)
                                    </small>
                                </label>
                                <input type="number"
                                       step="0.01"
                                       min="0"
                                       wire:model.live="pricing_amount"
                                       class="form-control @error('pricing_amount') is-invalid @enderror"
                                       placeholder="0.00">
                                @error('pricing_amount')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-12">
                                <div class="pricing-preview">
                                    <div class="pricing-preview-icon">
                                        <i class="ph-duotone ph-calculator"></i>
                                    </div>
                                    <div class="pricing-preview-content">
                                        <small class="text-muted d-block">مثال توضيحي:</small>
                                        <p class="mb-0 small">
                                            @if($selectedType && (float) $pricing_amount > 0)
                                                @switch($pricing_type)
                                                    @case('fixed_monthly')
                                                        الطالب هيدفع <strong>{{ number_format((float) $pricing_amount, 2) }} ج.م</strong> شهرياً
                                                        @break
                                                    @case('per_session')
                                                        لو حضر 8 حصص → <strong>{{ number_format((float) $pricing_amount * 8, 2) }} ج.م</strong> في الشهر
                                                        @break
                                                    @case('per_subject')
                                                        لو عنده 3 مواد → <strong>{{ number_format((float) $pricing_amount * 3, 2) }} ج.م</strong> في الشهر
                                                        @break
                                                    @case('per_grade')
                                                        حسب المرحلة — <strong>{{ number_format((float) $pricing_amount, 2) }} ج.م</strong> شهرياً
                                                        @break
                                                @endswitch
                                            @else
                                                أدخل المبلغ لعرض المثال
                                            @endif
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Form Actions --}}
            <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
                @if($editMode)
                    <button type="button" class="btn btn-primary" wire:click="update" wire:loading.attr="disabled" wire:target="update">
                        <span wire:loading.remove wire:target="update">
                            <i class="ph-duotone ph-floppy-disk me-1"></i>حفظ التعديلات
                        </span>
                        <span wire:loading wire:target="update">
                            <span class="spinner-border spinner-border-sm me-1"></span>جاري الحفظ...
                        </span>
                    </button>
                @else
                    <button type="button" class="btn btn-primary" wire:click="save(false)" wire:loading.attr="disabled" wire:target="save(false)">
                        <span wire:loading.remove wire:target="save(false)">
                            <i class="ph-duotone ph-floppy-disk me-1"></i>حفظ
                        </span>
                        <span wire:loading wire:target="save(false)">
                            <span class="spinner-border spinner-border-sm me-1"></span>جاري الحفظ...
                        </span>
                    </button>
                    <button type="button" class="btn btn-success" wire:click="save(true)" wire:loading.attr="disabled" wire:target="save(true)">
                        <span wire:loading.remove wire:target="save(true)">
                            <i class="ph-duotone ph-plus-circle me-1"></i>حفظ وإنشاء جديد
                        </span>
                        <span wire:loading wire:target="save(true)">
                            <span class="spinner-border spinner-border-sm me-1"></span>جاري الحفظ...
                        </span>
                    </button>
                @endif
                <a href="{{ route('teacher.groups.index') }}" wire:navigate class="btn btn-light border">
                    <i class="ph-duotone ph-x-circle me-1"></i>إلغاء
                </a>
            </div>
        </div>
    </div>

    {{-- Duplicate Confirmation Modal --}}
    @if($showDuplicateConfirm)
        <div class="gdm-backdrop" wire:click="cancelDuplicate"></div>
        <div class="gdm-wrapper">
            <div class="gdm-content">
                <div class="gdm-icon">
                    <i class="ph-duotone ph-warning-circle"></i>
                </div>
                <h4 class="gdm-title">تنبيه — المادة مضافة بالفعل</h4>
                <p class="gdm-text">
                    المادة دي <strong>مضافة بالفعل</strong> في نفس اليوم.
                    <br>
                    هل أنت متأكد من إضافتها مرة تانية؟
                </p>
                <div class="gdm-actions">
                    <button type="button" wire:click="cancelDuplicate" class="gdm-btn gdm-btn-cancel">
                        <i class="ph-duotone ph-x me-1"></i>
                        لا، الغيها
                    </button>
                    <button type="button" wire:click="confirmDuplicate" class="gdm-btn gdm-btn-confirm">
                        <i class="ph-duotone ph-check me-1"></i>
                        نعم، ضيفها
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
