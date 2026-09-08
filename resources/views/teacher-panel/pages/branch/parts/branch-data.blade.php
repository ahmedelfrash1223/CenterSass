<div>
    <!-- List Header -->
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <p class="text-muted mb-0 small">إدارة أفرعك وعناوينها وبيانات التواصل</p>
        </div>
        <a href="{{ route('teacher.branches.create') }}" wire:navigate
           class="btn btn-primary d-flex align-items-center gap-1">
            <i class="ph-duotone ph-plus"></i>
            إضافة فرع
        </a>
    </div>

    <!-- Feedback -->
    @if (session('branch-success'))
        <div class="alert alert-success d-flex align-items-center gap-2 mb-4">
            <i class="ph-duotone ph-check-circle fs-5"></i>
            <span>{{ session('branch-success') }}</span>
        </div>
    @endif

    <!-- Branch Table -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-transparent border-bottom">
            <div class="row align-items-center g-2">
                <div class="col-md-6">
                    <div class="position-relative">
                        <i class="ph-duotone ph-magnifying-glass position-absolute top-50 translate-middle-y ms-3 text-muted"></i>
                        <input type="text" wire:model.live.debounce.400ms="search" class="form-control ps-5"
                               placeholder="ابحث عن فرع...">
                    </div>
                </div>
            </div>
        </div>

        <!-- Table Results -->
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                <tr>
                    <th class="ps-4">#</th>
                    <th>الاسم</th>
                    <th>العنوان</th>
                    <th>الهاتف</th>
                    <th>البريد الإلكتروني</th>
                    <th>الحالة</th>
                    <th class="text-center">الإجراءات</th>
                </tr>
                </thead>
                <tbody>
                @forelse ($branches as $branch)
                    <tr wire:key="branch-{{ $branch->id }}">
                        <td class="ps-4">
                            <span class="text-muted fw-semibold">{{ $loop->iteration }}</span>
                        </td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <div class="avtar avtar-s bg-light-primary text-primary">
                                    <i class="ph-duotone ph-buildings fs-5"></i>
                                </div>
                                <span class="fw-semibold">{{ $branch->name }}</span>
                            </div>
                        </td>
                        <td>{{ $branch->address }}</td>
                        <td>{{ $branch->phone ?: 'غير محدد' }}</td>
                        <td>
                            @if ($branch->email)
                                <a href="mailto:{{ $branch->email }}"
                                   class="text-decoration-none">{{ $branch->email }}</a>
                            @else
                                <span class="text-muted">غير محدد</span>
                            @endif
                        </td>
                        <td>
                            @if ($branch->is_active)
                                <span class="badge bg-success bg-opacity-10 text-success">
                                        <i class="ph-duotone ph-check-circle me-1"></i>نشط
                                    </span>
                            @else
                                <span class="badge bg-danger bg-opacity-10 text-danger">
                                        <i class="ph-duotone ph-x-circle me-1"></i>غير نشط
                                    </span>
                            @endif
                        </td>
                        <td>
                            <div class="d-flex align-items-center justify-content-center gap-3">
                                @if ($deleteId === $branch->id)
                                    <button type="button" wire:click="delete" wire:loading.attr="disabled"
                                            wire:target="delete" class="border-0 bg-transparent p-0 text-success"
                                            title="تأكيد الحذف">
                                        <span wire:loading.remove wire:target="delete"><i
                                                class="ph-duotone ph-check-circle fs-4"></i></span>
                                        <span wire:loading wire:target="delete"
                                              class="spinner-border spinner-border-sm"></span>
                                    </button>
                                    <button type="button" wire:click="cancelDelete"
                                            class="border-0 bg-transparent p-0 text-secondary" title="إلغاء">
                                        <i class="ph-duotone ph-x-circle fs-4"></i>
                                    </button>
                                @else
                                    <a href="{{ route('teacher.branches.edit', $branch) }}" wire:navigate
                                       class="border-0 bg-transparent p-0 text-primary" title="تعديل">
                                        <i class="ph-duotone ph-pencil-simple fs-4"></i>
                                    </a>
                                    <button type="button" wire:click="confirmDelete({{ $branch->id }})"
                                            class="border-0 bg-transparent p-0 text-danger" title="حذف">
                                        <i class="ph-duotone ph-trash fs-4"></i>
                                    </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center py-5">
                            <div class="text-muted">
                                <i class="ph-duotone ph-buildings fs-1 d-block mb-2"></i>
                                <h6>{{ $search || $status !== '' ? 'لا توجد نتائج' : 'لا توجد أفرع حتى الآن' }}</h6>
                                @if (! $search && $status === '')
                                    <small>ابدأ بإضافة أول فرع.</small>
                                @endif
                            </div>
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if ($branches->hasPages())
            <div class="card-footer bg-transparent border-top">
                {{ $branches->links() }}
            </div>
        @endif
    </div>
</div>
