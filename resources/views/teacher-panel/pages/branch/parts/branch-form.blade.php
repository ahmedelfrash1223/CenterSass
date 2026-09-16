<div>
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <h5 class="mb-4">{{ $editMode ? 'تعديل الفرع' : 'إضافة فرع جديد' }}</h5>

            @if($editMode && isset($branch))
                <div class="info-strip mb-4">
                    <div class="row g-2">
                        {{-- أضيف بواسطة --}}
                        <div class="col-md-6">
                            <div class="info-item">
                                <div class="info-icon info-icon-primary">
                                    <i class="ph-duotone ph-user-plus"></i>
                                </div>
                                <div class="info-content">
                                    <small class="info-label">أضيف بواسطة</small>
                                    @if($branch->creator)
                                        <span
                                            class="badge bg-{{ $branch->creator->role->color() }} bg-opacity-10 text-{{ $branch->creator->role->color() }}">
                                            <i class="ph-duotone {{ $branch->creator->role->icon() }} me-1"></i>
                                            {{ $branch->creator->name }}
                                            <small
                                                class="ms-1 opacity-75">({{ $branch->creator->role->label() }})</small>
                                        </span>
                                    @else
                                        <span class="text-muted small">—</span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        {{-- تاريخ الإضافة --}}
                        <div class="col-md-6">
                            <div class="info-item">
                                <div class="info-icon info-icon-info">
                                    <i class="ph-duotone ph-calendar"></i>
                                </div>
                                <div class="info-content">
                                    <small class="info-label">تاريخ الإضافة</small>
                                    <span class="fw-semibold small">
                                        {{ $branch->created_at?->format('Y-m-d h:i A') }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Branch Details -->
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">اسم الفرع <span class="text-danger">*</span></label>
                    <input type="text" wire:model="name" class="form-control @error('name') is-invalid @enderror"
                           placeholder="أدخل اسم الفرع">
                    @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">رقم الهاتف</label>
                    <input type="text" wire:model="phone" class="form-control @error('phone') is-invalid @enderror"
                           placeholder="أدخل رقم الهاتف">
                    @error('phone')
                    <div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">البريد الإلكتروني</label>
                    <input type="email" wire:model="email" class="form-control @error('email') is-invalid @enderror"
                           placeholder="أدخل البريد الإلكتروني">
                    @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">الحالة <span class="text-danger">*</span></label>
                    <div class="d-flex gap-2">
                        <button type="button" wire:click="$set('is_active', 1)"
                                class="status-option {{ $is_active == 1 ? 'active' : '' }}">
                            <i class="ph-duotone ph-check-circle"></i>
                            نشط
                        </button>
                        <button type="button" wire:click="$set('is_active', 0)"
                                class="status-option {{ $is_active == 0 ? 'inactive' : '' }}">
                            <i class="ph-duotone ph-x-circle"></i>
                            غير نشط
                        </button>
                    </div>
                    @error('is_active')
                    <div class="text-danger small mt-1">{{ $message }}</div>@enderror
                </div>
                <div class="col-12 mb-3">
                    <label class="form-label">العنوان <span class="text-danger">*</span></label>
                    <textarea wire:model="address" rows="4" class="form-control @error('address') is-invalid @enderror"
                              placeholder="أدخل عنوان الفرع"></textarea>
                    @error('address')
                    <div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>

            <!-- Form Actions -->
            <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
                @if($editMode)
                    <button type="button" wire:click="update" wire:loading.attr="disabled" wire:target="update"
                            class="btn btn-primary">
                        <span wire:loading.remove wire:target="update">
                            <i class="ph-duotone ph-floppy-disk me-1"></i>حفظ التعديلات
                        </span>
                        <span wire:loading wire:target="update">
                            <span class="spinner-border spinner-border-sm me-1"></span>جاري الحفظ...
                        </span>
                    </button>
                @else
                    <button type="button" wire:click="save(false)" wire:loading.attr="disabled"
                            wire:target="save(false)" class="btn btn-primary">
                        <span wire:loading.remove wire:target="save(false)">
                            <i class="ph-duotone ph-floppy-disk me-1"></i>حفظ
                        </span>
                        <span wire:loading wire:target="save(false)">
                            <span class="spinner-border spinner-border-sm me-1"></span>جاري الحفظ...
                        </span>
                    </button>
                    <button type="button" wire:click="save(true)" wire:loading.attr="disabled" wire:target="save(true)"
                            class="btn btn-success">
                        <span wire:loading.remove wire:target="save(true)">
                            <i class="ph-duotone ph-plus-circle me-1"></i>حفظ وإنشاء جديد
                        </span>
                        <span wire:loading wire:target="save(true)">
                            <span class="spinner-border spinner-border-sm me-1"></span>جاري الحفظ...
                        </span>
                    </button>
                @endif
                <a href="{{ route('teacher.branches.index') }}" wire:navigate class="btn btn-light border">
                    <i class="ph-duotone ph-x-circle me-1"></i>إلغاء
                </a>
            </div>
        </div>
    </div>
</div>
