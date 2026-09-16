<div>
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <h5 class="mb-4">{{ $editMode ? 'تعديل بيانات الطالب' : 'إضافة طالب جديد' }}</h5>

            @if($editMode && isset($student))
                <div class="info-strip mb-4">
                    <div class="row g-2">
                        <div class="col-md-6">
                            <div class="info-item">
                                <div class="info-icon info-icon-primary">
                                    <i class="ph-duotone ph-user-plus"></i>
                                </div>
                                <div class="info-content">
                                    <small class="info-label">أضيف بواسطة</small>
                                    @if($student->creator)
                                        <span class="badge bg-{{ $student->creator->role->color() }} bg-opacity-10 text-{{ $student->creator->role->color() }}">
                                            <i class="ph-duotone {{ $student->creator->role->icon() }} me-1"></i>
                                            {{ $student->creator->name }}
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
                                    <span class="fw-semibold small">{{ $student->created_at?->format('Y-m-d h:i A') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">الاسم <span class="text-danger">*</span></label>
                    <input type="text" wire:model="name" class="form-control @error('name') is-invalid @enderror" placeholder="أدخل الاسم">
                    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">اسم المستخدم <span class="text-danger">*</span></label>
                    <input type="text" wire:model="username" class="form-control @error('username') is-invalid @enderror" placeholder="أدخل اسم المستخدم">
                    @error('username')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">البريد الإلكتروني <span class="text-danger">*</span></label>
                    <input type="email" wire:model="email" class="form-control @error('email') is-invalid @enderror" placeholder="example@mail.com">
                    @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">رقم الهاتف</label>
                    <input type="text" wire:model="phone" class="form-control @error('phone') is-invalid @enderror" placeholder="01xxxxxxxxx">
                    @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>


                <div class="col-md-6 mb-3">
                    <label class="form-label">المرحلة الدراسية <span class="text-danger">*</span></label>
                    <select wire:model="grade" class="form-select @error('grade') is-invalid @enderror">
                        <option value="">اختر المرحلة</option>
                        @foreach($grades as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('grade')<div class="invalid-feedback">{{ $message }}</div>@enderror
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
                    <label class="form-label">
                        كلمة المرور
                        @if($editMode)
                            <small class="text-muted">(اتركها فارغة إذا لم تريد التغيير)</small>
                        @else
                            <small class="text-muted">(اتركها فارغة لاستخدام 12345678)</small>
                        @endif
                    </label>
                    <input type="password" wire:model="password" class="form-control @error('password') is-invalid @enderror" placeholder="أدخل كلمة المرور">
                    @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">تأكيد كلمة المرور</label>
                    <input type="password" wire:model="password_confirmation" class="form-control" placeholder="أعد إدخال كلمة المرور">
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">الحالة <span class="text-danger">*</span></label>
                    <div class="d-flex gap-2">
                        <button type="button" wire:click="$set('is_active', 1)" class="status-option {{ $is_active == 1 ? 'active' : '' }}">
                            <i class="ph-duotone ph-check-circle"></i> نشط
                        </button>
                        <button type="button" wire:click="$set('is_active', 0)" class="status-option {{ $is_active == 0 ? 'inactive' : '' }}">
                            <i class="ph-duotone ph-x-circle"></i> غير نشط
                        </button>
                    </div>
                </div>
            </div>

            <hr class="my-4">
            <div class="mb-3">
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-2">
                    <div>
                        <h6 class="fw-bold mb-1">
                            <i class="ph-duotone ph-users-three me-1"></i>
                            المجموعات
                            <span class="badge bg-primary bg-opacity-10 text-primary ms-2">{{ count($group_ids) }} مختارة</span>
                        </h6>
                        <p class="text-muted small mb-0">اختياري — يمكنك إضافة الطالب لمجموعة أو أكثر</p>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="button" wire:click="$set('group_ids', @js($groups->where('is_full', false)->pluck('id')->map(fn($id) => (string)$id)->toArray()))" class="btn btn-sm btn-outline-success">
                            <i class="ph-duotone ph-check-square me-1"></i>تحديد الكل
                        </button>
                        <button type="button" wire:click="$set('group_ids', [])" class="btn btn-sm btn-outline-danger">
                            <i class="ph-duotone ph-x-square me-1"></i>إلغاء الكل
                        </button>
                    </div>
                </div>

                @error('group_ids')
                <div class="alert alert-danger d-flex align-items-center gap-2 mb-3">
                    <i class="ph-duotone ph-warning-circle fs-5"></i>
                    <span>{{ $message }}</span>
                </div>
                @enderror

                @if($groups->count() > 0)
                    <div class="row g-2">
                        @foreach($groups as $group)
                            @php
                                $isSelected = in_array((string)$group->id, $group_ids);
                                $isDisabled = $group->is_full && !$isSelected;
                            @endphp
                            <div class="col-md-4 col-lg-3">
                                <label class="permission-item {{ $isSelected ? 'is-selected' : '' }} {{ $isDisabled ? 'opacity-50' : '' }}"
                                       for="grp-{{ $group->id }}"
                                       title="{{ $isDisabled ? 'المجموعة ممتلئة' : '' }}">
                                    <input
                                        type="checkbox"
                                        wire:model.live="group_ids"
                                        value="{{ (string) $group->id }}"
                                        id="grp-{{ $group->id }}"
                                        class="permission-checkbox"
                                        {{ $isSelected ? 'checked' : '' }}
                                        {{ $isDisabled ? 'disabled' : '' }}
                                    >
                                    <div class="permission-content">
                                        <span class="permission-icon">
                                            <i class="ph-duotone ph-users-three"></i>
                                        </span>
                                        <span class="permission-label">
                                            {{ $group->name }}
                                            @if($group->max_students > 0)
                                                <small class="text-muted d-block">
                                                    {{ $group->students_count }}/{{ $group->max_students }}
                                                    @if($group->is_full)
                                                        <span class="badge bg-danger bg-opacity-10 text-danger ms-1">ممتلئة</span>
                                                    @endif
                                                </small>
                                            @endif
                                        </span>
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
                        <span>لا توجد مجموعات متاحة. يمكنك إضافة الطالب بدون مجموعة.</span>
                    </div>
                @endif
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
                <a href="{{ route('teacher.students.index') }}" wire:navigate class="btn btn-light border">
                    <i class="ph-duotone ph-x-circle me-1"></i>إلغاء
                </a>
            </div>
        </div>
    </div>
</div>
