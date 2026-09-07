<div>
    <!-- Header -->
    <!-- List Header -->
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <p class="text-muted mb-0 small">إدارة المدرسين والحسابات والصلاحيات</p>
        </div>

        <a
            href="{{ route('admin.teacher.create') }}"
            wire:navigate
            class="btn btn-primary d-flex align-items-center gap-1"
        >
            <i class="ph-duotone ph-user-plus"></i>
            إضافة مدرس
        </a>
    </div>

    <!-- Success Message -->
    <!-- Success Feedback -->
    @if (session('teacher-success'))
        <div class="alert alert-success d-flex align-items-center gap-2 mb-4">
            <i class="ph-duotone ph-check-circle fs-5"></i>
            <span>{{ session('teacher-success') }}</span>
        </div>
    @endif

    <!-- Table Card -->
    <!-- Teacher Table -->
    <div class="card border-0 shadow-sm">

        <!-- Search -->
        <!-- Search Controls -->
        <div class="card-header bg-transparent border-bottom">
            <div class="row align-items-center">
                <div class="col-md-5">
                    <div class="position-relative">
                        <i
                            class="ph-duotone ph-magnifying-glass position-absolute top-50 translate-middle-y ms-3 text-muted"></i>

                        <input
                            type="text"
                            wire:model.live.debounce.400ms="search"
                            class="form-control ps-5"
                            placeholder="ابحث عن مدرس (الاسم، البريد، اسم المستخدم، الكود)..."
                        >
                    </div>
                </div>
            </div>
        </div>

        <!-- Table -->
        <!-- Table Results -->
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">

                <!-- Table Header -->
                <thead>
                <tr>
                    <th class="ps-4">#</th>
                    <th>المدرس</th>
                    <th>اسم المستخدم</th>
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
                                <span class="text-muted fw-semibold">
                                    {{ $teacher->id }}
                                </span>
                        </td>

                        <!-- Teacher -->
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <div class="avtar avtar-s bg-light-primary text-primary">
                                    <i class="ph-duotone ph-user fs-5"></i>
                                </div>

                                <div>
                                    <h6 class="mb-0 fw-semibold">
                                        {{ $teacher->name }}
                                    </h6>

                                    <small class="text-muted">
                                        {{ $teacher->code }}
                                    </small>
                                </div>
                            </div>
                        </td>

                        <!-- Username -->
                        <td>
                                <span class="fw-semibold">
                                    {{ $teacher->username }}
                                </span>
                        </td>

                        <!-- Email -->
                        <td>
                            <a
                                href="mailto:{{ $teacher->email }}"
                                class="text-decoration-none"
                            >
                                {{ $teacher->email }}
                            </a>
                        </td>

                        <!-- Phone -->
                        <td>
                            @if ($teacher->phone)
                                <a
                                    href="tel:{{ $teacher->phone }}"
                                    class="text-decoration-none"
                                >
                                    {{ $teacher->phone }}
                                </a>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>

                        <!-- Status -->
                        <td>
                            @if ($teacher->is_active)
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

                                @if ($deleteId === $teacher->id)
                                    <button
                                        type="button"
                                        wire:click="delete"
                                        class="border-0 bg-transparent p-0 text-success text-decoration-none"
                                        title="تأكيد الحذف"
                                        wire:loading.attr="disabled"
                                    ><span
                                            wire:loading.remove
                                            wire:target="delete"
                                        ><i class="ph-duotone ph-check-circle fs-4"></i></span><span
                                            wire:loading
                                            wire:target="delete"
                                            class="spinner-border spinner-border-sm"
                                        ></span>
                                    </button>

                                    <button
                                        type="button"
                                        wire:click="cancelDelete"
                                        class="border-0 bg-transparent p-0 text-secondary text-decoration-none"
                                        title="إلغاء"
                                    >
                                        <i class="ph-duotone ph-x-circle fs-4"></i>
                                    </button>
                                @else
                                    <a
                                        wire:navigate
                                        href="{{ route('admin.teacher.show', $teacher->id) }}"
                                        class="border-0 bg-transparent p-0 text-info text-decoration-none"
                                        title="عرض"
                                    >
                                        <i class="ph-duotone ph-eye fs-4"></i>
                                    </a>

                                    <a
                                        wire:navigate
                                        href="{{ route('admin.teacher.edit', $teacher->id) }}"
                                        class="border-0 bg-transparent p-0 text-primary text-decoration-none"
                                        title="تعديل"
                                    >
                                        <i class="ph-duotone ph-pencil-simple fs-4"></i>
                                    </a>

                                    <button
                                        type="button"
                                        wire:click="confirmDelete({{ $teacher->id }})"
                                        class="border-0 bg-transparent p-0 text-danger text-decoration-none"
                                        title="حذف"
                                    >
                                        <i class="ph-duotone ph-trash fs-4"></i>
                                    </button>
                                @endif

                            </div>
                        </td>

                    </tr>

                @empty

                    <!-- Empty State -->
                    <tr>
                        <td
                            colspan="7"
                            class="text-center py-5"
                        >

                            <div class="text-muted">

                                <i class="ph-duotone ph-user fs-1 d-block mb-2"></i>

                                @if ($search)
                                    <h6 class="mb-1">
                                        لا توجد نتائج
                                    </h6>

                                    <small>
                                        لم يتم العثور على مدرس بهذا الاسم أو البريد الإلكتروني.
                                    </small>
                                @else
                                    <h6 class="mb-1">
                                        لا يوجد مدرسين حتى الآن
                                    </h6>

                                    <small>
                                        ابدأ بإضافة أول مدرس.
                                    </small>
                                @endif

                            </div>

                        </td>
                    </tr>
                @endforelse

                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if ($teachers->hasPages())
            <div class="card-footer bg-transparent border-top">
                {{ $teachers->links() }}
            </div>
        @endif

    </div>
</div>
