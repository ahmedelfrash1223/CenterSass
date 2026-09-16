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
                                        <span
                                            class="badge bg-{{ $group->creator->role->color() }} bg-opacity-10 text-{{ $group->creator->role->color() }}">
                                            <i class="ph-duotone {{ $group->creator->role->icon() }} me-1"></i>
                                            {{ $group->creator->name }}
                                            <small
                                                class="ms-1 opacity-75">({{ $group->creator->role->label() }})</small>
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
                                    <span class="fw-semibold small">
                                        {{ $group->created_at?->format('Y-m-d h:i A') }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">اسم المجموعة <span class="text-danger">*</span></label>
                    <input type="text" wire:model="name" class="form-control @error('name') is-invalid @enderror"
                           placeholder="أدخل اسم المجموعة">
                    @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">الفرع <span class="text-danger">*</span></label>
                    <select wire:model="branch_id" class="form-select @error('branch_id') is-invalid @enderror">
                        <option value="">اختر الفرع</option>
                        @foreach($branches as $branch)
                            <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                        @endforeach
                    </select>
                    @error('branch_id')
                    <div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">المرحلة الدراسية <span class="text-danger">*</span></label>
                    <select wire:model.live="grade" class="form-select @error('grade') is-invalid @enderror">
                        <option value="">اختر المرحلة</option>
                        @foreach($grades as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('grade')
                    <div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-2 mb-3">
                    <label class="form-label">الحد الأقصى للطلاب</label>
                    <input type="number" min="0" wire:model="max_students"
                           class="form-control @error('max_students') is-invalid @enderror" placeholder="0 = غير محدود">
                    @error('max_students')
                    <div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-2 mb-3">
                    <label class="form-label">تاريخ البداية</label>
                    <input type="date" wire:model="start_date"
                           class="form-control @error('start_date') is-invalid @enderror">
                    @error('start_date')
                    <div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-2 mb-3">
                    <label class="form-label">تاريخ النهاية</label>
                    <input type="date" wire:model="end_date"
                           class="form-control @error('end_date') is-invalid @enderror">
                    @error('end_date')
                    <div class="invalid-feedback">{{ $message }}</div>@enderror
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
                    @error('status')
                    <div class="text-danger small mt-1">{{ $message }}</div>@enderror
                </div>

                <div class="col-12 mb-3">
                    <label class="form-label">الوصف</label>
                    <textarea rows="3" wire:model="description"
                              class="form-control @error('description') is-invalid @enderror"
                              placeholder="أدخل وصف المجموعة"></textarea>
                    @error('description')
                    <div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>

            {{-- ✅ Schedules Section --}}
            <hr class="my-4">
            <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                <div>
                    <h6 class="fw-bold mb-1">
                        <i class="ph-duotone ph-calendar-check me-1"></i>
                        المواعيد والمواد
                        <span
                            class="badge bg-primary bg-opacity-10 text-primary ms-2">{{ count($schedules) }} موعد</span>
                    </h6>
                    <p class="text-muted small mb-0">حدد أيام ومواعيد ومواد المجموعة</p>
                </div>
                <button type="button" class="btn btn-sm btn-primary"
                        wire:click="addScheduleRow" {{ !$grade ? 'disabled' : '' }}>
                    <i class="ph-duotone ph-plus me-1"></i>
                    إضافة موعد
                </button>
            </div>

            @if(!$grade)
                <div class="alert alert-info d-flex align-items-center gap-2 mb-3">
                    <i class="ph-duotone ph-info fs-5"></i>
                    <span>يرجى اختيار المرحلة الدراسية أولاً لعرض المواد المتاحة.</span>
                </div>
            @endif

            @if(count($schedules) > 0)
                @foreach($schedules as $index => $schedule)
                    <div class="row g-2 p-3 bg-light rounded-3 mb-2 align-items-end"
                         wire:key="schedule-row-{{ $index }}">
                        <div class="col-md-2">
                            <label class="form-label small fw-semibold">اليوم</label>
                            <select wire:model="schedules.{{ $index }}.day"
                                    class="form-select form-select-sm @error('schedules.'.$index.'.day') is-invalid @enderror">
                                <option value="">اختر اليوم</option>
                                @foreach($days as $key => $label)
                                    <option value="{{ $key }}">{{ $label }}</option>
                                @endforeach
                            </select>
                            @error('schedules.'.$index.'.day')
                            <div class="text-danger small">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-2">
                            <label class="form-label small fw-semibold">من</label>
                            <input type="time" wire:model="schedules.{{ $index }}.start_time"
                                   class="form-control form-control-sm @error('schedules.'.$index.'.start_time') is-invalid @enderror">
                            @error('schedules.'.$index.'.start_time')
                            <div class="text-danger small">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-2">
                            <label class="form-label small fw-semibold">إلى</label>
                            <input type="time" wire:model="schedules.{{ $index }}.end_time"
                                   class="form-control form-control-sm @error('schedules.'.$index.'.end_time') is-invalid @enderror">
                            @error('schedules.'.$index.'.end_time')
                            <div class="text-danger small">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-2">
                            <label class="form-label small fw-semibold">المادة</label>
                            <select wire:model="schedules.{{ $index }}.subject_id"
                                    class="form-select form-select-sm @error('schedules.'.$index.'.subject_id') is-invalid @enderror" {{ !$grade ? 'disabled' : '' }}>
                                <option value="">{{ $grade ? 'اختر المادة' : 'اختر المرحلة أولاً' }}</option>
                                @foreach($teacherSubjects as $subject)
                                    <option value="{{ $subject->id }}">
                                        {{ $subject->grade_label }} - {{ $subject->subject_label }}
                                    </option>
                                @endforeach
                            </select>
                            @error('schedules.'.$index.'.subject_id')
                            <div class="text-danger small">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-3">
                            <label class="form-label small fw-semibold">المدرس</label>
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
                            <div class="text-danger small">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-1 text-center">
                            <button type="button" wire:click="removeScheduleRow({{ $index }})"
                                    class="btn btn-sm btn-outline-danger w-100" title="حذف الموعد">
                                <i class="ph-duotone ph-trash"></i>
                            </button>
                        </div>
                    </div>
                @endforeach
            @else
                <div class="text-center text-muted py-4 border rounded-3 bg-light">
                    <i class="ph-duotone ph-calendar-x fs-1 d-block mb-2"></i>
                    <p class="mb-0 small">لا توجد مواعيد. اضغط "إضافة موعد" للبدء.</p>
                </div>
            @endif

            {{-- Form Actions --}}
            <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
                @if($editMode)
                    <button type="button" class="btn btn-primary" wire:click="update" wire:loading.attr="disabled"
                            wire:target="update">
                        <span wire:loading.remove wire:target="update">
                            <i class="ph-duotone ph-floppy-disk me-1"></i>حفظ التعديلات
                        </span>
                        <span wire:loading wire:target="update">
                            <span class="spinner-border spinner-border-sm me-1"></span>جاري الحفظ...
                        </span>
                    </button>
                @else
                    <button type="button" class="btn btn-primary" wire:click="save(false)" wire:loading.attr="disabled"
                            wire:target="save(false)">
                        <span wire:loading.remove wire:target="save(false)">
                            <i class="ph-duotone ph-floppy-disk me-1"></i>حفظ
                        </span>
                        <span wire:loading wire:target="save(false)">
                            <span class="spinner-border spinner-border-sm me-1"></span>جاري الحفظ...
                        </span>
                    </button>
                    <button type="button" class="btn btn-success" wire:click="save(true)" wire:loading.attr="disabled"
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
</div>
