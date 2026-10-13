<div class="row g-3 mb-4">
    <div class="col-6 col-lg-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="rounded-3 d-flex align-items-center justify-content-center bg-danger-subtle text-danger flex-shrink-0"
                    style="width: 48px; height: 48px;">
                    <i class="ph-duotone ph-user-minus fs-4"></i>
                </div>
                <div class="min-w-0">
                    <div class="fs-4 fw-bold lh-1 text-danger">
                        {{ number_format($stats['students_no_payment']) }}
                    </div>
                    <div class="text-body-secondary small mt-1">مدفعوش حاجة</div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-6 col-lg-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="rounded-3 d-flex align-items-center justify-content-center bg-warning-subtle text-warning flex-shrink-0"
                    style="width: 48px; height: 48px;">
                    <i class="ph-duotone ph-clock fs-4"></i>
                </div>
                <div class="min-w-0">
                    <div class="fs-4 fw-bold lh-1 text-warning">
                        {{ number_format($stats['students_remaining']) }}
                    </div>
                    <div class="text-body-secondary small mt-1">عليهم باقي</div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-6 col-lg-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="rounded-3 d-flex align-items-center justify-content-center bg-success-subtle text-success flex-shrink-0"
                    style="width: 48px; height: 48px;">
                    <i class="ph-duotone ph-check-circle fs-4"></i>
                </div>
                <div class="min-w-0">
                    <div class="fs-4 fw-bold lh-1 text-success">
                        {{ number_format($stats['paid_invoices']) }}
                    </div>
                    <div class="text-body-secondary small mt-1">فواتير مدفوعة</div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-6 col-lg-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="rounded-3 d-flex align-items-center justify-content-center bg-primary-subtle text-primary flex-shrink-0"
                    style="width: 48px; height: 48px;">
                    <i class="ph-duotone ph-currency-circle-dollar fs-4"></i>
                </div>
                <div class="min-w-0">
                    <div class="fs-4 fw-bold lh-1 text-primary">
                        {{ number_format($stats['collected'], 2) }}
                    </div>
                    <div class="text-body-secondary small mt-1">إجمالي المحصّل</div>
                </div>
            </div>
        </div>
    </div>
</div>
