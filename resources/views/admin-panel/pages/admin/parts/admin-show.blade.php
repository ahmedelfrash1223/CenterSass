<div>
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
                <div>
                    <h5 class="mb-1"><i class="ph-duotone ph-user me-2"></i>{{ $admin->name }}</h5><span
                        class="text-muted"
                    >{{ $admin->code }}</span>
                </div>
                <div class="d-flex gap-2"><a
                        href="{{ route('admin.admins.edit', $admin) }}"
                        wire:navigate
                        class="btn btn-primary"
                    ><i class="ph-duotone ph-pencil-simple me-1"></i>تعديل</a><a
                        href="{{ route('admin.admins.index') }}"
                        wire:navigate
                        class="btn btn-light border"
                    ><i class="ph-duotone ph-arrow-right me-1"></i>رجوع</a></div>
            </div>
        </div>
    </div>
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <h6 class="mb-4">بيانات المدير</h6>
            <div class="row g-3">
                <div class="col-md-4"><small class="text-muted d-block">الاسم</small>{{ $admin->name }}</div>
                <div class="col-md-4"><small class="text-muted d-block">اسم المستخدم</small>{{ $admin->username }}</div>
                <div class="col-md-4"><small class="text-muted d-block">البريد الإلكتروني</small><a
                        href="mailto:{{ $admin->email }}"
                    >{{ $admin->email }}</a></div>
                <div class="col-md-4"><small class="text-muted d-block">الكود</small>{{ $admin->code }}</div>
                <div class="col-md-4"><small class="text-muted d-block">الدور</small>{{ $admin->role ?? 'مدير' }}</div>
                <div class="col-md-4"><small class="text-muted d-block">الحالة</small>
                    @if ($admin->is_active)
                        <span class="badge bg-success bg-opacity-10 text-success">نشط</span>
                    @else
                        <span
                            class="badge bg-danger bg-opacity-10 text-danger"
                        >غير نشط</span>
                    @endif
                </div>
                <div class="col-md-4"><small class="text-muted d-block">آخر
                        دخول</small>{{ $admin->last_login_at?->format('Y-m-d H:i') ?? 'غير متاح' }}</div>
                <div class="col-md-4"><small class="text-muted d-block">تاريخ
                        الإنشاء</small>{{ $admin->created_at?->format('Y-m-d H:i') }}</div>
                <div class="col-md-4"><small class="text-muted d-block">آخر
                        تحديث</small>{{ $admin->updated_at?->format('Y-m-d H:i') }}</div>
            </div>
        </div>
    </div>
    <div class="d-flex justify-content-end gap-2 mt-4">
        @if ($confirmingDelete)
            <button
                type="button"
                wire:click="delete"
                wire:loading.attr="disabled"
                wire:target="delete"
                class="btn btn-danger"
            ><span
                    wire:loading.remove
                    wire:target="delete"
                ><i class="ph-duotone ph-trash me-1"></i>تأكيد الحذف</span><span
                    wire:loading
                    wire:target="delete"
                ><span class="spinner-border spinner-border-sm me-1"></span>جاري
                    الحذف...</span></button>
            <button
                type="button"
                wire:click="cancelDelete"
                class="btn btn-light border"
            >إلغاء
            </button>
        @else
            <button
                type="button"
                wire:click="confirmDelete"
                class="btn btn-outline-danger"
            ><i class="ph-duotone ph-trash me-1"></i>حذف
            </button>
        @endif
    </div>
</div>
