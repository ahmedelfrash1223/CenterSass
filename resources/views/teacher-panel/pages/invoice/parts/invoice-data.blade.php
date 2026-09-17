<div>
    {{-- Filters --}}
    <div class="payment-search-card mb-4">
        <div class="row g-3 align-items-end">
            <div class="col-md-4">
                <label class="filter-label">
                    <i class="ph-duotone ph-magnifying-glass me-1"></i>
                    البحث
                </label>
                <input type="text"
                       wire:model.live.debounce.300ms="search"
                       class="form-control search-input"
                       placeholder="اسم الطالب، رقم الفاتورة..."
                       style="padding-right: 16px;">
            </div>

            <div class="col-md-3">
                <label class="filter-label">
                    <i class="ph-duotone ph-funnel me-1"></i>
                    الحالة
                </label>
                <select wire:model.live="statusFilter" class="form-select filter-input">
                    <option value="">كل الحالات</option>
                    <option value="unpaid">مستحق</option>
                    <option value="partial">مدفوع جزئياً</option>
                    <option value="paid">مدفوع</option>
                    <option value="cancelled">ملغي</option>
                </select>
            </div>

            <div class="col-md-3">
                <label class="filter-label">
                    <i class="ph-duotone ph-calendar me-1"></i>
                    الشهر
                </label>
                <input type="month" wire:model.live="monthFilter" class="form-control filter-input">
            </div>

            <div class="col-md-2">
                <button type="button"
                        wire:click="clearFilters"
                        class="btn btn-light border w-100 filter-input">
                    <i class="ph-duotone ph-funnel-simple me-1"></i>
                    تفريغ
                </button>
            </div>
        </div>
    </div>

    @livewire('teacher-panel.parts.flash-message', [
        'successKey' => 'invoice-success',
        'errorKey' => 'invoice-error',
    ])

    {{-- Stats --}}
    <div class="row g-3 mb-4">
        <div class="col-md-6 col-lg-3">
            <div class="card border-0 shadow-sm stat-card-hover h-100">
                <div class="card-body">
                    <div class="stat-icon-box bg-primary bg-opacity-10 text-primary mb-2">
                        <i class="ph-duotone ph-receipt"></i>
                    </div>
                    <h3 class="mb-1 fw-bold">{{ number_format($stats['total']) }}</h3>
                    <p class="text-muted mb-0 small">إجمالي الفواتير</p>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-lg-3">
            <div class="card border-0 shadow-sm stat-card-hover h-100">
                <div class="card-body">
                    <div class="stat-icon-box bg-danger bg-opacity-10 text-danger mb-2">
                        <i class="ph-duotone ph-currency-circle-dollar"></i>
                    </div>
                    <h3 class="mb-1 fw-bold text-danger">{{ number_format($stats['unpaid_amount'], 2) }}</h3>
                    <p class="text-muted mb-0 small">مستحقات</p>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-lg-3">
            <div class="card border-0 shadow-sm stat-card-hover h-100">
                <div class="card-body">
                    <div class="stat-icon-box bg-warning bg-opacity-10 text-warning mb-2">
                        <i class="ph-duotone ph-clock"></i>
                    </div>
                    <h3 class="mb-1 fw-bold text-warning">{{ number_format($stats['partial_amount'], 2) }}</h3>
                    <p class="text-muted mb-0 small">مدفوع جزئياً</p>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-lg-3">
            <div class="card border-0 shadow-sm stat-card-hover h-100">
                <div class="card-body">
                    <div class="stat-icon-box bg-success bg-opacity-10 text-success mb-2">
                        <i class="ph-duotone ph-check-circle"></i>
                    </div>
                    <h3 class="mb-1 fw-bold text-success">{{ number_format($stats['paid_amount'], 2) }}</h3>
                    <p class="text-muted mb-0 small">مدفوع</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Invoices Table --}}
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                    <tr>
                        <th class="ps-4">#</th>
                        <th>الفاتورة</th>
                        <th>الطالب</th>
                        <th>الشهر</th>
                        <th class="text-center">الإجمالي</th>
                        <th class="text-center">المدفوع</th>
                        <th class="text-center">المتبقي</th>
                        <th class="text-center">الحالة</th>
                        <th class="text-center">إجراءات</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($invoices as $invoice)
                        <tr wire:key="invoice-{{ $invoice->id }}">
                            <td class="ps-4">
                                <span class="text-muted fw-semibold">{{ $invoices->firstItem() + $loop->index }}</span>
                            </td>
                            <td>
                                <span class="fw-semibold small">{{ $invoice->invoice_number }}</span>
                                <small class="text-muted d-block">
                                    {{ $invoice->created_at?->format('Y-m-d') }}
                                </small>
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="student-avatar">{{ mb_substr($invoice->student?->name ?? '?', 0, 1) }}</div>
                                    <div>
                                        <span class="fw-semibold d-block small">{{ $invoice->student?->name }}</span>
                                        <small class="text-muted">{{ $invoice->student?->code }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <small>{{ $invoice->period_label }}</small>
                            </td>
                            <td class="text-center">
                                <span class="fw-bold">{{ number_format((float) $invoice->total, 2) }}</span>
                            </td>
                            <td class="text-center">
                                <span class="text-success fw-semibold">
                                    {{ number_format((float) $invoice->paid_amount, 2) }}
                                </span>
                            </td>
                            <td class="text-center">
                                @if($invoice->remaining_amount > 0)
                                    <span class="text-danger fw-semibold">
                                        {{ number_format((float) $invoice->remaining_amount, 2) }}
                                    </span>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <span class="badge bg-{{ $invoice->status_color }} bg-opacity-10 text-{{ $invoice->status_color }}">
                                    {{ $invoice->status_label }}
                                </span>
                            </td>
                            <td class="text-center">
                                <div class="d-flex justify-content-center gap-2">
                                    <a href="{{ route('teacher.invoices.show', $invoice->id) }}"
                                       wire:navigate
                                       class="btn btn-sm btn-light border"
                                       title="عرض">
                                        <i class="ph-duotone ph-eye"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-5">
                                <i class="ph-duotone ph-receipt fs-1 d-block mb-2 text-muted"></i>
                                <h6 class="mb-1">لا توجد فواتير</h6>
                                <small class="text-muted">لم يتم توليد أي فواتير بعد.</small>
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if($invoices->hasPages())
            <div class="card-footer bg-transparent border-top">
                {{ $invoices->links() }}
            </div>
        @endif
    </div>
</div>
