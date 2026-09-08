<div>
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <h5 class="mb-4">{{ $editMode ? 'تعديل المجموعة' : 'إضافة مجموعة جديدة' }}</h5>

            <!-- Group Details -->
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">اسم المجموعة <span class="text-danger">*</span></label>
                    <input type="text" wire:model="name" class="form-control @error('name') is-invalid @enderror"
                           placeholder="أدخل اسم المجموعة">
                    @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
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
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-4 mb-3">
                    <label class="form-label">الحد الأقصى للطلاب</label>
                    <input type="number" min="0" wire:model="max_students"
                           class="form-control @error('max_students') is-invalid @enderror" placeholder="0 = غير محدود">
                    @error('max_students')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-4 mb-3">
                    <label class="form-label">الحالة <span class="text-danger">*</span></label>
                    <select wire:model="status" class="form-select @error('status') is-invalid @enderror">
                        @foreach($statuses as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('status')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-4 mb-3">
                    <label class="form-label">تاريخ البداية</label>
                    <input type="date" wire:model="start_date"
                           class="form-control @error('start_date') is-invalid @enderror">
                    @error('start_date')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-4 mb-3">
                    <label class="form-label">تاريخ النهاية</label>
                    <input type="date" wire:model="end_date"
                           class="form-control @error('end_date') is-invalid @enderror">
                    @error('end_date')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-12 mb-3">
                    <label class="form-label">الوصف</label>
                    <textarea rows="3" wire:model="description"
                              class="form-control @error('description') is-invalid @enderror"
                              placeholder="أدخل وصف المجموعة"></textarea>
                    @error('description')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <hr>

            <!-- Schedules Section -->
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="fw-bold mb-0">المواعيد والمواد</h6>
                <button type="button" class="btn btn-sm btn-primary" wire:click="addScheduleRow">
                    <i class="ph-duotone ph-plus me-1"></i>
                    إضافة موعد
                </button>
            </div>

            @if($schedules)
                @foreach($schedules as $index => $schedule)
                    <div class="row g-2 p-3 bg-light rounded-3 mb-2">
                        <div class="col-md-3">
                            <label class="form-label small">اليوم</label>
                            <select wire:model="schedules.{{ $index }}.day"
                                    class="form-select form-select-sm @error('schedules.'.$index.'.day') is-invalid @enderror">
                                <option value="">اختر اليوم</option>
                                @foreach($days as $key => $label)
                                    <option value="{{ $key }}">{{ $label }}</option>
                                @endforeach
                            </select>
                            @error('schedules.'.$index.'.day')
                            <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-2">
                            <label class="form-label small">من</label>
                            <input type="time" wire:model="schedules.{{ $index }}.start_time"
                                   class="form-control form-control-sm @error('schedules.'.$index.'.start_time') is-invalid @enderror">
                            @error('schedules.'.$index.'.start_time')
                            <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-2">
                            <label class="form-label small">إلى</label>
                            <input type="time" wire:model="schedules.{{ $index }}.end_time"
                                   class="form-control form-control-sm @error('schedules.'.$index.'.end_time') is-invalid @enderror">
                            @error('schedules.'.$index.'.end_time')
                            <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4">
                            <label class="form-label small">المادة</label>
                            <select wire:model="schedules.{{ $index }}.subject_id"
                                    class="form-select form-select-sm @error('schedules.'.$index.'.subject_id') is-invalid @enderror">
                                <option value="">اختر المادة</option>
                                @foreach($teacherSubjects as $subject)
                                    <option value="{{ $subject->id }}">
                                        {{ $subject->grade_label }} - {{ $subject->subject_label }}
                                    </option>
                                @endforeach
                            </select>
                            @error('schedules.'.$index.'.subject_id')
                            <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-1">
                            <button type="button" class="btn btn-danger btn-sm w-100"
                                    wire:click="removeScheduleRow({{ $index }})">
                                <i class="ph-duotone ph-x"></i>
                            </button>
                        </div>
                    </div>
                @endforeach
                @error('schedules')
                <div class="text-danger small">{{ $message }}</div>
                @enderror
            @endif

            <!-- Form Actions -->
            <div class="d-flex justify-content-end gap-2 mt-4">
                @if($editMode)
                    <button type="button" wire:click="update" wire:loading.attr="disabled" wire:target="update"
                            class="btn btn-primary">
                        <span wire:loading.remove wire:target="update"><i class="ph-duotone ph-floppy-disk me-1"></i>حفظ التعديلات</span>
                        <span wire:loading wire:target="update"><span
                                class="spinner-border spinner-border-sm me-1"></span>جاري الحفظ...</span>
                    </button>
                @else
                    <button type="button" wire:click="save(false)" wire:loading.attr="disabled"
                            wire:target="save(false)" class="btn btn-primary">
                        <span wire:loading.remove wire:target="save(false)"><i
                                class="ph-duotone ph-floppy-disk me-1"></i>حفظ</span>
                        <span wire:loading wire:target="save(false)"><span
                                class="spinner-border spinner-border-sm me-1"></span>جاري الحفظ...</span>
                    </button>
                    <button type="button" wire:click="save(true)" wire:loading.attr="disabled" wire:target="save(true)"
                            class="btn btn-success">
                        <span wire:loading.remove wire:target="save(true)"><i
                                class="ph-duotone ph-plus-circle me-1"></i>حفظ وإنشاء جديد</span>
                        <span wire:loading wire:target="save(true)"><span
                                class="spinner-border spinner-border-sm me-1"></span>جاري الحفظ...</span>
                    </button>
                @endif
                <a href="{{ route('teacher.groups.index') }}" wire:navigate class="btn btn-light border">
                    <i class="ph-duotone ph-x-circle me-1"></i>إلغاء
                </a>
            </div>
        </div>
    </div>
</div>
