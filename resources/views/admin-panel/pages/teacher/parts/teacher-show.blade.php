<div>
    <!-- Success Message -->
    @if(session('teacher-success'))
        <div class="alert alert-success d-flex align-items-center gap-2 mb-4">
            <i class="ph-duotone ph-check-circle fs-5"></i>
            <span>{{ session('teacher-success') }}</span>
        </div>
    @endif

    <!-- Error Message -->
    @if(session('teacher-error'))
        <div class="alert alert-danger d-flex align-items-center gap-2 mb-4">
            <i class="ph-duotone ph-x-circle fs-5"></i>
            <span>{{ session('teacher-error') }}</span>
        </div>
    @endif

    <!-- Teacher Header -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="avtar avtar-l bg-{{ $teacher->role->color() }} bg-opacity-10 text-{{ $teacher->role->color() }}">
                        <i class="ph-duotone {{ $teacher->role->icon() }} fs-2"></i>
                    </div>
                    <div>
                        <h5 class="mb-1 d-flex align-items-center gap-2">
                            {{ $teacher->name }}
                            <span class="badge bg-{{ $teacher->role->color() }} bg-opacity-10 text-{{ $teacher->role->color() }}">
                                <i class="ph-duotone {{ $teacher->role->icon() }} me-1"></i>
                                {{ $teacher->role->label() }}
                            </span>
                        </h5>
                        <span class="text-muted small">
                            <i class="ph-duotone ph-identification-card me-1"></i>
                            {{ $teacher->code }}
                        </span>
                    </div>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('admin.teacher.edit', $teacher) }}" wire:navigate class="btn btn-primary">
                        <i class="ph-duotone ph-pencil-simple me-1"></i>
                        تعديل
                    </a>
                    <a href="{{ route('admin.teacher.index') }}" wire:navigate class="btn btn-light border">
                        <i class="ph-duotone ph-arrow-right me-1"></i>
                        رجوع
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Teacher Details -->
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-transparent border-bottom">
                    <h6 class="mb-0 fw-bold">
                        <i class="ph-duotone ph-info me-1"></i>
                        بيانات المدرس
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-sm-6">
                            <small class="text-muted d-block mb-1">الاسم</small>
                            <span class="fw-semibold">{{ $teacher->name }}</span>
                        </div>
                        <div class="col-sm-6">
                            <small class="text-muted d-block mb-1">اسم المستخدم</small>
                            <span class="fw-semibold">{{ $teacher->username }}</span>
                        </div>
                        <div class="col-sm-6">
                            <small class="text-muted d-block mb-1">البريد الإلكتروني</small>
                            <a href="mailto:{{ $teacher->email }}" class="text-decoration-none small">
                                {{ $teacher->email }}
                            </a>
                        </div>
                        <div class="col-sm-6">
                            <small class="text-muted d-block mb-1">الهاتف</small>
                            @if($teacher->phone)
                                <a href="tel:{{ $teacher->phone }}" class="text-decoration-none small">{{ $teacher->phone }}</a>
                            @else
                                <span class="text-muted">غير محدد</span>
                            @endif
                        </div>
                        <div class="col-sm-6">
                            <small class="text-muted d-block mb-1">نوع الحساب</small>
                            <span class="badge bg-{{ $teacher->role->color() }} bg-opacity-10 text-{{ $teacher->role->color() }}">
                                <i class="ph-duotone {{ $teacher->role->icon() }} me-1"></i>
                                {{ $teacher->role->label() }}
                            </span>
                        </div>
                        <div class="col-sm-6">
                            <small class="text-muted d-block mb-1">تابع لـ</small>
                            @if($teacher->owner)
                                <a href="{{ route('admin.teacher.show', $teacher->owner->id) }}" wire:navigate class="text-decoration-none small">
                                    <i class="ph-duotone ph-crown text-warning me-1"></i>
                                    {{ $teacher->owner->name }}
                                </a>
                            @else
                                <span class="small text-muted">
                                    <i class="ph-duotone ph-crown text-warning me-1"></i>
                                    صاحب الحساب
                                </span>
                            @endif
                        </div>
                        <div class="col-sm-6">
                            <small class="text-muted d-block mb-1">الحالة</small>
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
                        </div>
                        <div class="col-sm-6">
                            <small class="text-muted d-block mb-1">تاريخ الإنشاء</small>
                            <span class="small">
                                <i class="ph-duotone ph-calendar me-1 text-muted"></i>
                                {{ $teacher->created_at?->format('Y-m-d h:i A') }}
                            </span>
                        </div>
                        <div class="col-sm-6">
                            <small class="text-muted d-block mb-1">آخر تحديث</small>
                            <span class="small">
                                <i class="ph-duotone ph-clock me-1 text-muted"></i>
                                {{ $teacher->updated_at?->format('Y-m-d h:i A') }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Team & Stats -->
        <div class="col-lg-6">
            <!-- Staff (لو Owner) -->
            @if($teacher->isOwner() && $teacher->staff->count() > 0)
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-transparent border-bottom d-flex justify-content-between align-items-center">
                        <h6 class="mb-0 fw-bold">
                            <i class="ph-duotone ph-users me-1"></i>
                            الفريق
                        </h6>
                        <span class="badge bg-primary bg-opacity-10 text-primary">
                            {{ $teacher->staff->count() }}
                        </span>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-sm table-hover align-middle mb-0">
                                <thead>
                                <tr>
                                    <th class="ps-3">الاسم</th>
                                    <th>النوع</th>
                                    <th>الحالة</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($teacher->staff as $member)
                                    <tr>
                                        <td class="ps-3">
                                            <a href="{{ route('admin.teacher.show', $member->id) }}" wire:navigate class="text-decoration-none">
                                                <span class="fw-semibold">{{ $member->name }}</span>
                                            </a>
                                        </td>
                                        <td>
                                                <span class="badge bg-{{ $member->role->color() }} bg-opacity-10 text-{{ $member->role->color() }}">
                                                    {{ $member->role->label() }}
                                                </span>
                                        </td>
                                        <td>
                                            @if($member->is_active)
                                                <span class="text-success small">
                                                        <i class="ph-duotone ph-check-circle"></i>
                                                    </span>
                                            @else
                                                <span class="text-danger small">
                                                        <i class="ph-duotone ph-x-circle"></i>
                                                    </span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Students -->
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-transparent border-bottom d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-bold">
                        <i class="ph-duotone ph-student me-1"></i>
                        الطلاب
                    </h6>
                    <span class="badge bg-primary bg-opacity-10 text-primary">
                        {{ $teacher->students->count() }}
                    </span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-sm table-hover align-middle mb-0">
                            <thead>
                            <tr>
                                <th class="ps-3">الاسم</th>
                                <th>البريد</th>
                                <th>الحالة</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($teacher->students as $student)
                                <tr>
                                    <td class="ps-3">
                                        <span class="fw-semibold">{{ $student->name }}</span>
                                    </td>
                                    <td>
                                        <small class="text-muted">{{ $student->email }}</small>
                                    </td>
                                    <td>
                                        @if($student->is_active)
                                            <span class="badge bg-success bg-opacity-10 text-success">نشط</span>
                                        @else
                                            <span class="badge bg-danger bg-opacity-10 text-danger">غير نشط</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center text-muted py-4">
                                        <i class="ph-duotone ph-users fs-3 d-block mb-1"></i>
                                        لا يوجد طلاب
                                    </td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- ✅ Permissions Section -->
        @if(!$teacher->isOwner())
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-transparent border-bottom d-flex justify-content-between align-items-center">
                        <h6 class="mb-0 fw-bold">
                            <i class="ph-duotone ph-shield-check me-1"></i>
                            الصلاحيات
                        </h6>
                        <div class="d-flex gap-2">
                            <span class="badge bg-success bg-opacity-10 text-success">
                                <i class="ph-duotone ph-check-circle me-1"></i>
                                {{ count($userPermissions) }} ممنوحة
                            </span>
                            <span class="badge bg-secondary bg-opacity-10 text-secondary">
                                <i class="ph-duotone ph-x-circle me-1"></i>
                                {{ count(\App\Enums\Teacher\TeacherPermissionEnum::values()) - count($userPermissions) }} ممنوعة
                            </span>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            @foreach($groupedPermissions as $groupName => $permissions)
                                @php
                                    $groupGranted = collect($permissions)->filter(fn($p) => in_array($p->value, $userPermissions))->count();
                                    $groupTotal = count($permissions);
                                @endphp
                                <div class="col-md-6 col-lg-4">
                                    <div class="permission-view-group">
                                        <div class="permission-view-header d-flex justify-content-between align-items-center">
                                            <span class="badge bg-primary bg-opacity-10 text-primary">
                                                {{ $groupName }}
                                            </span>
                                            <small class="text-muted">
                                                {{ $groupGranted }}/{{ $groupTotal }}
                                            </small>
                                        </div>
                                        <div class="permission-view-body">
                                            @foreach($permissions as $permission)
                                                @php $isGranted = in_array($permission->value, $userPermissions); @endphp
                                                <div class="permission-view-item {{ $isGranted ? 'is-granted' : 'is-denied' }}">
                                                    <i class="ph-duotone {{ $isGranted ? 'ph-check-circle' : 'ph-x-circle' }}"></i>
                                                    <span>{{ $permission->label() }}</span>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <!-- Delete Section -->
    <div class="d-flex justify-content-end gap-2 mt-4">
        @if($confirmingDelete)
            <button type="button" wire:click="delete" wire:loading.attr="disabled" wire:target="delete" class="btn btn-danger">
                <span wire:loading.remove wire:target="delete">
                    <i class="ph-duotone ph-trash me-1"></i>
                    تأكيد الحذف
                </span>
                <span wire:loading wire:target="delete">
                    <span class="spinner-border spinner-border-sm me-1"></span>
                    جاري الحذف...
                </span>
            </button>
            <button type="button" wire:click="cancelDelete" class="btn btn-light border">
                إلغاء
            </button>
        @else
            <button type="button" wire:click="confirmDelete" class="btn btn-outline-danger">
                <i class="ph-duotone ph-trash me-1"></i>
                حذف
            </button>
        @endif
    </div>
</div>

<style>

</style>
