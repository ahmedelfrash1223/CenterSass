@section('title', 'الخطط والأسعار')
@section('pricing-active', 'active')
@extends('landing.master')

@section('main')

    {{-- Hero Section --}}
    <section class="container-xl px-3 px-lg-4 pt-5 pb-5 position-relative overflow-hidden">
        <div class="hero-glow-a"></div>
        <div class="row justify-content-center text-center position-relative" style="z-index:1;">
            <div class="col-12 col-lg-9">
                <div class="hero-pill mb-4">
                    <span class="pulse-dot"></span>
                    <span class="small fw-bold">خطط مرنة تناسب طريقة عملك</span>
                </div>
                <h1 class="hero-title display-4 fw-bold mb-4">
                    اختر الخطة التي <span class="accent">تناسب احتياجاتك</span>
                </h1>
                <p class="text-on-surface-variant fs-6 fs-lg-5 mx-auto mb-0" style="max-width:760px;">
                    ابدأ بإدارة دروسك بشكل أكثر تنظيمًا، واختر الخطة التي تناسب عدد
                    مجموعاتك وطلابك واحتياجاتك التعليمية.
                </p>
            </div>
        </div>
    </section>

    {{-- Pricing Plans --}}
    <section class="container-xl px-3 px-lg-4 pb-5">
        <div class="text-center mb-5">
            <div class="text-secondary fw-bold small text-uppercase mb-1">
                <span class="d-inline-block rounded-circle bg-secondary" style="width:6px;height:6px;"></span>
                الخطط والأسعار
            </div>
            <h2 class="fw-bold mb-2">ابدأ بالطريقة التي تناسبك</h2>
            <p class="text-on-surface-variant small mt-1 mb-0">
                جميع الخطط مصممة لتساعدك على تنظيم عملك ومتابعة طلابك بسهولة.
            </p>
        </div>

        <div class="row g-4 justify-content-center">

            {{-- Starter Plan --}}
            <div class="col-12 col-md-6 col-lg-4">
                <div class="card-token p-4 p-lg-5 h-100 d-flex flex-column">
                    <div class="icon-capsule mb-4"><span class="material-symbols-outlined fs-3">rocket_launch</span></div>
                    <h3 class="fw-bold mb-2">البداية</h3>
                    <p class="text-on-surface-variant small mb-4">مناسبة للمعلمين الذين يريدون البدء في تنظيم دروسهم وطلابهم.</p>
                    <div class="mb-4">
                        <span class="price-value display-5">99</span>
                        <span class="text-on-surface-variant">جنيه / شهر</span>
                    </div>
                    <div class="d-flex flex-column gap-3 mb-4">
                        {{-- Feature --}}
                        <div class="d-flex align-items-center gap-2"><span class="material-symbols-outlined plan-check">check_circle</span><span class="small">حتى 3 مجموعات</span></div>
                        <div class="d-flex align-items-center gap-2"><span class="material-symbols-outlined plan-check">check_circle</span><span class="small">حتى 50 طالب</span></div>
                        <div class="d-flex align-items-center gap-2"><span class="material-symbols-outlined plan-check">check_circle</span><span class="small">إدارة الحضور والغياب</span></div>
                        <div class="d-flex align-items-center gap-2"><span class="material-symbols-outlined plan-check">check_circle</span><span class="small">إدارة الواجبات</span></div>
                        <div class="d-flex align-items-center gap-2"><span class="material-symbols-outlined plan-check">check_circle</span><span class="small">التقييمات والدرجات</span></div>
                    </div>
                    <div class="mt-auto">
                        <a href="{{ route('teacher.dashboard') }}" wire:navigate class="btn-token">
                            <span>ابدأ الآن</span>
                            <span class="material-symbols-outlined">arrow_back</span>
                        </a>
                    </div>
                </div>
            </div>

            {{-- Professional Plan --}}
            <div class="col-12 col-md-6 col-lg-4">
                <div class="card-token featured p-4 p-lg-5 h-100 d-flex flex-column position-relative">
                    <span class="featured-badge">الأكثر استخدامًا</span>
                    <div class="icon-capsule primary-icon mb-4"><span class="material-symbols-outlined fs-3">workspace_premium</span></div>
                    <h3 class="fw-bold mb-2">المعلم المحترف</h3>
                    <p class="text-on-surface-variant small mb-4">للمعلمين الذين يديرون مجموعات متعددة ويحتاجون إلى متابعة أكثر شمولًا.</p>
                    <div class="mb-4">
                        <span class="price-value display-5">199</span>
                        <span class="text-on-surface-variant">جنيه / شهر</span>
                    </div>
                    <div class="d-flex flex-column gap-3 mb-4">
                        {{-- Features --}}
                        <div class="d-flex align-items-center gap-2"><span class="material-symbols-outlined plan-check">check_circle</span><span class="small">حتى 10 مجموعات</span></div>
                        <div class="d-flex align-items-center gap-2"><span class="material-symbols-outlined plan-check">check_circle</span><span class="small">حتى 200 طالب</span></div>
                        <div class="d-flex align-items-center gap-2"><span class="material-symbols-outlined plan-check">check_circle</span><span class="small">إدارة الحضور والغياب</span></div>
                        <div class="d-flex align-items-center gap-2"><span class="material-symbols-outlined plan-check">check_circle</span><span class="small">الواجبات والتكليفات</span></div>
                        <div class="d-flex align-items-center gap-2"><span class="material-symbols-outlined plan-check">check_circle</span><span class="small">التقييمات والدرجات</span></div>
                        <div class="d-flex align-items-center gap-2"><span class="material-symbols-outlined plan-check">check_circle</span><span class="small">الاختبارات والنتائج</span></div>
                        <div class="d-flex align-items-center gap-2"><span class="material-symbols-outlined plan-check">check_circle</span><span class="small">التقارير والإحصائيات</span></div>
                    </div>
                    <div class="mt-auto">
                        <a href="{{ route('teacher.dashboard') }}" wire:navigate class="btn-token solid">
                            <span>ابدأ الآن</span>
                            <span class="material-symbols-outlined">arrow_back</span>
                        </a>
                    </div>
                </div>
            </div>

            {{-- Academy Plan --}}
            <div class="col-12 col-md-6 col-lg-4">
                <div class="card-token p-4 p-lg-5 h-100 d-flex flex-column">
                    <div class="icon-capsule mb-4"><span class="material-symbols-outlined fs-3">domain</span></div>
                    <h3 class="fw-bold mb-2">الأكاديمية</h3>
                    <p class="text-on-surface-variant small mb-4">للمراكز والأكاديميات التي تحتاج إلى إدارة أكبر وعدد أكبر من المستخدمين.</p>
                    <div class="mb-4">
                        <span class="price-value display-5">499</span>
                        <span class="text-on-surface-variant">جنيه / شهر</span>
                    </div>
                    <div class="d-flex flex-column gap-3 mb-4">
                        {{-- Features --}}
                        <div class="d-flex align-items-center gap-2"><span class="material-symbols-outlined plan-check">check_circle</span><span class="small">مجموعات متعددة</span></div>
                        <div class="d-flex align-items-center gap-2"><span class="material-symbols-outlined plan-check">check_circle</span><span class="small">عدد كبير من الطلاب</span></div>
                        <div class="d-flex align-items-center gap-2"><span class="material-symbols-outlined plan-check">check_circle</span><span class="small">إدارة المعلمين</span></div>
                        <div class="d-flex align-items-center gap-2"><span class="material-symbols-outlined plan-check">check_circle</span><span class="small">الحضور والواجبات والتقييمات</span></div>
                        <div class="d-flex align-items-center gap-2"><span class="material-symbols-outlined plan-check">check_circle</span><span class="small">الاختبارات والنتائج</span></div>
                        <div class="d-flex align-items-center gap-2"><span class="material-symbols-outlined plan-check">check_circle</span><span class="small">تقارير وإحصائيات متقدمة</span></div>
                    </div>
                    <div class="mt-auto">
                        <a href="#" class="btn-token">
                            <span>تواصل معنا</span>
                            <span class="material-symbols-outlined">arrow_back</span>
                        </a>
                    </div>
                </div>
            </div>

        </div>
    </section>

    {{-- Included Features --}}
    <section class="container-xl px-3 px-lg-4 pb-5">
        <div class="panel-token p-4 p-lg-5">
            <div class="row g-4 align-items-center">

                {{-- Content --}}
                <div class="col-12 col-lg-5">
                    <div class="feature-tag mb-3" style="background:color-mix(in srgb,var(--primary-fixed) 30%,transparent);color:var(--on-primary-fixed-variant);">
                        <span class="material-symbols-outlined" style="font-size:16px;">verified</span>
                        في كل الخطط
                    </div>
                    <h2 class="fw-bold mb-3">الأدوات الأساسية موجودة دائمًا</h2>
                    <p class="text-on-surface-variant mb-0">
                        مهما كانت الخطة التي تختارها، تحصل على الأدوات الأساسية التي
                        تحتاجها لتنظيم العملية التعليمية ومتابعة طلابك.
                    </p>
                </div>

                {{-- Features --}}
                <div class="col-12 col-lg-7">
                    <div class="row g-3">

                        {{-- Students --}}
                        <div class="col-12 col-md-6">
                            <div class="stat-box p-3 d-flex align-items-center gap-3">
                                <div class="icon-capsule" style="width:2.75rem;height:2.75rem;"><span class="material-symbols-outlined">groups</span></div>
                                <div>
                                    <h4 class="fw-bold mb-1 fs-6">إدارة الطلاب</h4>
                                    <p class="text-on-surface-variant mb-0 small">بيانات الطلاب ومتابعتهم.</p>
                                </div>
                            </div>
                        </div>

                        {{-- Attendance --}}
                        <div class="col-12 col-md-6">
                            <div class="stat-box p-3 d-flex align-items-center gap-3">
                                <div class="icon-capsule" style="width:2.75rem;height:2.75rem;"><span class="material-symbols-outlined">fact_check</span></div>
                                <div>
                                    <h4 class="fw-bold mb-1 fs-6">الحضور</h4>
                                    <p class="text-on-surface-variant mb-0 small">سجل واضح للحضور والغياب.</p>
                                </div>
                            </div>
                        </div>

                        {{-- Assignments --}}
                        <div class="col-12 col-md-6">
                            <div class="stat-box p-3 d-flex align-items-center gap-3">
                                <div class="icon-capsule" style="width:2.75rem;height:2.75rem;"><span class="material-symbols-outlined">assignment</span></div>
                                <div>
                                    <h4 class="fw-bold mb-1 fs-6">الواجبات</h4>
                                    <p class="text-on-surface-variant mb-0 small">إنشاء ومتابعة التكليفات.</p>
                                </div>
                            </div>
                        </div>

                        {{-- Reports --}}
                        <div class="col-12 col-md-6">
                            <div class="stat-box p-3 d-flex align-items-center gap-3">
                                <div class="icon-capsule" style="width:2.75rem;height:2.75rem;"><span class="material-symbols-outlined">analytics</span></div>
                                <div>
                                    <h4 class="fw-bold mb-1 fs-6">التقارير</h4>
                                    <p class="text-on-surface-variant mb-0 small">رؤية أوضح للأداء والنتائج.</p>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

            </div>
        </div>
    </section>

    {{-- FAQ Section --}}
    <section class="container-xl px-3 px-lg-4 pb-5">
        <div class="text-center mb-5">
            <div class="text-secondary fw-bold small text-uppercase mb-1">
                <span class="d-inline-block rounded-circle bg-secondary" style="width:6px;height:6px;"></span>
                الأسئلة الشائعة
            </div>
            <h2 class="fw-bold mb-2">هل لديك سؤال؟</h2>
            <p class="text-on-surface-variant small mb-0">بعض الإجابات السريعة حول الخطط والأسعار.</p>
        </div>

        <div class="row g-3 justify-content-center">

            {{-- FAQ: Change Plan --}}
            <div class="col-12 col-lg-8">
                <div class="card-token p-4">
                    <div class="d-flex align-items-start gap-3">
                        <div class="icon-capsule flex-shrink-0" style="width:2.75rem;height:2.75rem;"><span class="material-symbols-outlined">help</span></div>
                        <div>
                            <h3 class="fw-bold fs-6 mb-2">هل يمكنني تغيير الخطة لاحقًا؟</h3>
                            <p class="text-on-surface-variant small mb-0">نعم، يمكنك تغيير خطتك وفقًا لاحتياجاتك وعدد الطلاب والمجموعات التي تديرها.</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- FAQ: Billing --}}
            <div class="col-12 col-lg-8">
                <div class="card-token p-4">
                    <div class="d-flex align-items-start gap-3">
                        <div class="icon-capsule flex-shrink-0" style="width:2.75rem;height:2.75rem;"><span class="material-symbols-outlined">payments</span></div>
                        <div>
                            <h3 class="fw-bold fs-6 mb-2">هل الأسعار شهرية؟</h3>
                            <p class="text-on-surface-variant small mb-0">الأسعار الموضحة هي أسعار شهرية، ويمكن أن تختلف تفاصيل الاشتراك حسب الخطة.</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- FAQ: Support --}}
            <div class="col-12 col-lg-8">
                <div class="card-token p-4">
                    <div class="d-flex align-items-start gap-3">
                        <div class="icon-capsule flex-shrink-0" style="width:2.75rem;height:2.75rem;"><span class="material-symbols-outlined">support_agent</span></div>
                        <div>
                            <h3 class="fw-bold fs-6 mb-2">هل يمكنني الحصول على مساعدة؟</h3>
                            <p class="text-on-surface-variant small mb-0">يمكنك التواصل معنا للحصول على المساعدة ومعرفة الخطة المناسبة لاحتياجاتك.</p>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </section>

    {{-- Trust Section --}}
    <section class="trust-strip py-5 px-3 px-lg-4">
        <div class="container-xl">
            <div class="row g-4 text-center">

                {{-- Flexible Plans --}}
                <div class="col-12 col-md-4">
                    <div class="icon-capsule primary-icon mx-auto mb-3"><span class="material-symbols-outlined">tune</span></div>
                    <h4 class="fw-bold fs-6 mb-2">خطط مرنة</h4>
                    <p class="text-on-surface-variant small mb-0">اختر الخطة التي تناسب حجم عملك واحتياجاتك.</p>
                </div>

                {{-- Simple Experience --}}
                <div class="col-12 col-md-4">
                    <div class="icon-capsule mx-auto mb-3"><span class="material-symbols-outlined">bolt</span></div>
                    <h4 class="fw-bold fs-6 mb-2">بدون تعقيد</h4>
                    <p class="text-on-surface-variant small mb-0">كل ما تحتاجه لإدارة دروسك في مكان واحد.</p>
                </div>

                {{-- Support --}}
                <div class="col-12 col-md-4">
                    <div class="icon-capsule mx-auto mb-3"><span class="material-symbols-outlined">support_agent</span></div>
                    <h4 class="fw-bold fs-6 mb-2">دعم مستمر</h4>
                    <p class="text-on-surface-variant small mb-0">نساعدك على الاستفادة من كلاسيو بأفضل شكل.</p>
                </div>

            </div>
        </div>
    </section>

    {{-- CTA Section --}}
    <section class="container-xl px-3 px-lg-4 py-5">
        <div class="panel-token p-4 p-lg-5 text-center position-relative overflow-hidden">
            <div class="hero-glow-a" style="top:auto;bottom:-14rem;opacity:.55;"></div>
            <div class="position-relative" style="z-index:1;">
                <div class="icon-capsule primary-icon mx-auto mb-4"><span class="material-symbols-outlined fs-3">rocket_launch</span></div>
                <h2 class="fw-bold mb-3">جاهز تبدأ مع كلاسيو؟</h2>
                <p class="text-on-surface-variant mx-auto mb-4" style="max-width:620px;">
                    اختر خطتك وابدأ في تنظيم طلابك ومجموعاتك وحضورك وواجباتك
                    من خلال منصة واحدة مصممة للمعلمين.
                </p>
            </div>
        </div>
    </section>

@endsection
