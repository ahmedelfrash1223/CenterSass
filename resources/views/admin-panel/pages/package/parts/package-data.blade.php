<div>
    <!-- Header -->
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <p class="text-muted mb-0 small">إدارة الباقات والأسعار والخصائص</p>
        </div>

        <a
            href="{{ route('admin.package.create') }}"
            wire:navigate
            class="btn btn-primary d-flex align-items-center gap-1"
        >
            <i class="ph-duotone ph-plus"></i>
            إضافة باقة
        </a>
    </div>

    <!-- Success Message -->
    @if (session('package-success'))
        <div class="alert alert-success d-flex align-items-center gap-2 mb-4">
            <i class="ph-duotone ph-check-circle fs-5"></i>
            <span>{{ session('package-success') }}</span>
        </div>
    @endif

    <!-- Table Card -->
    <div class="card border-0 shadow-sm">

        <!-- Search -->
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
                            placeholder="ابحث عن اسم الباقة..."
                        >
                    </div>
                </div>
            </div>
        </div>

        <!-- Table -->
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">

                <!-- Table Header -->
                <thead>
                    <tr>
                        <th class="ps-4">#</th>
                        <th>الباقة</th>
                        <th>السعر</th>
                        <th>بعد الخصم</th>
                        <th>المدة</th>
                        <th>الخصم</th>
                        <th class="text-center">الإجراءات</th>
                    </tr>
                </thead>

                <!-- Table Body -->
                <tbody>

                    @forelse($packages as $package)
                        <tr wire:key="package-{{ $package->id }}">

                            <!-- ID -->
                            <td class="ps-4">
                                <span class="text-muted fw-semibold">
                                    {{ $package->id }}
                                </span>
                            </td>

                            <!-- Package -->
                            <td>
                                <div class="d-flex align-items-center gap-2">

                                    <div class="avtar avtar-s bg-light-primary text-primary">
                                        <i class="ph-duotone ph-package fs-5"></i>
                                    </div>

                                    <div>
                                        <h6 class="mb-0 fw-semibold">
                                            {{ $package->title }}
                                        </h6>

                                        <small class="text-muted">
                                            {{ $package->features->count() }} خصائص
                                        </small>
                                    </div>

                                </div>
                            </td>

                            <!-- Price -->
                            <td>
                                <span class="fw-semibold">
                                    {{ number_format($package->price, 2) }}
                                </span>
                                <small class="text-muted">ج.م</small>
                            </td>

                            <!-- Price After Discount -->
                            <td>
                                <span class="fw-semibold text-success">
                                    {{ number_format($package->price_after_discount, 2) }}
                                </span>
                                <small class="text-muted">ج.م</small>
                            </td>

                            <!-- Duration -->
                            <td>
                                <span class="badge bg-light-primary text-primary">
                                    {{ $package->duration }}
                                    {{ $package->duration_type->label() }}
                                </span>
                            </td>

                            <!-- Discount -->
                            <td>
                                @if ($package->discount_value > 0)
                                    <span class="badge bg-light-warning text-warning">
                                        {{ $package->discount_value }}
                                        {{ $package->discount_type->label() }}
                                    </span>
                                @else
                                    <span class="text-muted">
                                        بدون خصم
                                    </span>
                                @endif
                            </td>

                            <!-- Actions -->
                            <td>

                                <div class="d-flex align-items-center justify-content-center gap-3">

                                    @if ($deleteId === $package->id)
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
                                            href="{{ route('admin.package.show', $package->id) }}"
                                            class="border-0 bg-transparent p-0 text-info text-decoration-none"
                                            title="عرض"
                                        >
                                            <i class="ph-duotone ph-eye fs-4"></i>
                                        </a>

                                        <a
                                            wire:navigate
                                            href="{{ route('admin.package.edit', $package->id) }}"
                                            class="border-0 bg-transparent p-0 text-primary text-decoration-none"
                                            title="تعديل"
                                        >
                                            <i class="ph-duotone ph-pencil-simple fs-4"></i>
                                        </a>

                                        <button
                                            type="button"
                                            wire:click="confirmDelete({{ $package->id }})"
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

                                    <i class="ph-duotone ph-package fs-1 d-block mb-2"></i>

                                    @if ($search)
                                        <h6 class="mb-1">
                                            لا توجد نتائج
                                        </h6>

                                        <small>
                                            لم يتم العثور على باقة بهذا الاسم.
                                        </small>
                                    @else
                                        <h6 class="mb-1">
                                            لا توجد باقات حتى الآن
                                        </h6>

                                        <small>
                                            ابدأ بإضافة أول باقة.
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
        @if ($packages->hasPages())
            <div class="card-footer bg-transparent border-top">
                {{ $packages->links() }}
            </div>
        @endif

    </div>
</div>
