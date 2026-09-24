<div class="row g-3 mb-4">
    <div class="col-md-6 col-lg-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div class="rounded-3 d-flex align-items-center justify-content-center bg-primary-subtle text-primary"
                         style="width: 48px; height: 48px;">
                        <i class="ph-duotone ph-student fs-4"></i>
                    </div>
                    <i class="ph-duotone ph-trend-up text-success"></i>
                </div>
                <h3 class="mb-1 fw-bold">{{ number_format($stats['students_count']) }}</h3>
                <p class="text-body-secondary mb-0 small">
                    <i class="ph-duotone ph-student me-1"></i>
                    إجمالي الطلاب
                </p>
            </div>
        </div>
    </div>

    <div class="col-md-6 col-lg-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div class="rounded-3 d-flex align-items-center justify-content-center bg-success-subtle text-success"
                         style="width: 48px; height: 48px;">
                        <i class="ph-duotone ph-users-three fs-4"></i>
                    </div>
                    <i class="ph-duotone ph-trend-up text-success"></i>
                </div>
                <h3 class="mb-1 fw-bold">{{ number_format($stats['groups_count']) }}</h3>
                <p class="text-body-secondary mb-0 small">
                    <i class="ph-duotone ph-users-three me-1"></i>
                    إجمالي المجموعات
                </p>
            </div>
        </div>
    </div>

    <div class="col-md-6 col-lg-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div class="rounded-3 d-flex align-items-center justify-content-center bg-warning-subtle text-warning"
                         style="width: 48px; height: 48px;">
                        <i class="ph-duotone ph-git-branch fs-4"></i>
                    </div>
                    <i class="ph-duotone ph-check-circle text-success"></i>
                </div>
                <h3 class="mb-1 fw-bold">{{ number_format($stats['branches_count']) }}</h3>
                <p class="text-body-secondary mb-0 small">
                    <i class="ph-duotone ph-git-branch me-1"></i>
                    {{ $isOwner ? 'إجمالي الفروع' : 'الفروع المتاحة لك' }}
                </p>
            </div>
        </div>
    </div>

    <div class="col-md-6 col-lg-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div class="rounded-3 d-flex align-items-center justify-content-center bg-info-subtle text-info"
                         style="width: 48px; height: 48px;">
                        <i class="ph-duotone ph-notebook fs-4"></i>
                    </div>
                    <i class="ph-duotone ph-check-circle text-success"></i>
                </div>
                <h3 class="mb-1 fw-bold">{{ number_format($stats['subjects_count']) }}</h3>
                <p class="text-body-secondary mb-0 small">
                    <i class="ph-duotone ph-notebook me-1"></i>
                    إجمالي المواد
                </p>
            </div>
        </div>
    </div>
</div>
