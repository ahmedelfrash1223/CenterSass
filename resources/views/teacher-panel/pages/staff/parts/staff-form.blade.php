<div>
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <h5 class="mb-4">{{ $editMode ? 'تعديل عضو الفريق' : 'إضافة عضو جديد' }}</h5>

            @if($editMode && isset($teacher))
                <div class="info-strip mb-4">
                    <div class="row g-2">
                        <div class="col-md-6">
                            <div class="info-item">
                                <div class="info-icon info-icon-primary">
                                    <i class="ph-duotone ph-user-plus"></i>
                                </div>
                                <div class="info-content">
                                    <small class="info-label">أضيف بواسطة</small>
                                    @if($teacher->creator)
                                        <span class="badge bg-{{ $teacher->creator->role->color() }} bg-opacity-10 text-{{ $teacher->creator->role->color() }}">
                                            <i class="ph-duotone {{ $teacher->creator->role->icon() }} me-1"></i>
                                            {{ $teacher->creator->name }}
                                            <small class="ms-1 opacity-75">({{ $teacher->creator->role->label() }})</small>
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
                                        {{ $teacher->created_at?->format('Y-m-d h:i A') }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">الاسم <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror" wire:model="name" placeholder="أدخل الاسم">
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

            {{-- ✅ Branches Section --}}
            @if(!$editMode || (isset($teacher) && !$teacher->isOwner()))
                <hr class="my-4">
                <div class="branches-section">
                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
                        <div>
                            <h6 class="fw-bold mb-1">
                                <i class="ph-duotone ph-git-branch me-1"></i>
                                الفروع
                                <span class="badge bg-primary bg-opacity-10 text-primary ms-2">{{ count($branch_ids) }} مختارة</span>
                            </h6>
                            <p class="text-muted small mb-0">حدد الفروع المسموح لهذا العضو بالعمل فيها (اختياري)</p>
                        </div>
                        <div class="d-flex gap-2">
                            <button type="button"
                                    wire:click="$set('branch_ids', @js($branches->pluck('id')->map(fn($id) => (string)$id)->toArray()))"
                                    class="btn btn-sm btn-outline-success">
                                <i class="ph-duotone ph-check-square me-1"></i>تحديد الكل
                            </button>
                            <button type="button" wire:click="$set('branch_ids', [])" class="btn btn-sm btn-outline-danger">
                                <i class="ph-duotone ph-x-square me-1"></i>إلغاء الكل
                            </button>
                        </div>
                    </div>

                    @error('branch_ids')
                    <div class="alert alert-danger d-flex align-items-center gap-2 mb-3">
                        <i class="ph-duotone ph-warning-circle fs-5"></i>
                        <span>{{ $message }}</span>
                    </div>
                    @enderror

                    @if($branches->count() > 0)
                        <div class="row g-2">
                            @foreach($branches as $branch)
                                <div class="col-md-4 col-lg-3">
                                    <label class="permission-item {{ in_array((string)$branch->id, $branch_ids) ? 'is-selected' : '' }}"
                                           for="brn-{{ $branch->id }}">
                                        <input
                                            type="checkbox"
                                            wire:model.live="branch_ids"
                                            value="{{ (string) $branch->id }}"
                                            id="brn-{{ $branch->id }}"
                                            class="permission-checkbox"
                                            {{ in_array((string)$branch->id, $branch_ids) ? 'checked' : '' }}
                                        >
                                        <div class="permission-content">
                                            <span class="permission-icon">
                                                <i class="ph-duotone ph-git-branch"></i>
                                            </span>
                                            <span class="permission-label">{{ $branch->name }}</span>
                                            <span class="permission-check">
                                                <i class="ph-duotone ph-check"></i>
                                            </span>
                                        </div>
                                    </label>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="alert alert-info mb-0 d-flex align-items-center gap-2">
                            <i class="ph-duotone ph-info fs-5"></i>
                            <span>لا توجد فروع متاحة لك. تواصل مع صاحب الحساب لإضافتك لفروع.</span>
                        </div>
                    @endif
                </div>
            @endif

            {{-- ✅ Permissions Section --}}
            <hr class="my-4">
            <div class="permissions-section">
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
                    <div>
                        <h6 class="fw-bold mb-1">
                            <i class="ph-duotone ph-shield-check me-1"></i>
                            الصلاحيات
                            <span class="badge bg-primary bg-opacity-10 text-primary ms-2">{{ $this->selectedCount }} مختارة</span>
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

                @foreach($groupedPermissions as $groupName => $groupPermissions)
                    <div class="permission-group mb-3">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2">
                                    {{ $groupName }}
                                </span>
                                <small class="text-muted">{{ count($groupPermissions) }} صلاحية</small>
                            </div>
                            <button type="button" wire:click="toggleGroup('{{ $groupName }}')" class="btn btn-sm btn-link text-decoration-none p-0">
                                تحديد الكل / إلغاء الكل
                            </button>
                        </div>
                        <div class="row g-2">
                            @foreach($groupPermissions as $permission)
                                <div class="col-md-4 col-lg-3">
                                    <label class="permission-item {{ ($permissions[$permission->value] ?? false) ? 'is-selected' : '' }}" for="perm-{{ $permission->value }}">
                                        <input
                                            type="checkbox"
                                            wire:click="togglePermission('{{ $permission->value }}')"
                                            id="perm-{{ $permission->value }}"
                                            class="permission-checkbox"
                                            {{ ($permissions[$permission->value] ?? false) ? 'checked' : '' }}
                                        >
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
                <a href="{{ route('teacher.staff.index') }}" wire:navigate class="btn btn-light border">
                    <i class="ph-duotone ph-x-circle me-1"></i>إلغاء
                </a>
            </div>
        </div>
    </div>
</div>
