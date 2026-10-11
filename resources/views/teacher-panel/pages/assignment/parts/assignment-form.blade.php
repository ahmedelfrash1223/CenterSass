<div>
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <h5 class="mb-4">{{ $editMode ? 'تعديل المتابعة' : 'إضافة متابعة جديدة' }}</h5>

            @if($editMode && isset($assignment))
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
                                    @if($assignment->creator)
                                        <span
                                            class="badge bg-{{ $assignment->creator->role->color() }}-subtle text-{{ $assignment->creator->role->color() }}-emphasis">
                                            {{ $assignment->creator->name }}
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
                                    <span class="fw-semibold small">
                                        {{ $assignment->created_at?->format('Y-m-d h:i A') }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            {{-- =============================================
            Section: البيانات الأساسية
            ============================================= --}}
            <div class="card border mb-3">
                <div class="card-header bg-body d-flex align-items-center justify-content-between"
                    style="cursor: pointer;" wire:click="toggleSection('sectionBasic')">
                    <div class="d-flex align-items-center gap-2">
                        <div class="rounded-3 d-flex align-items-center justify-content-center bg-primary-subtle text-primary"
                            style="width: 40px; height: 40px;">
                            <i class="ph-duotone ph-check-square"></i>
                        </div>
                        <div>
                            <h6 class="mb-0 fw-bold">البيانات الأساسية</h6>
                            <small class="text-body-secondary">العنوان، النوع، المجموعة، الدرجة</small>
                        </div>
                    </div>
                    <i class="ph-duotone {{ $sectionBasic ? 'ph-caret-up' : 'ph-caret-down' }}"></i>
                </div>

                @if($sectionBasic)
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-8 mb-3">
                                <label class="form-label">العنوان <span class="text-danger">*</span></label>
                                <input type="text" wire:model="title"
                                    class="form-control @error('title') is-invalid @enderror"
                                    placeholder="مثال: سورة البقرة صفحة 5">
                                @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label class="form-label">النوع <span class="text-danger">*</span></label>
                                <select wire:model="type" class="form-select @error('type') is-invalid @enderror">
                                    <option value="">اختر النوع</option>
                                    @foreach($types as $value => $label)
                                        <option value="{{ $value }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                                @error('type')
                                <div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">المجموعة <span class="text-danger">*</span></label>
                                <select wire:model.live="group_id"
                                    class="form-select @error('group_id') is-invalid @enderror">
                                    <option value="">اختر المجموعة</option>
                                    @foreach($groups as $group)
                                        <option value="{{ $group->id }}">{{ $group->name }}</option>
                                    @endforeach
                                </select>
                                @error('group_id')
                                <div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-3 mb-3">
                                <label class="form-label">الدرجة العظمى <span class="text-danger">*</span></label>
                                <input type="number" step="0.5" min="0.5" wire:model="max_score"
                                    class="form-control @error('max_score') is-invalid @enderror">
                                @error('max_score')
                                <div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-3 mb-3">
                                <label class="form-label">الحالة <span class="text-danger">*</span></label>
                                <select wire:model="status" class="form-select @error('status') is-invalid @enderror">
                                    @foreach($statuses as $value => $label)
                                        <option value="{{ $value }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                                @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">تاريخ التسليم</label>
                                <input type="date" wire:model="due_date"
                                    class="form-control @error('due_date') is-invalid @enderror">
                                @error('due_date')
                                <div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            {{-- =============================================
            Section: الوصف
            ============================================= --}}
            <div class="card border mb-3">
                <div class="card-header bg-body d-flex align-items-center justify-content-between"
                    style="cursor: pointer;" wire:click="toggleSection('sectionDescription')">
                    <div class="d-flex align-items-center gap-2">
                        <div class="rounded-3 d-flex align-items-center justify-content-center bg-info-subtle text-info"
                            style="width: 40px; height: 40px;">
                            <i class="ph-duotone ph-text-align-right"></i>
                        </div>
                        <div>
                            <h6 class="mb-0 fw-bold">الوصف</h6>
                            <small class="text-body-secondary">تفاصيل إضافية عن المتابعة</small>
                        </div>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        @if($description)
                            <span class="badge bg-info text-white">
                                <i class="ph-duotone ph-check me-1"></i>مكتمل
                            </span>
                        @endif
                        <i class="ph-duotone {{ $sectionDescription ? 'ph-caret-up' : 'ph-caret-down' }}"></i>
                    </div>
                </div>

                @if($sectionDescription)
                    <div class="card-body">
                        <textarea rows="4" wire:model="description"
                            class="form-control @error('description') is-invalid @enderror"
                            placeholder="اكتب تفاصيل المتابعة هنا..."></textarea>
                        @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                @endif
            </div>

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
                    <button type="button" class="btn btn-primary" wire:click="save" wire:loading.attr="disabled"
                        wire:target="save">
                        <span wire:loading.remove wire:target="save">
                            <i class="ph-duotone ph-floppy-disk me-1"></i>حفظ
                        </span>
                        <span wire:loading wire:target="save">
                            <span class="spinner-border spinner-border-sm me-1"></span>جاري الحفظ...
                        </span>
                    </button>
                @endif

                <a href="{{ route('teacher.assignments.index') }}" wire:navigate class="btn btn-light border">
                    <i class="ph-duotone ph-x-circle me-1"></i>إلغاء
                </a>
            </div>
        </div>
    </div>
</div>
