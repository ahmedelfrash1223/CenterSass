@extends('landing.master')

@section('title', 'بوابة الطالب ')

@section('main')

    {{-- Student Portal Hero --}}
    <section class="container-xl px-3 px-lg-4 pt-5 pb-5 position-relative overflow-hidden">
        <div class="hero-glow-a"></div>
        <div class="row align-items-center g-5 position-relative" style="z-index:1;">
            <div class="col-12 col-lg-7">
                <div class="hero-pill mb-4">
                    <span class="pulse-dot"></span>
                    <span class="small fw-bold">مرحبًا بك في بوابة الطالب</span>
                </div>
                <h1 class="hero-title display-4 fw-bold mb-4">
                    كل ما تحتاجه
                    <span class="accent">في مكان واحد</span>
                </h1>
                <p class="text-on-surface-variant fs-6 fs-lg-5 mb-4" style="max-width:680px;">
                    تابع مجموعاتك، حضورك، واجباتك، تقييماتك ونتائجك بسهولة من خلال
                    بوابة الطالب في كلاسيو.
                </p>
                <div class="d-flex flex-wrap gap-3">
                    <a href="#" wire:navigate class="btn-token solid" style="width:auto;min-width:210px;">
                        <span>الدخول إلى بوابة الطالب</span>
                        <span class="material-symbols-outlined">arrow_back</span>
                    </a>
                </div>
            </div>
            <div class="col-12 col-lg-5">
                <div class="panel-token p-4 p-lg-5 position-relative overflow-hidden">
                    <div class="hero-glow-a" style="top:-12rem;right:-10rem;transform:none;width:420px;height:420px;opacity:.5;"></div>
                    <div class="position-relative text-center" style="z-index:1;">
                        <div class="icon-capsule primary-icon mx-auto mb-4" style="width:4.5rem;height:4.5rem;">
                            <span class="material-symbols-outlined" style="font-size:38px;">school</span>
                        </div>
                        <h2 class="fw-bold mb-2">بوابة الطالب</h2>
                        <p class="text-on-surface-variant small mb-4">
                            ادخل إلى حسابك وتابع كل تفاصيل رحلتك التعليمية.
                        </p>
                        <div class="row g-3">
                            <div class="col-4">
                                <div class="stat-box p-3 h-100">
                                    <span class="material-symbols-outlined text-primary mb-2">groups</span>
                                    <div class="fw-bold small">المجموعات</div>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="stat-box p-3 h-100">
                                    <span class="material-symbols-outlined text-primary mb-2">fact_check</span>
                                    <div class="fw-bold small">الحضور</div>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="stat-box p-3 h-100">
                                    <span class="material-symbols-outlined text-primary mb-2">assignment</span>
                                    <div class="fw-bold small">الواجبات</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- App Download Section --}}
    <section class="container-xl px-3 px-lg-4 py-5">
        <div class="panel-token p-4 p-lg-5 position-relative overflow-hidden">
            <div class="hero-glow-a" style="top:auto;bottom:-18rem;right:auto;left:-10rem;transform:none;opacity:.45;"></div>
            <div class="row align-items-center g-5 position-relative" style="z-index:1;">

                {{-- Download Content --}}
                <div class="col-12 col-lg-6">
                    <div class="feature-tag mb-3" style="background:color-mix(in srgb,var(--primary-fixed) 30%,transparent);color:var(--on-primary-fixed-variant);">
                        <span class="material-symbols-outlined" style="font-size:16px;">smartphone</span>
                        تطبيق Classio
                    </div>
                    <h2 class="fw-bold mb-3">خليك متابع من موبايلك</h2>
                    <p class="text-on-surface-variant mb-4">
                        حمّل تطبيق كلاسيو على هاتفك للوصول إلى حسابك ومتابعة
                        مجموعاتك وواجباتك وحضورك ونتائجك في أي وقت.
                    </p>

                    {{-- Store Buttons --}}
                    <div class="d-flex flex-wrap gap-3">

                        {{-- App Store --}}
                        <a href="#" class="classio-store-btn" aria-label="Download on the App Store">
                            <svg class="classio-store-icon" viewBox="0 0 24 24" aria-hidden="true">
                                <path fill="currentColor" d="M17.05 20.28c-.98.95-2.05.8-3.08.35-1.09-.46-2.09-.48-3.24 0-1.44.62-2.2.44-3.06-.35C2.79 15.25 3.51 7.59 9.05 7.31c1.35.07 2.29.74 3.08.81 1.18-.24 2.31-.93 3.57-.84 1.51.12 2.65.72 3.39 1.8-3.12 1.87-2.38 5.98.48 7.2-.57 1.5-1.31 2.99-2.52 4.01zM12.03 7.25C11.88 5.02 13.69 3.18 15.77 3c.29 2.58-2.31 4.5-3.74 4.25z"/>
                            </svg>
                            <span class="classio-store-copy">
                            <span class="classio-store-small">Download on the</span>
                            <span class="classio-store-name">App Store</span>
                        </span>
                        </a>

                        {{-- Google Play --}}
                        <a href="#" class="classio-store-btn" aria-label="Get it on Google Play">
                            <svg class="classio-google-play-icon" viewBox="0 0 48 48" aria-hidden="true">
                                <path fill="#00DFA2" d="M6.7 4.7c-.43.55-.7 1.33-.7 2.28v33.99c0 .95.27 1.73.7 2.28L26.2 24 6.7 4.7z"/>
                                <path fill="#FFCC00" d="M32.7 30.43 26.2 24 6.7 43.25c.69.72 1.82.81 3.16.06l22.84-12.88z"/>
                                <path fill="#FF3D00" d="m32.7 17.57-22.84-12.88c-1.34-.75-2.47-.66-3.16.06L26.2 24l6.5-6.43z"/>
                                <path fill="#00B0FF" d="M39.83 21.58 32.7 17.57 26.2 24l6.5 6.43 7.13-4.01c2.05-1.16 2.05-3.68 0-4.84z"/>
                            </svg>
                            <span class="classio-store-copy">
                            <span class="classio-store-small">GET IT ON</span>
                            <span class="classio-store-name">Google Play</span>
                        </span>
                        </a>

                    </div>
                </div>

                {{-- QR Code --}}
                <div class="col-12 col-lg-6">
                    <div class="card-token p-4 p-lg-5">
                        <div class="row align-items-center g-4">

                            {{-- Temporary QR --}}
                            <div class="col-12 col-sm-auto text-center">
                                <div class="classio-qr-box">
                                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=https%3A%2F%2Fclassio.app" alt="QR Code لتحميل تطبيق Classio">
                                </div>
                            </div>

                            {{-- QR Text --}}
                            <div class="col text-center text-sm-end">
                                <div class="icon-capsule mx-auto mx-sm-0 mb-3" style="width:3rem;height:3rem;">
                                    <span class="material-symbols-outlined">qr_code_scanner</span>
                                </div>
                                <h3 class="fw-bold fs-5 mb-2">سكان لتحميل التطبيق</h3>
                                <p class="text-on-surface-variant small mb-0">
                                    افتح كاميرا الموبايل ووجّهها إلى رمز QR
                                    لتحميل تطبيق Classio مباشرة.
                                </p>
                            </div>

                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>

    {{-- Student Features --}}
    <section class="container-xl px-3 px-lg-4 py-5">
        <div class="text-center mb-5">
            <div class="text-secondary fw-bold small text-uppercase mb-1">
                <span class="d-inline-block rounded-circle bg-secondary" style="width:6px;height:6px;"></span>
                بوابة الطالب
            </div>
            <h2 class="fw-bold mb-2">تابع كل تفاصيل دراستك بسهولة</h2>
            <p class="text-on-surface-variant small mb-0">
                الأدوات التي تحتاجها لمتابعة دراستك في مكان واحد.
            </p>
        </div>

        <div class="row g-4">
            <div class="col-12 col-md-6 col-lg-3">
                <div class="card-token p-4 h-100">
                    <div class="icon-capsule primary-icon mb-4"><span class="material-symbols-outlined">groups</span></div>
                    <h3 class="fw-bold fs-5 mb-2">مجموعاتك</h3>
                    <p class="text-on-surface-variant small mb-0">تعرف على المجموعات والدروس المسجل بها ومواعيدها.</p>
                </div>
            </div>
            <div class="col-12 col-md-6 col-lg-3">
                <div class="card-token p-4 h-100">
                    <div class="icon-capsule mb-4"><span class="material-symbols-outlined">fact_check</span></div>
                    <h3 class="fw-bold fs-5 mb-2">الحضور</h3>
                    <p class="text-on-surface-variant small mb-0">تابع سجل حضورك وغيابك في الدروس والمجموعات.</p>
                </div>
            </div>
            <div class="col-12 col-md-6 col-lg-3">
                <div class="card-token p-4 h-100">
                    <div class="icon-capsule mb-4"><span class="material-symbols-outlined">assignment</span></div>
                    <h3 class="fw-bold fs-5 mb-2">الواجبات</h3>
                    <p class="text-on-surface-variant small mb-0">تعرف على الواجبات والتكليفات المطلوبة منك ومتابعة حالتها.</p>
                </div>
            </div>
            <div class="col-12 col-md-6 col-lg-3">
                <div class="card-token p-4 h-100">
                    <div class="icon-capsule mb-4"><span class="material-symbols-outlined">analytics</span></div>
                    <h3 class="fw-bold fs-5 mb-2">النتائج</h3>
                    <p class="text-on-surface-variant small mb-0">تابع تقييماتك ودرجاتك ونتائج الاختبارات بسهولة.</p>
                </div>
            </div>
        </div>
    </section>

@endsection
