<div>
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <h5 class="mb-4">{{ $editMode ? 'تعديل المجموعة' : 'إضافة مجموعة جديدة' }}</h5>

            @if($editMode && isset($group))
                <div class="alert alert-light border mb-4">
                    <div class="row g-2">
                        <div class="col-md-6">
                            <div class="d-flex align-items-center gap-2">
                                <div class="rounded-3 d-flex align-items-center justify-content-center bg-primary-subtle text-primary"
                                     style="width: 40px; height: 40px;">
                                    <i class="ph-duotone ph-user-plus"></i>
                                </div>
                                <div>
                                    <small class="text-body-secondary d-block">أضيف بواسطة</small>
                                    @if($group->creator)
                                        <span class="badge bg-{{ $group->creator->role->color() }}-subtle text-{{ $group->creator->role->color() }}-emphasis">
                                            <i class="ph-duotone {{ $group->creator->role->icon() }} me-1"></i>
                                            {{ $group->creator->name }}
                                        </span>
                                    @else
                                        <span class="text-body-secondary small">—</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex align-items-center gap-2">
                                <div class="rounded-3 d-flex align-items-center justify-content-center bg-info-subtle text-info"
                                     style="width: 40px; height: 40px;">
                                    <i class="ph-duotone ph-calendar"></i>
                                </div>
                                <div>
                                    <small class="text-body-secondary d-block">تاريخ الإضافة</small>
                                    <span class="fw-semibold small">{{ $group->created_at?->format('Y-m-d h:i A') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif


            <div class="card border mb-3">
                <div class="card-header bg-body d-flex align-items-center justify-content-between"
                     style="cursor: pointer;"
                     wire:click="toggleSection('sectionBasic')">
                    <div class="d-flex align-items-center gap-2">
                        <div class="rounded-3 d-flex align-items-center justify-content-center bg-primary-subtle text-primary"
                             style="width: 40px; height: 40px;">
                            <i class="ph-duotone ph-info"></i>
                        </div>
                        <div>
                            <h6 class="mb-0 fw-bold">البيانات الرئيسية</h6>
                            <small class="text-body-secondary">بيانات المجموعة الأساسية</small>
                        </div>
                    </div>
                    <i class="ph-duotone {{ $sectionBasic ? 'ph-caret-up' : 'ph-caret-down' }}"></i>
                </div>

                @if($sectionBasic)
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">اسم المجموعة <span class="text-danger">*</span></label>
                                <input type="text" wire:model="name"
                                       class="form-control @error('name') is-invalid @enderror"
                                       placeholder="أدخل اسم المجموعة">
                                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">الفرع <span class="text-danger">*</span></label>
                                <select wire:model="branch_id"
                                        class="form-select @error('branch_id') is-invalid @enderror">
                                    <option value="">اختر الفرع</option>
                                    @foreach($branches as $branch)
                                        <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                                    @endforeach
                                </select>
                                @error('branch_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">المرحلة الدراسية <span class="text-danger">*</span></label>
                                <select wire:model.live="grade"
                                        class="form-select @error('grade') is-invalid @enderror">
                                    <option value="">اختر المرحلة</option>
                                    @foreach($grades as $value => $label)
                                        <option value="{{ $value }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                                @error('grade')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                <small class="text-body-secondary d-block mt-1">
                                    <i class="ph-duotone ph-info me-1"></i>
                                    لازم تختار المرحلة الأول عشان تقدر تضيف مواعيد
                                </small>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">الحد الأقصى للطلاب</label>
                                <input type="number" min="0" wire:model="max_students"
                                       class="form-control @error('max_students') is-invalid @enderror"
                                       placeholder="0 = غير محدود">
                                @error('max_students')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-3 mb-3">
                                <label class="form-label">تاريخ البداية</label>
                                <input type="date" wire:model="start_date"
                                       class="form-control @error('start_date') is-invalid @enderror">
                                @error('start_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-3 mb-3">
                                <label class="form-label">تاريخ النهاية</label>
                                <input type="date" wire:model="end_date"
                                       class="form-control @error('end_date') is-invalid @enderror">
                                @error('end_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">الحالة <span class="text-danger">*</span></label>
                                <div class="d-flex gap-2">
                                    <button type="button" wire:click="$set('status', 'active')"
                                            class="status-option {{ $status === 'active' ? 'active' : '' }}">
                                        <i class="ph-duotone ph-check-circle"></i>
                                        نشط
                                    </button>
                                    <button type="button" wire:click="$set('status', 'inactive')"
                                            class="status-option {{ $status === 'inactive' ? 'inactive' : '' }}">
                                        <i class="ph-duotone ph-x-circle"></i>
                                        غير نشط
                                    </button>
                                </div>
                                @error('status')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-12 mb-3">
                                <label class="form-label">الوصف</label>
                                <textarea rows="3" wire:model="description"
                                          class="form-control @error('description') is-invalid @enderror"
                                          placeholder="أدخل وصف المجموعة"></textarea>
                                @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                    </div>
                @endif
            </div>


            <div class="card border mb-3">
                <div class="card-header bg-body d-flex align-items-center justify-content-between"
                     style="cursor: pointer;"
                     wire:click="toggleSection('sectionSchedule')">
                    <div class="d-flex align-items-center gap-2">
                        <div class="rounded-3 d-flex align-items-center justify-content-center bg-success-subtle text-success"
                             style="width: 40px; height: 40px;">
                            <i class="ph-duotone ph-calendar-check"></i>
                        </div>
                        <div>
                            <h6 class="mb-0 fw-bold">المواعيد والمواد والمدرسين</h6>
                            <small class="text-body-secondary">اضبط مواعيد الحصص الأسبوعية</small>
                        </div>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        @if(count($schedules) > 0)
                            <span class="badge bg-success">{{ count($schedules) }} موعد</span>
                        @endif
                        <i class="ph-duotone {{ $sectionSchedule ? 'ph-caret-up' : 'ph-caret-down' }}"></i>
                    </div>
                </div>

                @if($sectionSchedule)
                    <div class="card-body">

                        @if(!$grade)
                            <div class="alert alert-info d-flex align-items-center gap-2 mb-0">
                                <i class="ph-duotone ph-info fs-5"></i>
                                <span>اختر المرحلة الدراسية من القسم الأول الأول.</span>
                            </div>
                        @else

                            @if($showDuplicateConfirm)
                                <div class="alert alert-warning d-flex align-items-center gap-3 mb-3">
                                    <i class="ph-duotone ph-warning-circle fs-3"></i>
                                    <div class="flex-grow-1">
                                        <strong>المادة دي مضافة بالفعل في نفس اليوم</strong>
                                        <p class="small mb-0">متأكد إنك عايز تضيفها تاني؟</p>
                                    </div>
                                    <div class="d-flex gap-2">
                                        <button type="button" wire:click="confirmDuplicate" class="btn btn-sm btn-warning">
                                            <i class="ph-duotone ph-check me-1"></i>نعم، ضيفها
                                        </button>
                                        <button type="button" wire:click="cancelDuplicate" class="btn btn-sm btn-outline-secondary">
                                            إلغاء
                                        </button>
                                    </div>
                                </div>
                            @endif

                            <div class="d-flex justify-content-end mb-3">
                                <button type="button" wire:click="addScheduleRow" class="btn btn-primary btn-sm">
                                    <i class="ph-duotone ph-plus me-1"></i>
                                    إضافة موعد جديد
                                </button>
                            </div>

                            <div id="schedules-container">
                                @foreach($schedules as $index => $schedule)
                                    @php
                                        $minutes = $this->computeScheduleMinutes($schedule);
                                        $hoursLabel = $minutes !== null ? $this->formatMinutes($minutes) : null;
                                        $teacherSubjects = $schedule['teacher_id']
                                            ? $this->subjectsForTeacher((int) $schedule['teacher_id'])
                                            : [];
                                        $uid = $schedule['uid'] ?? 'sch_' . $index;
                                    @endphp

                                    <div class="card border mb-3 schedule-card"
                                         data-index="{{ $index }}"
                                         data-uid="{{ $uid }}">

                                        <div class="card-header bg-body d-flex align-items-center justify-content-between">
                                            <div class="d-flex align-items-center gap-2">
                                                <span class="drag-handle" style="cursor: grab;">
                                                    <i class="ph-duotone ph-dots-six-vertical fs-5 text-body-secondary"></i>
                                                </span>

                                                <span class="badge bg-primary text-white">
                                                    موعد {{ $index + 1 }}
                                                </span>

                                                @if($hoursLabel)
                                                    <span class="badge bg-success text-white">
                                                        <i class="ph-duotone ph-clock me-1"></i>
                                                        {{ $hoursLabel }}
                                                    </span>
                                                @endif

                                                @if(!empty($schedule['days']))
                                                    <span class="badge bg-info text-white">
                                                        {{ count($schedule['days']) }} أيام
                                                    </span>
                                                @endif
                                            </div>

                                            <div class="d-flex gap-2">
                                                <button type="button"
                                                        wire:click="duplicateScheduleRow({{ $index }})"
                                                        class="btn btn-sm btn-outline-primary"
                                                        title="تكرار الموعد">
                                                    <i class="ph-duotone ph-copy"></i>
                                                </button>

                                                <button type="button"
                                                        wire:click="removeScheduleRow({{ $index }})"
                                                        class="btn btn-sm btn-outline-danger"
                                                        title="حذف الموعد">
                                                    <i class="ph-duotone ph-trash"></i>
                                                </button>
                                            </div>
                                        </div>

                                        <div class="card-body">
                                            <div class="mb-3">
                                                <div class="d-flex align-items-center justify-content-between mb-2 flex-wrap gap-2">
                                                    <label class="form-label small fw-semibold mb-0">
                                                        <i class="ph-duotone ph-calendar-blank me-1"></i>
                                                        الأيام
                                                        @error('schedules.'.$index.'.days')
                                                        <span class="text-danger small">— {{ $message }}</span>
                                                        @enderror
                                                    </label>
                                                    <div class="d-flex gap-2">
                                                        <button type="button" wire:click="selectAllDays({{ $index }})"
                                                                class="btn btn-sm btn-outline-success">
                                                            كل الأيام
                                                        </button>
                                                        <button type="button" wire:click="deselectAllDays({{ $index }})"
                                                                class="btn btn-sm btn-outline-secondary">
                                                            إلغاء
                                                        </button>
                                                    </div>
                                                </div>
                                                <div class="d-flex flex-wrap gap-2">
                                                    @foreach($days as $dayKey => $dayLabel)
                                                        @php $isChecked = in_array($dayKey, $schedule['days'] ?? []); @endphp
                                                        <div>
                                                            <input type="checkbox"
                                                                   class="btn-check"
                                                                   id="day-{{ $uid }}-{{ $dayKey }}"
                                                                   wire:model.live="schedules.{{ $index }}.days"
                                                                   value="{{ $dayKey }}"
                                                                {{ $isChecked ? 'checked' : '' }}>
                                                            <label class="btn btn-sm {{ $isChecked ? 'btn-primary' : 'btn-outline-secondary' }}"
                                                                   for="day-{{ $uid }}-{{ $dayKey }}">
                                                                {{ $dayLabel }}
                                                            </label>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>

                                            <div class="row g-3">
                                                <div class="col-md-6">
                                                    <label class="form-label small fw-semibold">
                                                        <i class="ph-duotone ph-chalkboard-teacher me-1"></i>المدرس
                                                    </label>
                                                    <select wire:model.live="schedules.{{ $index }}.teacher_id"
                                                            class="form-select form-select-sm @error('schedules.'.$index.'.teacher_id') is-invalid @enderror">
                                                        <option value="">— اختر المدرس —</option>
                                                        @foreach($availableTeachers as $teacher)
                                                            <option value="{{ $teacher['id'] }}">{{ $teacher['name'] }}</option>
                                                        @endforeach
                                                    </select>
                                                    @error('schedules.'.$index.'.teacher_id')
                                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                                    @enderror
                                                </div>

                                                <div class="col-md-6">
                                                    <label class="form-label small fw-semibold">
                                                        <i class="ph-duotone ph-notebook me-1"></i>المادة
                                                    </label>
                                                    <select wire:model.live="schedules.{{ $index }}.subject_id"
                                                            class="form-select form-select-sm @error('schedules.'.$index.'.subject_id') is-invalid @enderror"
                                                            @if(empty($schedule['teacher_id'])) disabled @endif>
                                                        <option value="">
                                                            {{ empty($schedule['teacher_id']) ? '— اختر المدرس الأول —' : '— اختر المادة —' }}
                                                        </option>
                                                        @foreach($teacherSubjects as $subject)
                                                            <option value="{{ $subject['id'] }}">{{ $subject['label'] }}</option>
                                                        @endforeach
                                                    </select>
                                                    @error('schedules.'.$index.'.subject_id')
                                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                                    @enderror
                                                </div>

                                                <div class="col-md-6">
                                                    <label class="form-label small fw-semibold">
                                                        <i class="ph-duotone ph-play me-1"></i>وقت بداية الحصة
                                                    </label>
                                                    <div class="alert alert-info py-2 px-3 mb-2 small d-flex align-items-center gap-2">
                                                        <i class="ph-duotone ph-info fs-5"></i>
                                                        <span>اختر الساعة والدقيقة اللي بتبدأ فيها الحصة من القوائم تحت</span>
                                                    </div>
                                                    <div class="input-group input-group-sm">
                                                        <span class="input-group-text"><i class="ph-duotone ph-clock"></i></span>
                                                        <select wire:model.live="schedules.{{ $index }}.start_hour"
                                                                class="form-select form-select-sm">
                                                            <option value="">ساعة البداية</option>
                                                            @foreach($hourOptions as $h)
                                                                <option value="{{ $h }}">{{ $h }}</option>
                                                            @endforeach
                                                        </select>
                                                        <select wire:model.live="schedules.{{ $index }}.start_min"
                                                                class="form-select form-select-sm">
                                                            @foreach($minuteOptions as $m)
                                                                <option value="{{ $m }}">{{ $m }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <label class="form-label small fw-semibold">
                                                        <i class="ph-duotone ph-stop me-1"></i>وقت نهاية الحصة
                                                    </label>
                                                    <div class="alert alert-info py-2 px-3 mb-2 small d-flex align-items-center gap-2">
                                                        <i class="ph-duotone ph-info fs-5"></i>
                                                        <span>اختر الساعة والدقيقة اللي بتنتهي فيها الحصة من القوائم تحت</span>
                                                    </div>
                                                    <div class="input-group input-group-sm">
                                                        <span class="input-group-text"><i class="ph-duotone ph-clock"></i></span>
                                                        <select wire:model.live="schedules.{{ $index }}.end_hour"
                                                                class="form-select form-select-sm">
                                                            <option value="">ساعة النهاية</option>
                                                            @foreach($hourOptions as $h)
                                                                <option value="{{ $h }}">{{ $h }}</option>
                                                            @endforeach
                                                        </select>
                                                        <select wire:model.live="schedules.{{ $index }}.end_min"
                                                                class="form-select form-select-sm">
                                                            @foreach($minuteOptions as $m)
                                                                <option value="{{ $m }}">{{ $m }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    @error('schedules.'.$index.'.end_time')
                                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                @endif
            </div>

            <div class="card border mb-3">
                <div class="card-header bg-body d-flex align-items-center justify-content-between"
                     style="cursor: pointer;"
                     wire:click="toggleSection('sectionPricing')">
                    <div class="d-flex align-items-center gap-2">
                        <div class="rounded-3 d-flex align-items-center justify-content-center bg-warning-subtle text-warning"
                             style="width: 40px; height: 40px;">
                            <i class="ph-duotone ph-currency-circle-dollar"></i>
                        </div>
                        <div>
                            <h6 class="mb-0 fw-bold">تفاصيل الدفع</h6>
                            <small class="text-body-secondary">حدد طريقة تحصيل المصروفات</small>
                        </div>
                    </div>
                    <i class="ph-duotone {{ $sectionPricing ? 'ph-caret-up' : 'ph-caret-down' }}"></i>
                </div>

                @if($sectionPricing)
                    <div class="card-body">
                        <div class="d-flex justify-content-end mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="has-pricing" wire:model.live="has_pricing">
                                <label class="form-check-label small fw-semibold" for="has-pricing">
                                    <i class="ph-duotone ph-power me-1"></i> مُفعّل
                                </label>
                            </div>
                        </div>

                        @if(!$has_pricing)
                            <div class="alert alert-info d-flex align-items-center gap-2 mb-0">
                                <i class="ph-duotone ph-info fs-5"></i>
                                <span>المجموعة دي مجانية — مش هيتولّد لها فواتير.</span>
                            </div>
                        @else
                            @php $selectedType = \App\Enums\Teacher\PricingTypeEnum::tryFrom($pricing_type); @endphp

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">طريقة التسعير <span class="text-danger">*</span></label>
                                    <select wire:model.live="pricing_type"
                                            class="form-select @error('pricing_type') is-invalid @enderror">
                                        @foreach(\App\Enums\Teacher\PricingTypeEnum::cases() as $type)
                                            <option value="{{ $type->value }}">{{ $type->label() }}</option>
                                        @endforeach
                                    </select>
                                    @error('pricing_type')<div class="invalid-feedback">{{ $message }}</div>@enderror

                                    @if($selectedType)
                                        <small class="text-body-secondary d-block mt-2">
                                            <i class="ph-duotone ph-info me-1"></i>{{ $selectedType->description() }}
                                        </small>
                                    @endif
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">
                                        المبلغ <span class="text-danger">*</span>
                                        <small class="text-body-secondary">
                                            (ج.م @if($selectedType) {{ $selectedType->label() }} @endif)
                                        </small>
                                    </label>
                                    <input type="number" step="0.01" min="0" wire:model.live="pricing_amount"
                                           class="form-control @error('pricing_amount') is-invalid @enderror"
                                           placeholder="0.00">
                                    @error('pricing_amount')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>

                                <div class="col-12">
                                    <div class="alert alert-light border mb-0">
                                        <div class="d-flex align-items-start gap-3">
                                            <div class="rounded-3 d-flex align-items-center justify-content-center bg-warning-subtle text-warning"
                                                 style="width: 40px; height: 40px;">
                                                <i class="ph-duotone ph-calculator"></i>
                                            </div>
                                            <div>
                                                <small class="text-body-secondary d-block">مثال توضيحي:</small>
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
                            </div>
                        @endif
                    </div>
                @endif
            </div>

            {{-- Form Actions --}}
            <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
                @if($editMode)
                    <button type="button" class="btn btn-primary"
                            wire:click="update"
                            wire:loading.attr="disabled"
                            wire:target="update">
                        <span wire:loading.remove wire:target="update">
                            <i class="ph-duotone ph-floppy-disk me-1"></i>حفظ التعديلات
                        </span>
                        <span wire:loading wire:target="update">
                            <span class="spinner-border spinner-border-sm me-1"></span>جاري الحفظ...
                        </span>
                    </button>
                @else
                    <button type="button" class="btn btn-primary"
                            wire:click="save(false)"
                            wire:loading.attr="disabled"
                            wire:target="save(false)">
                        <span wire:loading.remove wire:target="save(false)">
                            <i class="ph-duotone ph-floppy-disk me-1"></i>حفظ
                        </span>
                        <span wire:loading wire:target="save(false)">
                            <span class="spinner-border spinner-border-sm me-1"></span>جاري الحفظ...
                        </span>
                    </button>

                    <button type="button" class="btn btn-success"
                            wire:click="save(true)"
                            wire:loading.attr="disabled"
                            wire:target="save(true)">
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

    @script
    <script>
        function initSchedulesSortable() {
            const container = document.getElementById('schedules-container');
            if (!container) return;
            if (typeof Sortable === 'undefined') return;

            if (container._sortable) {
                container._sortable.destroy();
            }

            container._sortable = Sortable.create(container, {
                animation: 200,
                handle: '.drag-handle',
                ghostClass: 'sortable-ghost',
                onEnd: function (evt) {
                    if (evt.oldIndex === evt.newIndex) return;

                    const items = Array.from(container.children);
                    items.sort((a, b) => parseInt(a.dataset.index) - parseInt(b.dataset.index));

                    container.innerHTML = '';
                    items.forEach(item => container.appendChild(item));

                    $wire.reorderSchedules(evt.oldIndex, evt.newIndex);
                }
            });
        }

        if (typeof Sortable === 'undefined') {
            const s = document.createElement('script');
            s.src = 'https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js';
            s.onload = () => setTimeout(initSchedulesSortable, 100);
            document.head.appendChild(s);
        } else {
            setTimeout(initSchedulesSortable, 300);
        }

        Livewire.hook('morph.updated', () => {
            setTimeout(initSchedulesSortable, 200);
        });
    </script>
    @endscript
</div>
