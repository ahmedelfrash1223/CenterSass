<div>
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <h5 class="mb-4">{{ $editMode ? 'تعديل المدرس' : 'إضافة مدرس جديد' }}</h5>

            <!-- Teacher Account Details -->
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">اسم المدرس <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror" wire:model="name" placeholder="أدخل اسم المدرس">
                    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">اسم المستخدم <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('username') is-invalid @enderror" wire:model="username" placeholder="أدخل اسم المستخدم">
                    @error('username')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">البريد الإلكتروني <span class="text-danger">*</span></label>
                    <input type="email" class="form-control @error('email') is-invalid @enderror" wire:model="email" placeholder="أدخل البريد الإلكتروني">
                    @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">رقم الهاتف</label>
                    <input type="text" class="form-control @error('phone') is-invalid @enderror" wire:model="phone" placeholder="أدخل رقم الهاتف">
                    @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">نوع الحساب <span class="text-danger">*</span></label>
                    <select class="form-select @error('role') is-invalid @enderror" wire:model.live="role">
                        @foreach($roles as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('role')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                @if((int)$role !== \App\Enums\Teacher\TeacherRoleEnum::OWNER->value)
                    <div class="col-md-6 mb-3">
                        <label class="form-label">تابع لـ (المسئول) <span class="text-danger">*</span></label>
                        <select class="form-select @error('owner_id') is-invalid @enderror" wire:model="owner_id">
                            <option value="">اختر المسئول</option>
                            @foreach($owners as $owner)
                                <option value="{{ $owner->id }}">{{ $owner->name }} ({{ $owner->email }})</option>
                            @endforeach
                        </select>
                        @error('owner_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                @endif

                <div class="col-md-6 mb-3">
                    <label class="form-label">
                        كلمة المرور
                        @if($editMode)
                            <small class="text-muted">(اتركها فارغة إذا لم تريد التغيير)</small>
                        @else
                            <small class="text-muted">(اتركها فارغة لاستخدام 12345678)</small>
                        @endif
                    </label>
                    <input type="password" class="form-control @error('password') is-invalid @enderror" wire:model="password" placeholder="أدخل كلمة المرور">
                    @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">تأكيد كلمة المرور</label>
                    <input type="password" class="form-control @error('password_confirmation') is-invalid @enderror" wire:model="password_confirmation" placeholder="أعد إدخال كلمة المرور">
                    @error('password_confirmation')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">الحالة <span class="text-danger">*</span></label>
                    <div class="d-flex gap-2">
                        <button type="button" wire:click="$set('is_active', 1)" class="status-option {{ $is_active == 1 ? 'active' : '' }}">
                            <i class="ph-duotone ph-check-circle"></i>
                            نشط
                        </button>
                        <button type="button" wire:click="$set('is_active', 0)" class="status-option {{ $is_active == 0 ? 'inactive' : '' }}">
                            <i class="ph-duotone ph-x-circle"></i>
                            غير نشط
                        </button>
                    </div>
                    @error('is_active')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                </div>
            </div>

            @if((int)$role !== \App\Enums\Teacher\TeacherRoleEnum::OWNER->value)
                <hr class="my-4">
                <div class="permissions-section">
                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
                        <div>
                            <h6 class="fw-bold mb-1">
                                <i class="ph-duotone ph-shield-check me-1"></i>
                                الصلاحيات
                                <span class="badge bg-primary bg-opacity-10 text-primary ms-2">{{ count($selectedPermissions) }} مختارة</span>
                            </h6>
                            <p class="text-muted small mb-0">حدد الصلاحيات المسموح لهذا العضو بالوصول إليها</p>
                        </div>
                        <div class="d-flex gap-2">
                            <button type="button" wire:click="selectAll" class="btn btn-sm btn-outline-success">
                                <i class="ph-duotone ph-check-square me-1"></i>
                                تحديد الكل
                            </button>
                            <button type="button" wire:click="deselectAll" class="btn btn-sm btn-outline-danger">
                                <i class="ph-duotone ph-x-square me-1"></i>
                                إلغاء الكل
                            </button>
                        </div>
                    </div>

                    @if(!empty($groupedPermissions))
                        @foreach($groupedPermissions as $groupName => $permissions)
                            <div class="permission-group mb-3">
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2">
                                            {{ $groupName }}
                                        </span>
                                        <small class="text-muted">{{ count($permissions) }} صلاحية</small>
                                    </div>
                                    <button type="button" wire:click="toggleGroup('{{ $groupName }}')" class="btn btn-sm btn-link text-decoration-none p-0">
                                        تحديد الكل / إلغاء الكل
                                    </button>
                                </div>
                                <div class="row g-2">
                                    @foreach($permissions as $permission)
                                        <div class="col-md-4 col-lg-3">
                                            <label class="permission-item {{ in_array($permission->value, $selectedPermissions) ? 'is-selected' : '' }}" for="perm-{{ $permission->value }}">
                                                <input type="checkbox" wire:model.live="selectedPermissions" value="{{ $permission->value }}" id="perm-{{ $permission->value }}" class="permission-checkbox">
                                                <div class="permission-content">
                                                    <span class="permission-icon">
                                                        <i class="ph-duotone {{ $permission->icon() }}"></i>
                                                    </span>
                                                    <span class="permission-label">{{ $permission->label() }}</span>
                                                    <span class="permission-check">
                                                        <i class="ph-duotone ph-check"></i>
                                                    </span>
                                                </div>
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    @endif
                </div>
            @endif

            <!-- Form Actions -->
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
                <a href="{{ route('admin.teacher.index') }}" wire:navigate class="btn btn-light border">
                    <i class="ph-duotone ph-x-circle me-1"></i>إلغاء
                </a>
            </div>
        </div>
    </div>
</div>

<style>
    .status-option {
        flex: 1;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        padding: 9px 14px;
        background: var(--bs-body-bg, #fff);
        border: 1.5px solid var(--bs-border-color, #e5e7eb);
        border-radius: 10px;
        font-weight: 600;
        font-size: 13px;
        color: var(--bs-secondary-color, #6b7280);
        transition: all 0.2s ease;
        user-select: none;
        white-space: nowrap;
    }

    .status-option:hover {
        border-color: #a5b4fc;
        background: #f5f7ff;
        color: #4f46e5;
    }

    .status-option.active {
        border-color: #10b981;
        background: linear-gradient(135deg, #ecfdf5 0%, #d1fae5 100%);
        color: #059669;
        box-shadow: 0 2px 6px rgba(16, 185, 129, 0.15);
    }

    .status-option.inactive {
        border-color: #ef4444;
        background: linear-gradient(135deg, #fef2f2 0%, #fee2e2 100%);
        color: #dc2626;
        box-shadow: 0 2px 6px rgba(239, 68, 68, 0.15);
    }

    .status-option i {
        font-size: 16px;
    }

    .permissions-section {
        background: var(--bs-tertiary-bg, #f8fafc);
        border: 1px solid var(--bs-border-color, #e2e8f0);
        border-radius: 12px;
        padding: 16px;
    }

    .permission-group {
        padding-bottom: 12px;
        border-bottom: 1px solid var(--bs-border-color, #e2e8f0);
    }

    .permission-group:last-child {
        border-bottom: none;
        padding-bottom: 0;
    }

    .permission-item {
        display: block;
        cursor: pointer;
        margin: 0;
    }

    .permission-checkbox {
        position: absolute;
        opacity: 0;
        pointer-events: none;
    }

    .permission-content {
        position: relative;
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 8px 10px;
        background: var(--bs-body-bg, #fff);
        border: 1.5px solid var(--bs-border-color, #e5e7eb);
        border-radius: 8px;
        transition: all 0.2s ease;
        min-height: 40px;
    }

    .permission-item:hover .permission-content {
        border-color: #a5b4fc;
        background: var(--bs-tertiary-bg, #f5f7ff);
        transform: translateY(-1px);
        box-shadow: 0 2px 6px rgba(99, 102, 241, 0.1);
    }

    .permission-item.is-selected .permission-content {
        border-color: #4f46e5;
        background: linear-gradient(135deg, #eef2ff 0%, #e0e7ff 100%);
        box-shadow: 0 2px 6px rgba(79, 70, 229, 0.15);
    }

    .permission-icon {
        width: 24px;
        height: 24px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 6px;
        background: var(--bs-secondary-bg, #f1f5f9);
        color: var(--bs-secondary-color, #64748b);
        font-size: 13px;
        flex-shrink: 0;
    }

    .permission-item.is-selected .permission-icon {
        background: #4f46e5;
        color: #fff;
    }

    .permission-label {
        flex: 1;
        font-size: 12px;
        font-weight: 600;
        color: var(--bs-body-color, #374151);
        line-height: 1.3;
    }

    .permission-item.is-selected .permission-label {
        color: #4f46e5;
    }

    .permission-check {
        width: 16px;
        height: 16px;
        border-radius: 50%;
        background: #4f46e5;
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 9px;
        opacity: 0;
        transform: scale(0);
        transition: all 0.2s ease;
        flex-shrink: 0;
    }

    .permission-item.is-selected .permission-check {
        opacity: 1;
        transform: scale(1);
    }

    [data-pc-theme="dark"] .status-option,
    [data-bs-theme="dark"] .status-option,
    body.dark .status-option,
    .dark-mode .status-option {
        background: #1e293b;
        border-color: rgba(255, 255, 255, 0.08);
        color: #94a3b8;
    }

    [data-pc-theme="dark"] .status-option.active,
    [data-bs-theme="dark"] .status-option.active,
    body.dark .status-option.active,
    .dark-mode .status-option.active {
        border-color: #10b981;
        background: linear-gradient(135deg, #064e3b 0%, #065f46 100%);
        color: #6ee7b7;
    }

    [data-pc-theme="dark"] .status-option.inactive,
    [data-bs-theme="dark"] .status-option.inactive,
    body.dark .status-option.inactive,
    .dark-mode .status-option.inactive {
        border-color: #ef4444;
        background: linear-gradient(135deg, #7f1d1d 0%, #991b1b 100%);
        color: #fca5a5;
    }

    [data-pc-theme="dark"] .permissions-section,
    [data-bs-theme="dark"] .permissions-section,
    body.dark .permissions-section,
    .dark-mode .permissions-section {
        background: #0f172a;
        border-color: rgba(255, 255, 255, 0.08);
    }

    [data-pc-theme="dark"] .permission-content,
    [data-bs-theme="dark"] .permission-content,
    body.dark .permission-content,
    .dark-mode .permission-content {
        background: #1e293b;
        border-color: rgba(255, 255, 255, 0.08);
    }

    [data-pc-theme="dark"] .permission-item.is-selected .permission-content,
    [data-bs-theme="dark"] .permission-item.is-selected .permission-content,
    body.dark .permission-item.is-selected .permission-content,
    .dark-mode .permission-item.is-selected .permission-content {
        border-color: #6366f1;
        background: linear-gradient(135deg, #312e81 0%, #4338ca 100%);
    }

    [data-pc-theme="dark"] .permission-label,
    [data-bs-theme="dark"] .permission-label,
    body.dark .permission-label,
    .dark-mode .permission-label {
        color: #cbd5e1;
    }

    [data-pc-theme="dark"] .permission-item.is-selected .permission-label,
    [data-bs-theme="dark"] .permission-item.is-selected .permission-label,
    body.dark .permission-item.is-selected .permission-label,
    .dark-mode .permission-item.is-selected .permission-label {
        color: #c7d2fe;
    }
</style>
