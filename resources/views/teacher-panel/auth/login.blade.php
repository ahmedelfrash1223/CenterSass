@extends('teacher-panel.auth.master')

@section('title', 'تسجيل الدخول')

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/teacherPanel/css/pages/teacher-login.css') }}">
@endpush

@push('scripts')
    <script src="{{ asset('assets/teacherPanel/js/pages/teacher-login.js') }}" defer></script>
@endpush

@section('main')
    <div class="nb-login-stage">
    <div class="nb-book-fit" data-nb-book-fit>
    <main class="nb-login">

        <!-- Open book shape (decorative): two curved pages + stacked page edges -->
        <img
            class="nb-login__book"
            src="{{ asset('assets/teacherPanel/images/login-book.svg') }}"
            alt=""
            aria-hidden="true"
            width="1280"
            height="860"
        >
        <span class="nb-login__spine" aria-hidden="true"></span>
        <span class="nb-login__ribbon" aria-hidden="true"></span>

        <!-- ================= Form column (start side) ================= -->
        <section class="nb-login__main" aria-labelledby="nb-login-title">
        <div class="nb-page__inner" data-nb-page-inner>

            <header class="nb-login__top">
                <div class="nb-logo">
                    <img
                        src="{{ asset('assets/teacherPanel/images/logo.png') }}"
                        alt="نبذة"
                        width="612"
                        height="408"
                    >
                </div>
            </header>

            <div class="nb-login__center">
                <div class="nb-login__form-block">

                    <p class="nb-eyebrow">
                        <span class="nb-eyebrow__dot" aria-hidden="true"></span>
                        لوحة المدرس
                    </p>

                    <h1 id="nb-login-title" class="nb-login__title">أهلاً بيك تاني</h1>
                    <p class="nb-login__subtitle">سجّل دخولك وتابع حضور طلابك ودرجاتهم ومصاريفهم من مكان واحد.</p>

                    @livewire('teacher-panel.auth.login-form')

                    <p class="nb-secure-note">
                        <svg viewBox="0 0 24 24" fill="none" aria-hidden="true" focusable="false">
                            <path d="M12 3.5 5 6.25v5.5c0 4.25 2.9 7.6 7 8.75 4.1-1.15 7-4.5 7-8.75v-5.5L12 3.5Z" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/>
                            <path d="m9 12 2.1 2.1L15.2 10" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        بياناتك محمية واتصالك مشفّر
                    </p>

                </div>
            </div>

            <footer class="nb-login__footer">
                <span>© نبذة {{ date('Y') }}</span>
                <span>
                    محتاج مساعدة؟
                    <a href="#" class="nb-login__support">كلّم الدعم</a>
                </span>
            </footer>

        </div>
        </section>

        <!-- ================= Brand panel (end side) ================= -->
        <aside class="nb-brand" aria-label="عن نبذة">
        <div class="nb-page__inner" data-nb-page-inner>

            <!-- Hub illustration (decorative) -->
            <div class="nb-hub" aria-hidden="true">

                <svg class="nb-hub__links" viewBox="0 0 460 340" fill="none" focusable="false">
                    <path d="M306 94 Q322 80 336 64"/>
                    <path d="M162 172 Q152 166 142 164"/>
                    <path d="M298 246 Q308 260 320 274"/>
                </svg>

                <div class="nb-hub__tile">
                    <svg class="nb-hub__mark" viewBox="0 0 64 64" fill="none" stroke-linecap="round" stroke-linejoin="round" focusable="false">
                        <path class="nb-mark__book" d="M32 18c-6-5-15-6-24-4v34c9-2 18-1 24 4 6-5 15-6 24-4V14c-9-2-18-1-24 4z"/>
                        <path class="nb-mark__book" d="M32 18v34"/>
                        <path class="nb-mark__lines" d="M14 26c4-1 8-1 12 1M14 33c4-1 8-1 12 1"/>
                        <path class="nb-mark__check" d="M37 32l5 5 9-11"/>
                    </svg>
                </div>

                <div class="nb-node nb-node--attendance">
                    <span class="nb-node__icon">
                        <svg viewBox="0 0 24 24" fill="none" focusable="false">
                            <path d="M5 12.5 10 17.5 19 7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </span>
                    <span class="nb-node__text">
                        <span class="nb-node__title">الحضور والغياب</span>
                        <span class="nb-node__meta">٣٦ من ٤٠ حاضر</span>
                    </span>
                </div>

                <div class="nb-node nb-node--fees">
                    <span class="nb-node__icon">
                        <svg viewBox="0 0 24 24" fill="none" focusable="false">
                            <rect x="3.5" y="6.5" width="17" height="11" rx="2.5" stroke="currentColor" stroke-width="1.8"/>
                            <circle cx="12" cy="12" r="2.4" stroke="currentColor" stroke-width="1.8"/>
                        </svg>
                    </span>
                    <span class="nb-node__text">
                        <span class="nb-node__title">المصاريف</span>
                        <span class="nb-node__meta">اتحصّل ٨٥٪</span>
                    </span>
                </div>

                <div class="nb-node nb-node--exams">
                    <span class="nb-node__icon">
                        <svg viewBox="0 0 24 24" fill="none" focusable="false">
                            <path d="M7 3.75h6.5L18 8.25v12H7z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
                            <path d="M13.25 3.75v4.5H18M9.75 12.5h5.5M9.75 16h5.5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </span>
                    <span class="nb-node__text">
                        <span class="nb-node__title">الامتحانات</span>
                        <span class="nb-node__meta">المتوسط ١٧ / ٢٠</span>
                    </span>
                </div>
            </div>

            <!-- Text slider -->
            <div class="nb-slider" data-nb-slider>
                <div class="nb-slider__track">
                    <div class="nb-slide is-active" data-nb-slide>
                        <h2 class="nb-slide__title">كل حاجة عن الطالب.. بلمحة</h2>
                        <p class="nb-slide__text">حضور، مصاريف، وامتحانات في مكان واحد، وولي الأمر بيوصله ملخص أول بأول.</p>
                    </div>
                    <div class="nb-slide" data-nb-slide aria-hidden="true">
                        <h2 class="nb-slide__title">الحضور في ثواني</h2>
                        <p class="nb-slide__text">سجّل حضور المجموعة كلها بسرعة، والغياب يتبلّغ لولي الأمر لوحده.</p>
                    </div>
                    <div class="nb-slide" data-nb-slide aria-hidden="true">
                        <h2 class="nb-slide__title">مصاريفك متظبطة</h2>
                        <p class="nb-slide__text">اعرف مين دفع ومين لسه، من غير كشكول ولا حسابات.</p>
                    </div>
                </div>

                <div class="nb-dots">
                    <button type="button" class="nb-dots__btn is-active" data-nb-dot="0" aria-label="الرسالة ١" aria-current="true"></button>
                    <button type="button" class="nb-dots__btn" data-nb-dot="1" aria-label="الرسالة ٢"></button>
                    <button type="button" class="nb-dots__btn" data-nb-dot="2" aria-label="الرسالة ٣"></button>
                </div>
            </div>

        </div>
        </aside>

    </main>
    </div>
    </div>
@endsection
