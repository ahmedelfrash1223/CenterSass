@section('title', 'المميزات ')
@section('features-active','active')
@extends('landing.master')
@section('main')

    {{-- Hero Section --}}
    <section class="container-xl px-3 px-lg-4 pt-5 pb-5 position-relative overflow-hidden">
        <div class="hero-glow-a"></div>

        <div class="row justify-content-center text-center position-relative" style="z-index:1;">
            <div class="col-12 col-lg-9">
                <div class="hero-pill mb-4">
                    <span class="pulse-dot"></span>
                    <span class="small fw-bold">كل ما تحتاجه لإدارة التعليم في مكان واحد</span>
                </div>

                <h1 class="hero-title display-4 fw-bold mb-4">
                    أدوات ذكية تجعل <span class="accent">إدارة الدروس أسهل</span>
                </h1>

                <p class="text-on-surface-variant fs-6 fs-lg-5 mx-auto mb-0" style="max-width:760px;">
                    نبذة تجمع لك إدارة الطلاب والمجموعات والحضور والواجبات
                    والتقييمات والاختبارات والتقارير في منصة واحدة بسيطة وسريعة.
                </p>
            </div>
        </div>
    </section>

    {{-- Main Features Section --}}
    <section class="container-xl px-3 px-lg-4 pb-5">
        <div class="text-center mb-5">
            <div class="text-secondary fw-bold small text-uppercase mb-1">
                <span class="d-inline-block rounded-circle bg-secondary" style="width:6px;height:6px;"></span>
                المميزات الأساسية
            </div>

            <h2 class="fw-bold mb-2">كل شيء تحت سيطرتك</h2>

            <p class="text-on-surface-variant small mt-1 mb-0">
                أدوات مصممة لتسهيل العمل اليومي للمعلم وتحسين تجربة الطالب وولي الأمر.
            </p>
        </div>

        <div class="row g-4">

            {{-- Students & Groups --}}
            <div class="col-12 col-md-6 col-lg-4">
                <div class="card-token p-4 h-100">
                    <div class="icon-capsule mb-4">
                        <span class="material-symbols-outlined fs-3">groups</span>
                    </div>

                    <h3 class="fw-bold fs-5 mb-3">إدارة الطلاب والمجموعات</h3>

                    <p class="text-on-surface-variant mb-4">
                        أنشئ مجموعاتك التعليمية وأضف الطلاب ونظم بياناتهم
                        وتابع كل طالب بسهولة من مكان واحد.
                    </p>

                    <div class="d-flex flex-wrap gap-2">
                        <span class="feature-tag">الطلاب</span>
                        <span class="feature-tag">المجموعات</span>
                        <span class="feature-tag">البيانات</span>
                    </div>
                </div>
            </div>

            {{-- Attendance --}}
            <div class="col-12 col-md-6 col-lg-4">
                <div class="card-token p-4 h-100">
                    <div class="icon-capsule mb-4">
                        <span class="material-symbols-outlined fs-3">fact_check</span>
                    </div>

                    <h3 class="fw-bold fs-5 mb-3">الحضور والغياب</h3>

                    <p class="text-on-surface-variant mb-4">
                        سجل حضور الطلاب وغيابهم بسرعة، واحتفظ بسجل واضح
                        يساعدك على متابعة انتظام كل طالب.
                    </p>

                    <div class="d-flex flex-wrap gap-2">
                        <span class="feature-tag">حضور</span>
                        <span class="feature-tag">غياب</span>
                        <span class="feature-tag">متابعة</span>
                    </div>
                </div>
            </div>

            {{-- Assignments --}}
            <div class="col-12 col-md-6 col-lg-4">
                <div class="card-token p-4 h-100">
                    <div class="icon-capsule mb-4">
                        <span class="material-symbols-outlined fs-3">assignment</span>
                    </div>

                    <h3 class="fw-bold fs-5 mb-3">الواجبات والتكليفات</h3>

                    <p class="text-on-surface-variant mb-4">
                        أنشئ الواجبات والتكليفات وتابع حالة تسليمها
                        واعرف مستوى التزام الطلاب بسهولة.
                    </p>

                    <div class="d-flex flex-wrap gap-2">
                        <span class="feature-tag">واجبات</span>
                        <span class="feature-tag">تسليم</span>
                        <span class="feature-tag">متابعة</span>
                    </div>
                </div>
            </div>

            {{-- Grades --}}
            <div class="col-12 col-md-6 col-lg-4">
                <div class="card-token p-4 h-100">
                    <div class="icon-capsule mb-4">
                        <span class="material-symbols-outlined fs-3">grade</span>
                    </div>

                    <h3 class="fw-bold fs-5 mb-3">التقييمات والدرجات</h3>

                    <p class="text-on-surface-variant mb-4">
                        سجل درجات وتقييمات الطلاب واحتفظ بتاريخ أدائهم
                        لتكوين صورة واضحة عن تطور مستواهم.
                    </p>

                    <div class="d-flex flex-wrap gap-2">
                        <span class="feature-tag">درجات</span>
                        <span class="feature-tag">تقييمات</span>
                        <span class="feature-tag">أداء</span>
                    </div>
                </div>
            </div>

            {{-- Exams --}}
            <div class="col-12 col-md-6 col-lg-4">
                <div class="card-token p-4 h-100">
                    <div class="icon-capsule mb-4">
                        <span class="material-symbols-outlined fs-3">quiz</span>
                    </div>

                    <h3 class="fw-bold fs-5 mb-3">الاختبارات</h3>

                    <p class="text-on-surface-variant mb-4">
                        أنشئ الاختبارات وتابع النتائج وساعد الطلاب على معرفة
                        نقاط القوة والجوانب التي تحتاج إلى تحسين.
                    </p>

                    <div class="d-flex flex-wrap gap-2">
                        <span class="feature-tag">اختبارات</span>
                        <span class="feature-tag">نتائج</span>
                        <span class="feature-tag">تحليل</span>
                    </div>
                </div>
            </div>

            {{-- Reports --}}
            <div class="col-12 col-md-6 col-lg-4">
                <div class="card-token p-4 h-100">
                    <div class="icon-capsule mb-4">
                        <span class="material-symbols-outlined fs-3">analytics</span>
                    </div>

                    <h3 class="fw-bold fs-5 mb-3">التقارير والإحصائيات</h3>

                    <p class="text-on-surface-variant mb-4">
                        احصل على رؤية واضحة للأداء والحضور والواجبات
                        والمؤشرات التعليمية من خلال تقارير منظمة.
                    </p>

                    <div class="d-flex flex-wrap gap-2">
                        <span class="feature-tag">إحصائيات</span>
                        <span class="feature-tag">تقارير</span>
                        <span class="feature-tag">تحليلات</span>
                    </div>
                </div>
            </div>

        </div>
    </section>

    {{-- Teacher Section --}}
    <section class="container-xl px-3 px-lg-4 pb-5">
        <div class="panel-token p-4 p-lg-5">
            <div class="row g-4 align-items-center">

                {{-- Teacher Content --}}
                <div class="col-12 col-lg-6">
                    <div class="feature-tag mb-3" style="background:color-mix(in srgb, var(--primary-fixed) 30%, transparent);color:var(--on-primary-fixed-variant);">
                        <span class="material-symbols-outlined text-primary" style="font-size:16px;">cast_for_education</span>
                        للمعلم
                    </div>

                    <h2 class="fw-bold mb-3">إدارة يومك الدراسي بدون الورق والتشتت</h2>

                    <p class="text-on-surface-variant mb-4">
                        نبذة تساعدك على تنظيم العملية التعليمية بالكامل،
                        بداية من المجموعات والحضور وحتى الواجبات والدرجات والتقارير.
                    </p>

                    <div class="d-flex flex-column gap-3">

                        {{-- Groups --}}
                        <div class="activity-row">
                            <span class="rounded-circle bg-primary" style="width:8px;height:8px;"></span>
                            <div>
                                <p class="fw-semibold mb-0 small">إدارة المجموعات والطلاب</p>
                                <p class="text-on-surface-variant mb-0" style="font-size:.72rem;">كل مجموعة وطلابها في مكان واحد.</p>
                            </div>
                        </div>

                        {{-- Attendance --}}
                        <div class="activity-row">
                            <span class="rounded-circle" style="width:8px;height:8px;background:var(--secondary-container);"></span>
                            <div>
                                <p class="fw-semibold mb-0 small">متابعة الحضور والواجبات</p>
                                <p class="text-on-surface-variant mb-0" style="font-size:.72rem;">سجل ومتابعة مستمرة بدون دفاتر ورقية.</p>
                            </div>
                        </div>

                        {{-- Reports --}}
                        <div class="activity-row">
                            <span class="rounded-circle" style="width:8px;height:8px;background:var(--primary-fixed);"></span>
                            <div>
                                <p class="fw-semibold mb-0 small">تقارير تساعدك على المتابعة</p>
                                <p class="text-on-surface-variant mb-0" style="font-size:.72rem;">بيانات واضحة لاتخاذ قراراتك التعليمية.</p>
                            </div>
                        </div>

                    </div>
                </div>

                {{-- Teacher Dashboard --}}
                <div class="col-12 col-lg-6">
                    <div class="dash-card p-4">

                        <div class="d-flex align-items-center justify-content-between pb-3 mb-3 border-bottom border-token">
                            <div class="d-flex align-items-center gap-3">
                                <div class="rounded-3 d-flex align-items-center justify-content-center" style="width:2.5rem;height:2.5rem;background:color-mix(in srgb,var(--primary) 12%,transparent);color:var(--primary);">
                                    <span class="material-symbols-outlined">dashboard</span>
                                </div>

                                <div>
                                    <h4 class="fw-bold mb-0 small">لوحة المعلم</h4>
                                    <p class="text-on-surface-variant mb-0" style="font-size:.72rem;">نظرة سريعة على يومك الدراسي</p>
                                </div>
                            </div>

                            <span class="feature-tag" style="background:color-mix(in srgb,var(--primary-fixed) 40%,transparent);color:var(--on-primary-fixed-variant);font-weight:700;">
                                مباشر
                            </span>
                        </div>

                        <div class="row g-3">

                            {{-- Groups Stat --}}
                            <div class="col-6">
                                <div class="stat-box p-3">
                                    <span class="material-symbols-outlined text-primary mb-2">groups</span>
                                    <span class="fs-4 fw-bold d-block">12</span>
                                    <span class="small text-on-surface-variant">مجموعة تعليمية</span>
                                </div>
                            </div>

                            {{-- Students Stat --}}
                            <div class="col-6">
                                <div class="stat-box p-3">
                                    <span class="material-symbols-outlined text-secondary mb-2">school</span>
                                    <span class="fs-4 fw-bold d-block">148</span>
                                    <span class="small text-on-surface-variant">طالب</span>
                                </div>
                            </div>

                            {{-- Homework Progress --}}
                            <div class="col-12">
                                <div class="stat-box p-3">
                                    <div class="d-flex justify-content-between mb-2">
                                        <span class="small text-on-surface-variant">إنجاز الواجبات</span>
                                        <span class="small fw-bold text-primary">88%</span>
                                    </div>

                                    <div class="progress-token">
                                        <div class="bar" style="width:88%;"></div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>

    {{-- Student & Parent Section --}}
    <section class="container-xl px-3 px-lg-4 pb-5">
        <div class="row g-4">

            {{-- Student --}}
            <div class="col-12 col-lg-6">
                <div class="card-token p-4 p-lg-5 h-100">

                    <div class="icon-capsule mb-4">
                        <span class="material-symbols-outlined fs-3">school</span>
                    </div>

                    <h3 class="fw-bold mb-3">تجربة تعليمية أفضل للطالب</h3>

                    <p class="text-on-surface-variant mb-4">
                        الطالب يحصل على مساحة منظمة لمتابعة دروسه وواجباته
                        ودرجاته وتقييماته دون الحاجة إلى البحث بين الأوراق والرسائل.
                    </p>

                    <div class="d-flex flex-column gap-3">

                        {{-- Schedule --}}
                        <div class="d-flex align-items-center gap-3">
                            <span class="feature-tag">
                                <span class="material-symbols-outlined text-secondary" style="font-size:15px;">calendar_today</span>
                            </span>
                            <span class="small fw-semibold">متابعة الجدول والدروس</span>
                        </div>

                        {{-- Assignments --}}
                        <div class="d-flex align-items-center gap-3">
                            <span class="feature-tag">
                                <span class="material-symbols-outlined text-secondary" style="font-size:15px;">task</span>
                            </span>
                            <span class="small fw-semibold">متابعة الواجبات والتكليفات</span>
                        </div>

                        {{-- Grades --}}
                        <div class="d-flex align-items-center gap-3">
                            <span class="feature-tag">
                                <span class="material-symbols-outlined text-secondary" style="font-size:15px;">grade</span>
                            </span>
                            <span class="small fw-semibold">معرفة الدرجات والتقييمات</span>
                        </div>

                    </div>
                </div>
            </div>

            {{-- Parent --}}
            <div class="col-12 col-lg-6">
                <div class="card-token featured p-4 p-lg-5 h-100">

                    <div class="icon-capsule primary-icon mb-4">
                        <span class="material-symbols-outlined fs-3">family_restroom</span>
                    </div>

                    <h3 class="fw-bold mb-3">ولي الأمر دائمًا على اطلاع</h3>

                    <p class="text-on-surface-variant mb-4">
                        مساحة مخصصة تساعد ولي الأمر على متابعة مستوى أبنائه
                        والحضور والإنجاز والتواصل بشكل أوضح.
                    </p>

                    <div class="d-flex flex-column gap-3">

                        {{-- Academic Performance --}}
                        <div class="d-flex align-items-center gap-3">
                            <span class="feature-tag" style="background:color-mix(in srgb,var(--primary-fixed) 35%,transparent);color:var(--on-primary-fixed-variant);">
                                <span class="material-symbols-outlined" style="font-size:15px;">insights</span>
                            </span>
                            <span class="small fw-semibold">متابعة الأداء الأكاديمي</span>
                        </div>

                        {{-- Attendance --}}
                        <div class="d-flex align-items-center gap-3">
                            <span class="feature-tag" style="background:color-mix(in srgb,var(--primary-fixed) 35%,transparent);color:var(--on-primary-fixed-variant);">
                                <span class="material-symbols-outlined" style="font-size:15px;">verified_user</span>
                            </span>
                            <span class="small fw-semibold">متابعة الحضور والانصراف</span>
                        </div>

                        {{-- Communication --}}
                        <div class="d-flex align-items-center gap-3">
                            <span class="feature-tag" style="background:color-mix(in srgb,var(--primary-fixed) 35%,transparent);color:var(--on-primary-fixed-variant);">
                                <span class="material-symbols-outlined" style="font-size:15px;">forum</span>
                            </span>
                            <span class="small fw-semibold">التواصل والمتابعة المستمرة</span>
                        </div>

                    </div>
                </div>
            </div>

        </div>
    </section>

    {{-- Analytics Section --}}
    <section class="container-xl px-3 px-lg-4 pb-5">
        <div class="panel-token p-4 p-lg-5">

            <div class="row g-4 align-items-center">

                {{-- Analytics Content --}}
                <div class="col-12 col-lg-5">

                    <div class="feature-tag mb-3" style="background:color-mix(in srgb,var(--secondary-container) 18%,transparent);color:var(--secondary);">
                        <span class="material-symbols-outlined" style="font-size:16px;">analytics</span>
                        بيانات واضحة
                    </div>

                    <h2 class="fw-bold mb-3">افهم أداء طلابك من خلال البيانات</h2>

                    <p class="text-on-surface-variant mb-4">
                        بدلًا من الاعتماد على الملاحظات المتفرقة،
                        تجمع نبذة المعلومات المهمة في صورة منظمة تساعدك
                        على متابعة التطور التعليمي.
                    </p>

                    <div class="row g-3">

                        {{-- Tasks Stat --}}
                        <div class="col-6">
                            <div class="stat-box p-3">
                                <span class="fs-4 fw-bold text-primary d-block mb-1">88%</span>
                                <span class="small text-on-surface-variant fw-medium">إنجاز المهام</span>
                            </div>
                        </div>

                        {{-- Attendance Stat --}}
                        <div class="col-6">
                            <div class="stat-box p-3">
                                <span class="fs-4 fw-bold text-secondary d-block mb-1">94%</span>
                                <span class="small text-on-surface-variant fw-medium">انتظام الحضور</span>
                            </div>
                        </div>

                    </div>
                </div>

                {{-- Analytics Chart --}}
                <div class="col-12 col-lg-7">
                    <div class="dash-card p-4">

                        <div class="d-flex align-items-center justify-content-between mb-4">

                            <div>
                                <h4 class="fw-bold mb-1">ملخص الأداء</h4>
                                <p class="text-on-surface-variant mb-0" style="font-size:.72rem;">آخر تحديث منذ لحظات</p>
                            </div>

                            <div class="icon-capsule" style="width:2.75rem;height:2.75rem;">
                                <span class="material-symbols-outlined">trending_up</span>
                            </div>

                        </div>

                        <div class="d-flex justify-content-between small text-on-surface-variant mb-2">
                            <span>متوسط إنجاز الطلاب</span>
                            <span class="fw-bold text-primary">82%</span>
                        </div>

                        <div class="progress-token mb-4">
                            <div class="bar" style="width:82%;"></div>
                        </div>

                        <svg viewBox="0 0 460 100" class="w-100" style="height:6rem;color:var(--secondary);" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <defs>
                                <linearGradient id="featuresChartGrad" x1="0" x2="0" y1="0" y2="1">
                                    <stop offset="0%" stop-color="#00DFA2" stop-opacity="0.35"/>
                                    <stop offset="100%" stop-color="#0094FD" stop-opacity="0"/>
                                </linearGradient>
                            </defs>

                            <path d="M0,78 Q55,45 110,58 T220,32 T330,48 T460,14 L460,100 L0,100 Z" fill="url(#featuresChartGrad)"/>
                            <path d="M0,78 Q55,45 110,58 T220,32 T330,48 T460,14" stroke="currentColor" stroke-linecap="round" stroke-width="3"/>

                            <circle cx="220" cy="32" r="4" fill="#00DFA2" stroke="var(--surface-container-lowest)" stroke-width="2"/>
                            <circle cx="460" cy="14" r="4" fill="#0094FD" stroke="var(--surface-container-lowest)" stroke-width="2"/>
                        </svg>

                    </div>
                </div>

            </div>
        </div>
    </section>

    {{-- Security & Experience Section --}}
    <section class="trust-strip py-5 px-3 px-lg-4">
        <div class="container-xl">

            <div class="text-center mb-5">
                <div class="text-secondary fw-bold small text-uppercase mb-1">
                    <span class="d-inline-block rounded-circle bg-secondary" style="width:6px;height:6px;"></span>
                    تجربة متكاملة
                </div>

                <h2 class="fw-bold mb-2">مصممة لتكون بسيطة وآمنة</h2>

                <p class="text-on-surface-variant small mb-0">
                    كل التفاصيل التي تحتاجها لإدارة تجربتك التعليمية اليومية.
                </p>
            </div>

            <div class="row g-4">

                {{-- Data Protection --}}
                <div class="col-12 col-md-4">
                    <div class="d-flex align-items-start gap-3">
                        <div class="rounded-3 d-flex align-items-center justify-content-center flex-shrink-0" style="width:2.75rem;height:2.75rem;background:color-mix(in srgb,var(--primary-container) 20%,transparent);color:var(--on-primary-container);">
                            <span class="material-symbols-outlined">verified_user</span>
                        </div>

                        <div>
                            <h4 class="fw-bold mb-1 fs-6">حماية البيانات</h4>
                            <p class="text-on-surface-variant mb-0 small">
                                تصميم يهتم بحماية بيانات المستخدمين
                                والمعلومات التعليمية.
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Speed --}}
                <div class="col-12 col-md-4">
                    <div class="d-flex align-items-start gap-3">
                        <div class="rounded-3 d-flex align-items-center justify-content-center flex-shrink-0" style="width:2.75rem;height:2.75rem;background:color-mix(in srgb,var(--secondary-fixed) 50%,transparent);color:var(--secondary);">
                            <span class="material-symbols-outlined">bolt</span>
                        </div>

                        <div>
                            <h4 class="fw-bold mb-1 fs-6">سرعة وسهولة</h4>
                            <p class="text-on-surface-variant mb-0 small">
                                واجهة واضحة وسريعة تساعدك على إنجاز
                                المهام اليومية بأقل خطوات.
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Responsive --}}
                <div class="col-12 col-md-4">
                    <div class="d-flex align-items-start gap-3">
                        <div class="rounded-3 d-flex align-items-center justify-content-center flex-shrink-0" style="width:2.75rem;height:2.75rem;background:color-mix(in srgb,var(--tertiary-container) 30%,transparent);color:var(--on-tertiary-container);">
                            <span class="material-symbols-outlined">devices</span>
                        </div>

                        <div>
                            <h4 class="fw-bold mb-1 fs-6">على كل أجهزتك</h4>
                            <p class="text-on-surface-variant mb-0 small">
                                تجربة متجاوبة مع الهواتف والأجهزة اللوحية
                                وأجهزة الكمبيوتر.
                            </p>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>

@endsection
