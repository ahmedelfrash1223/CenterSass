<div>
    <!-- Overview Statistics -->
    <div class="row g-4">
        <!-- Teacher Statistics -->
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm h-100 dashboard-card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h6 class="text-muted small mb-1">إجمالي المدرسين</h6>
                            <h2 class="mb-0 fw-bold">{{ number_format($totalTeachers) }}</h2>
                        </div>
                        <div class="bg-primary bg-opacity-10 p-3 rounded-3">
                            <i class="ph-duotone ph-chalkboard-teacher fs-3 text-primary"></i>
                        </div>
                    </div>
                    <div class="mt-3 small">
                        <span class="text-success">{{ $activeTeachers }} نشط</span>
                        <span class="text-muted mx-1">|</span>
                        <span class="text-danger">{{ $inactiveTeachers }} غير نشط</span>
                    </div>
                    @include('admin-panel.pages.dashboard.parts.growth', ['growth' => $teachersGrowth])
                </div>
            </div>
        </div>

        <!-- Student Statistics -->
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm h-100 dashboard-card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h6 class="text-muted small mb-1">إجمالي الطلاب</h6>
                            <h2 class="mb-0 fw-bold">{{ number_format($totalStudents) }}</h2>
                        </div>
                        <div class="bg-success bg-opacity-10 p-3 rounded-3">
                            <i class="ph-duotone ph-users fs-3 text-success"></i>
                        </div>
                    </div>
                    <small class="text-muted d-block mt-3">الطلاب المسجلون حالياً</small>
                </div>
            </div>
        </div>

        <!-- Package Statistics -->
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm h-100 dashboard-card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h6 class="text-muted small mb-1">إجمالي الباقات</h6>
                            <h2 class="mb-0 fw-bold">{{ number_format($totalPackages) }}</h2>
                        </div>
                        <div class="bg-info bg-opacity-10 p-3 rounded-3">
                            <i class="ph-duotone ph-package fs-3 text-info"></i>
                        </div>
                    </div>
                    <small class="text-muted d-block mt-3">الباقات المتاحة للمدرسين</small>
                </div>
            </div>
        </div>

        <!-- Subscription Statistics -->
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm h-100 dashboard-card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h6 class="text-muted small mb-1">إجمالي الاشتراكات</h6>
                            <h2 class="mb-0 fw-bold">{{ number_format($totalSubscriptions) }}</h2>
                        </div>
                        <div class="bg-warning bg-opacity-10 p-3 rounded-3">
                            <i class="ph-duotone ph-repeat fs-3 text-warning"></i>
                        </div>
                    </div>
                    <div class="mt-3 small d-flex flex-wrap gap-2">
                        <span class="text-success">نشط {{ $activeSubscriptions }}</span>
                        <span class="text-secondary">منتهي {{ $expiredSubscriptions }}</span>
                        <span class="text-warning">معلق {{ $pendingSubscriptions }}</span>
                    </div>
                    @include('admin-panel.pages.dashboard.parts.growth', ['growth' => $subscriptionsGrowth])
                </div>
            </div>
        </div>

        <!-- Admin Statistics -->
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm h-100 dashboard-card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h6 class="text-muted small mb-1">إجمالي المديرين</h6>
                            <h2 class="mb-0 fw-bold">{{ number_format($totalAdmins) }}</h2>
                        </div>
                        <div class="bg-danger bg-opacity-10 p-3 rounded-3">
                            <i class="ph-duotone ph-shield-user fs-3 text-danger"></i>
                        </div>
                    </div>
                    <small class="text-muted d-block mt-3">حسابات الإدارة</small>
                </div>
            </div>
        </div>

        <!-- Monthly Activity -->
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm h-100 dashboard-card">
                <div class="card-body">
                    <h6 class="text-muted small mb-3">نشاط هذا الشهر</h6>
                    <div class="d-flex justify-content-between mb-2"><span>مدرسون جدد</span><strong>{{ $newTeachersThisMonth }}</strong></div>
                    <div class="progress" style="height: 6px;"><div class="progress-bar bg-primary" style="width: 100%;"></div></div>
                    <div class="d-flex justify-content-between mt-3 mb-2"><span>اشتراكات جديدة</span><strong>{{ $newSubscriptionsThisMonth }}</strong></div>
                    <div class="progress" style="height: 6px;"><div class="progress-bar bg-warning" style="width: 100%;"></div></div>
                </div>
            </div>
        </div>

        <!-- Total Revenue -->
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm h-100 dashboard-card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div><h6 class="text-muted small mb-1">إجمالي الإيرادات</h6><h2 class="mb-0 fw-bold">{{ number_format($totalRevenue, 2) }}</h2><small class="text-muted">ج.م</small></div>
                        <div class="bg-warning bg-opacity-10 p-3 rounded-3"><i class="ph-duotone ph-currency-dollar fs-3 text-warning"></i></div>
                    </div>
                    @include('admin-panel.pages.dashboard.parts.growth', ['growth' => $revenueGrowth])
                </div>
            </div>
        </div>

        <!-- Monthly Revenue -->
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm h-100 dashboard-card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div><h6 class="text-muted small mb-1">إيرادات هذا الشهر</h6><h2 class="mb-0 fw-bold">{{ number_format($monthlyRevenue, 2) }}</h2><small class="text-muted">ج.م</small></div>
                        <div class="bg-warning bg-opacity-10 p-3 rounded-3"><i class="ph-duotone ph-currency-dollar fs-3 text-warning"></i></div>
                    </div>
                    <small class="text-muted d-block mt-3">من الاشتراكات المدفوعة</small>
                </div>
            </div>
        </div>
    </div>
</div>