<div>
    <!-- List Header -->
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <p class="text-muted mb-0 small">إدارة المدرسين والحسابات والصلاحيات</p>
        </div>

        <a href="{{ route('admin.teacher.create') }}" wire:navigate class="btn btn-primary d-flex align-items-center gap-1">
            <i class="ph-duotone ph-user-plus"></i>
            إضافة مدرس
        </a>
    </div>

    <!-- Success Feedback -->
    @if(session('teacher-success'))
        <div class="alert alert-success d-flex align-items-center gap-2 mb-4">
            <i class="ph-duotone ph-check-circle fs-5"></i>
            <span>{{ session('teacher-success') }}</span>
        </div>
    @endif

    <!-- Error Feedback -->
    @if(session('teacher-error'))
        <div class="alert alert-danger d-flex align-items-center gap-2 mb-4">
            <i class="ph-duotone ph-x-circle fs-5"></i>
            <span>{{ session('teacher-error') }}</span>
        </div>
    @endif

    <!-- Filters Card -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <div class="row g-2">
                <!-- Search -->
                <div class="col-md-4">
                    <div class="position-relative">
                        <i class="ph-duotone ph-magnifying-glass position-absolute top-50 translate-middle-y ms-3 text-muted"></i>
                        <input type="text" wire:model.live.debounce.400ms="search" class="form-control ps-5" placeholder="ابحث عن مدرس...">
                    </div>
                </div>

                <!-- Role Filter -->
                <div class="col-md-3">
                    <select wire:model.live="roleFilter" class="form-select">
                        <option value="">كل الأنواع</option>
                        @foreach($roles as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Status Filter -->
                <div class="col-md-2">
                    <select wire:model.live="statusFilter" class="form-select">
                        <option value="">كل الحالات</option>
                        <option value="1">نشط</option>
                        <option value="0">غير نشط</option>
                    </select>
                </div>

                <!-- Owner Filter -->
                <div class="col-md-3">
                    <select wire:model.live="ownerFilter" class="form-select">
                        <option value="">كل الحسابات</option>
                        <option value="owners_only">المسئولين فقط</option>
                        <option value="staff_only">المدرسين والمساعدين</option>
                        @foreach($owners as $owner)
                            <option value="{{ $owner->id }}">تابع لـ: {{ $owner->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    </div>

    <!-- Teacher Table -->
    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <!-- Table Header -->
                <thead>
                <tr>
                    <th class="ps-4">#</th>
                    <th>المدرس</th>
                    <th>نوع الحساب</th>
                    <th>تابع لـ</th>
                    <th>البريد الإلكتروني</th>
                    <th>رقم الهاتف</th>
                    <th>الحالة</th>
                    <th class="text-center">الإجراءات</th>
                </tr>
                </thead>

                <!-- Table Body -->
                <tbody>
                @forelse($teachers as $teacher)
                    <tr wire:key="teacher-{{ $teacher->id }}">
                        <!-- ID -->
                        <td class="ps-4">
                            <span class="text-muted fw-semibold">{{ $teachers->firstItem() + $loop->index }}</span>
                        </td>

                        <!-- Teacher Info -->
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <div class="avtar avtar-s bg-{{ $teacher->role->color() }} bg-opacity-10 text-{{ $teacher->role->color() }}">
                                    <i class="ph-duotone {{ $teacher->role->icon() }} fs-5"></i>
                                </div>

                                <div>
                                    <h6 class="mb-0 fw-semibold">{{ $teacher->name }}</h6>
                                    <small class="text-muted">{{ $teacher->code }}</small>
                                </div>
                            </div>
                        </td>

                        <!-- Role -->
                        <td>
                                <span class="badge bg-{{ $teacher->role->color() }} bg-opacity-10 text-{{ $teacher->role->color() }}">
                                    <i class="ph-duotone {{ $teacher->role->icon() }} me-1"></i>
                                    {{ $teacher->role->label() }}
                                </span>
                        </td>

                        <!-- Owner -->
                        <td>
                            @if($teacher->owner)
                                <div class="d-flex align-items-center gap-1">
                                    <i class="ph-duotone ph-crown text-warning"></i>
                                    <span class="small fw-semibold">{{ $teacher->owner->name }}</span>
                                </div>
                            @else
                                <span class="text-muted small">
                                        <i class="ph-duotone ph-crown text-warning me-1"></i>
                                        صاحب الحساب
                                    </span>
                            @endif
                        </td>

                        <!-- Email -->
                        <td>
                            <a href="mailto:{{ $teacher->email }}" class="text-decoration-none small">
                                {{ $teacher->email }}
                            </a>
                        </td>

                        <!-- Phone -->
                        <td>
                            @if($teacher->phone)
                                <a href="tel:{{ $teacher->phone }}" class="text-decoration-none small">
                                    {{ $teacher->phone }}
                                </a>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>

                        <!-- Status -->
                        <td>
                            @if($teacher->is_active)
                                <span class="badge bg-success bg-opacity-10 text-success">
                                        <i class="ph-duotone ph-check-circle me-1"></i>
                                        نشط
                                    </span>
                            @else
                                <span class="badge bg-danger bg-opacity-10 text-danger">
                                        <i class="ph-duotone ph-x-circle me-1"></i>
                                        غير نشط
                                    </span>
                            @endif
                        </td>

                        <!-- Actions -->
                        <td>
                            <div class="d-flex align-items-center justify-content-center gap-3">
                                @if($deleteId === $teacher->id)
                                    <button type="button" wire:click="delete" class="border-0 bg-transparent p-0 text-success text-decoration-none" title="تأكيد الحذف" wire:loading.attr="disabled">
                                            <span wire:loading.remove wire:target="delete">
                                                <i class="ph-duotone ph-check-circle fs-4"></i>
                                            </span>
                                        <span wire:loading wire:target="delete" class="spinner-border spinner-border-sm"></span>
                                    </button>

                                    <button type="button" wire:click="cancelDelete" class="border-0 bg-transparent p-0 text-secondary text-decoration-none" title="إلغاء">
                                        <i class="ph-duotone ph-x-circle fs-4"></i>
                                    </button>
                                @else
                                    <a wire:navigate href="{{ route('admin.teacher.show', $teacher->id) }}" class="border-0 bg-transparent p-0 text-info text-decoration-none" title="عرض">
                                        <i class="ph-duotone ph-eye fs-4"></i>
                                    </a>

                                    <a wire:navigate href="{{ route('admin.teacher.edit', $teacher->id) }}" class="border-0 bg-transparent p-0 text-primary text-decoration-none" title="تعديل">
                                        <i class="ph-duotone ph-pencil-simple fs-4"></i>
                                    </a>

                                    <button type="button" wire:click="confirmDelete({{ $teacher->id }})" class="border-0 bg-transparent p-0 text-danger text-decoration-none" title="حذف">
                                        <i class="ph-duotone ph-trash fs-4"></i>
                                    </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <!-- Empty State -->
                    <tr>
                        <td colspan="8" class="text-center py-5">
                            <div class="text-muted">
                                <i class="ph-duotone ph-user fs-1 d-block mb-2"></i>
                                @if($search || $roleFilter || $statusFilter || $ownerFilter)
                                    <h6 class="mb-1">لا توجد نتائج</h6>
                                    <small>لم يتم العثور على نتائج مطابقة للفلاتر المحددة.</small>
                                @else
                                    <h6 class="mb-1">لا يوجد مدرسين حتى الآن</h6>
                                    <small>ابدأ بإضافة أول مدرس.</small>
                                @endif
                            </div>
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($teachers->hasPages())
            <div class="card-footer bg-transparent border-top">
                {{ $teachers->links() }}
            </div>
        @endif
    </div>
</div>
