<div class="row g-3 mb-4">
    {{-- Total Invoices --}}
    <div class="col-6 col-lg-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div
                    class="rounded-3 d-flex align-items-center justify-content-center bg-primary-subtle text-primary flex-shrink-0"
                    style="width: 48px; height: 48px;">
                    <i class="ph-duotone ph-receipt fs-4"></i>
                </div>
                <div class="min-w-0">
                    <div class="fs-4 fw-bold lh-1">{{ number_format($stats['total']) }}</div>
                    <div class="text-body-secondary small mt-1">إجمالي الفواتير</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Collected --}}
    <div class="col-6 col-lg-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div
                    class="rounded-3 d-flex align-items-center justify-content-center bg-success-subtle text-success flex-shrink-0"
                    style="width: 48px; height: 48px;">
                    <i class="ph-duotone ph-check-circle fs-4"></i>
                </div>
                <div class="min-w-0">
                    <div class="fs-4 fw-bold lh-1 text-success">{{ number_format($stats['collected'], 2) }}</div>
                    <div class="text-body-secondary small mt-1">المحصّل</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Remaining --}}
    <div class="col-6 col-lg-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div
                    class="rounded-3 d-flex align-items-center justify-content-center bg-danger-subtle text-danger flex-shrink-0"
                    style="width: 48px; height: 48px;">
                    <i class="ph-duotone ph-warning-circle fs-4"></i>
                </div>
                <div class="min-w-0">
                    <div class="fs-4 fw-bold lh-1 text-danger">{{ number_format($stats['remaining'], 2) }}</div>
                    <div class="text-body-secondary small mt-1">المتبقي</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Incomplete --}}
    <div class="col-6 col-lg-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div
                    class="rounded-3 d-flex align-items-center justify-content-center bg-warning-subtle text-warning flex-shrink-0"
                    style="width: 48px; height: 48px;">
                    <i class="ph-duotone ph-clock fs-4"></i>
                </div>
                <div class="min-w-0">
                    <div
                        class="fs-4 fw-bold lh-1 text-warning">{{ number_format($stats['unpaid'] + $stats['partial']) }}</div>
                    <div class="text-body-secondary small mt-1">غير مكتملة</div>
                </div>
            </div>
        </div>
    </div>
</div>
