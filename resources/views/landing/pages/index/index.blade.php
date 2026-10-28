@section('index-active','active')
@section('title','منصة نبذة التعليمية')
@extends('landing.master')
@section('main')
    <!-- Hero -->
    @include('landing.parts.hero')
    <!-- Portals -->
    <section class="container-xl px-3 px-lg-4 pb-5">
        <div class="text-center mb-5">
            <div class="text-secondary fw-bold small text-uppercase mb-1">
                <span class="d-inline-block rounded-circle bg-secondary" style="width:6px;height:6px;"></span>
                بوابات الدخول السريع
            </div>
            <h2 class="fw-bold">اختر بوابتك للمتابعة</h2>
            <p class="text-on-surface-variant small mt-1">سجل دخولك المباشر إلى حسابك المخصص بتجربة آمنة ولحظية</p>
        </div>

        <div class="row g-4 align-items-stretch">
            <!-- Student -->
            <div class="col-12 col-md-4">
                <div class="card-token p-4 p-lg-4 h-100 d-flex flex-column justify-content-between position-relative">
                    <div>
                        <div class="icon-capsule mb-4"><span class="material-symbols-outlined fs-3">school</span></div>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h3 class="fw-bold fs-4 mb-0">بوابة الطالب</h3>
                            <span class="feature-tag">مساحة التعلم</span>
                        </div>
                        <p class="text-on-surface-variant mb-4">تابع دروسك المباشرة، سلّم واجباتك اليومية، واستعرض
                            تقييماتك وشهاداتك أولاً بأول بتفاعل ذكي.</p>
                        <div class="d-flex flex-wrap gap-2 mb-4">
                            <span class="feature-tag"><span class="material-symbols-outlined text-secondary"
                                                            style="font-size:14px;">calendar_today</span>الجدول الدراسي</span>
                            <span class="feature-tag"><span class="material-symbols-outlined text-secondary"
                                                            style="font-size:14px;">task</span>الواجبات</span>
                            <span class="feature-tag"><span class="material-symbols-outlined text-secondary"
                                                            style="font-size:14px;">grade</span>الدرجات</span>
                        </div>
                    </div>
                    <a href="{{route('landing.student-portal')}}" class="btn-token" type="button" data-portal-btn="بوابة الطالب">
                        <span>الدخول كطالب</span>
                        <span class="material-symbols-outlined">arrow_back</span>
                    </a>
                </div>
            </div>

            <!-- Teacher (featured) -->
            <div class="col-12 col-md-4">
                <div
                    class="card-token featured p-4 p-lg-4 h-100 d-flex flex-column justify-content-between position-relative">
                    <span class="featured-badge">المركز الأكاديمي الأكثر نشاطاً</span>
                    <div>
                        <div class="icon-capsule primary-icon mb-4"><span class="material-symbols-outlined fs-3">cast_for_education</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h3 class="fw-bold fs-4 mb-0">بوابة المعلم</h3>
                            <span class="feature-tag"
                                  style="background:color-mix(in srgb, var(--primary-fixed) 40%, transparent);color:var(--on-primary-fixed-variant);">لوحة القيادة</span>
                        </div>
                        <p class="text-on-surface-variant mb-4">أدر حصصك، راقب حضور وانضباط طلابك، وصمم الاختبارات
                            الآلية والتقارير المالية بنقرة واحدة.</p>
                        <div class="d-flex flex-wrap gap-2 mb-4">
                            <span class="feature-tag"><span class="material-symbols-outlined text-primary"
                                                            style="font-size:14px;">video_camera_front</span>إدارة الحصص</span>
                            <span class="feature-tag"><span class="material-symbols-outlined text-primary"
                                                            style="font-size:14px;">fact_check</span>سجل الحضور</span>
                            <span class="feature-tag"><span class="material-symbols-outlined text-primary"
                                                            style="font-size:14px;">analytics</span>التقارير المالية</span>
                        </div>
                    </div>
                    <a href="{{ route('teacher.dashboard') }}"
                       wire:navigate
                       class="btn-token solid"
                       data-portal-btn="بوابة المعلم">
                        <span>الدخول كمعلم</span>
                        <span class="material-symbols-outlined">arrow_back</span>
                    </a>
                </div>
            </div>

            <!-- Parent -->
            <div class="col-12 col-md-4">
                <div class="card-token p-4 p-lg-4 h-100 d-flex flex-column justify-content-between position-relative">
                    <div>
                        <div class="icon-capsule mb-4"><span
                                class="material-symbols-outlined fs-3">family_restroom</span></div>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h3 class="fw-bold fs-4 mb-0">بوابة ولي الأمر</h3>
                            <span class="feature-tag">متابعة دقيقة</span>
                        </div>
                        <p class="text-on-surface-variant mb-4">اطمئن على مستوى أبنائك الأكاديمي، سجلات الحضور
                            والانصراف، مع نافذة محادثة مباشرة مع المعلمين.</p>
                        <div class="d-flex flex-wrap gap-2 mb-4">
                            <span class="feature-tag"><span class="material-symbols-outlined text-secondary"
                                                            style="font-size:14px;">insights</span>تقارير الأداء</span>
                            <span class="feature-tag"><span class="material-symbols-outlined text-secondary"
                                                            style="font-size:14px;">verified_user</span>متابعة الحضور</span>
                            <span class="feature-tag"><span class="material-symbols-outlined text-secondary"
                                                            style="font-size:14px;">forum</span>التواصل الفوري</span>
                        </div>
                    </div>
                    <button class="btn-token" type="button" data-portal-btn="بوابة ولي الأمر">
                        <span>الدخول كولي أمر</span>
                        <span class="material-symbols-outlined">arrow_back</span>
                    </button>
                </div>
            </div>
        </div>
    </section>

    <!-- Stats / dashboard mock -->
    <section class="container-xl px-3 px-lg-4 pb-5">
        <div class="panel-token p-4 p-lg-5">
            <div class="row g-4 align-items-center">
                <div class="col-12 col-lg-5 text-end order-2 order-lg-1">
                    <div class="feature-tag mb-2"
                         style="background:color-mix(in srgb, var(--primary-fixed) 30%, transparent);color:var(--on-primary-fixed-variant);">
                        <span class="material-symbols-outlined text-primary" style="font-size:16px;">insights</span>
                        تحديث فوري للبيانات
                    </div>
                    <h3 class="fw-bold mb-3">رؤية شاملة وإحصاءات دقيقة في مكان واحد</h3>
                    <p class="text-on-surface-variant mb-4">تمنحك نبذة تحليلات تفاعلية لمستوى التحصيل العلمي للطلاب،
                        معدلات التفاعل في الدروس الخصوصية، ومعدلات الحضور الأسبوعية بنظام ذكي ومبسط.</p>
                    <div class="row g-3">
                        <div class="col-6">
                            <div class="stat-box p-3">
                                <span class="fs-4 fw-bold text-secondary d-block mb-1">99.8%</span>
                                <span class="small text-on-surface-variant fw-medium">معدل رضا أولياء الأمور</span>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="stat-box p-3">
                                <span class="fs-4 fw-bold text-primary d-block mb-1">3.5x</span>
                                <span class="small text-on-surface-variant fw-medium">تسريع متابعة الواجبات</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-lg-7 order-1 order-lg-2">
                    <div class="dash-card p-4">
                        <div
                            class="d-flex align-items-center justify-content-between pb-3 mb-3 border-bottom border-token">
                            <div class="d-flex align-items-center gap-3">
                                <div class="rounded-3 d-flex align-items-center justify-content-center"
                                     style="width:2.5rem;height:2.5rem;background:color-mix(in srgb, var(--primary) 12%, transparent);color:var(--primary);">
                                    <span class="material-symbols-outlined">analytics</span>
                                </div>
                                <div>
                                    <h4 class="fw-bold mb-0 small">ملخص أداء المجموعات التعليمية</h4>
                                    <p class="text-on-surface-variant mb-0" style="font-size:.72rem;">الفصل الدراسي
                                        الحالي • شهر نوفمبر</p>
                                </div>
                            </div>
                            <span class="feature-tag"
                                  style="background:color-mix(in srgb, var(--primary-fixed) 40%, transparent);color:var(--on-primary-fixed-variant);font-weight:700;">مباشر الآن</span>
                        </div>

                        <div class="d-flex justify-content-between small text-on-surface-variant mb-2">
                            <span>نسبة إنجاز المهام الدراسية</span>
                            <span class="fw-bold text-secondary">88%</span>
                        </div>
                        <div class="progress-token mb-3">
                            <div class="bar" style="width:88%;"></div>
                        </div>

                        <svg viewBox="0 0 460 90" class="w-100 mb-3" style="height:5rem;color:var(--secondary);"
                             fill="none" xmlns="http://www.w3.org/2000/svg">
                            <defs>
                                <linearGradient id="chartGrad" x1="0" x2="0" y1="0" y2="1">
                                    <stop offset="0%" stop-color="#00DFA2" stop-opacity="0.35"/>
                                    <stop offset="100%" stop-color="#0094FD" stop-opacity="0"/>
                                </linearGradient>
                            </defs>
                            <path d="M0,70 Q60,20 120,45 T240,25 T360,40 T460,10 L460,90 L0,90 Z"
                                  fill="url(#chartGrad)"/>
                            <path d="M0,70 Q60,20 120,45 T240,25 T360,40 T460,10" stroke="currentColor"
                                  stroke-linecap="round" stroke-width="3"/>
                            <circle cx="240" cy="25" r="4" fill="#00DFA2" stroke="#fff" stroke-width="2"/>
                            <circle cx="460" cy="10" r="4" fill="#0094FD" stroke="#fff" stroke-width="2"/>
                        </svg>

                        <div class="row g-2">
                            <div class="col-12 col-sm-6">
                                <div class="activity-row">
                                    <span class="rounded-circle bg-primary" style="width:8px;height:8px;"></span>
                                    <div class="text-truncate">
                                        <p class="fw-semibold mb-0 small text-truncate">تم تسليم واجب الرياضيات</p>
                                        <p class="text-on-surface-variant mb-0 text-truncate" style="font-size:.72rem;">
                                            سارة أحمد • منذ 5 دقائق</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 col-sm-6">
                                <div class="activity-row">
                                    <span class="rounded-circle"
                                          style="width:8px;height:8px;background:var(--secondary-container);"></span>
                                    <div class="text-truncate">
                                        <p class="fw-semibold mb-0 small text-truncate">تم بدء حصة الفيزياء</p>
                                        <p class="text-on-surface-variant mb-0 text-truncate" style="font-size:.72rem;">
                                            أ. طارق محمود • الآن</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Trust strip -->
    <section class="trust-strip py-4 px-3 px-lg-4">
        <div
            class="container-xl d-flex flex-column flex-md-row align-items-center justify-content-around gap-4 text-center text-md-start">
            <div class="d-flex align-items-center gap-3">
                <div class="rounded-3 d-flex align-items-center justify-content-center flex-shrink-0"
                     style="width:2.5rem;height:2.5rem;background:color-mix(in srgb, var(--primary-container) 20%, transparent);color:var(--on-primary-container);">
                    <span class="material-symbols-outlined">verified_user</span>
                </div>
                <div>
                    <h4 class="fw-bold mb-0 small">حماية وتشفير متكامل</h4>
                    <p class="text-on-surface-variant mb-0 small">تأمين تام للبيانات والتقارير الأكاديمية</p>
                </div>
            </div>
            <div class="d-flex align-items-center gap-3">
                <div class="rounded-3 d-flex align-items-center justify-content-center flex-shrink-0"
                     style="width:2.5rem;height:2.5rem;background:color-mix(in srgb, var(--secondary-fixed) 50%, transparent);color:var(--secondary);">
                    <span class="material-symbols-outlined">notifications_active</span>
                </div>
                <div>
                    <h4 class="fw-bold mb-0 small">إشعارات وتنبيهات فورية</h4>
                    <p class="text-on-surface-variant mb-0 small">تحديثات لحظية للحصص والملاحظات عبر التطبيق</p>
                </div>
            </div>
            <div class="d-flex align-items-center gap-3">
                <div class="rounded-3 d-flex align-items-center justify-content-center flex-shrink-0"
                     style="width:2.5rem;height:2.5rem;background:color-mix(in srgb, var(--tertiary-container) 30%, transparent);color:var(--on-tertiary-container);">
                    <span class="material-symbols-outlined">devices</span>
                </div>
                <div>
                    <h4 class="fw-bold mb-0 small">واجهة سهلة وسريعة</h4>
                    <p class="text-on-surface-variant mb-0 small">متوافقة بالكامل مع الهواتف الذكية والأجهزة اللوحية</p>
                </div>
            </div>
        </div>
    </section>
@endsection
