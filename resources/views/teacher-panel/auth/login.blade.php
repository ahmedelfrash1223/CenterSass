@extends('teacher-panel.auth.master')

@section('title', 'تسجيل الدخول')

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/teacherPanel/css/pages/teacher-login.css') }}">
@endpush

@push('scripts')
    <script src="{{ asset('assets/teacherPanel/js/pages/teacher-login.js') }}" defer></script>
@endpush

@section('main')
    <main class="nb-login">

        <!-- ================= Form side ================= -->
        <section class="nb-login__form-side" aria-labelledby="nb-login-title">
            <div class="nb-login__form-wrap">

                <div class="nb-login__logo nb-reveal">
                    <img
                        src="{{ asset('assets/teacherPanel/images/logo.png') }}"
                        alt="نبذة"
                        width="612"
                        height="408"
                    >
                </div>

                <header class="nb-login__header nb-reveal nb-reveal--2">
                    <h1 id="nb-login-title" class="nb-login__title">أهلاً بيك تاني</h1>
                    <p class="nb-login__subtitle">سجّل دخولك لمتابعة طلابك وحضورهم ومصاريفهم</p>
                </header>

                <div class="nb-reveal nb-reveal--3">
                    @livewire('teacher-panel.auth.login-form')
                </div>

                <footer class="nb-login__footer nb-reveal nb-reveal--4">
                    <span>© نبذة {{ date('Y') }}</span>
                </footer>

            </div>
        </section>

        <!-- ================= Brand side ================= -->
        <aside class="nb-login__brand" aria-label="نبذة بلمحة">

            <!-- Decorative logo motifs -->
            <svg class="nb-motif nb-motif--lines" viewBox="0 0 64 40" fill="none" aria-hidden="true" focusable="false">
                <path d="M8 13 C 22 9, 42 9, 56 13" stroke="currentColor" stroke-width="6" stroke-linecap="round"/>
                <path d="M8 28 C 22 24, 42 24, 56 28" stroke="currentColor" stroke-width="6" stroke-linecap="round"/>
            </svg>
            <svg class="nb-motif nb-motif--check" viewBox="0 0 48 48" fill="none" aria-hidden="true" focusable="false">
                <path d="M10 25 L20 35 L38 13" stroke="currentColor" stroke-width="6" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>

            <div class="nb-brand">
                <div class="nb-brand__intro nb-reveal">
                    <h2 class="nb-brand__title">كل حاجة عن الطالب.. بلمحة</h2>
                    <p class="nb-brand__text">الحضور والغياب والمصاريف والامتحانات في مكان واحد.</p>
                </div>

                <!-- Smart preview collage (decorative) -->
                <div class="nb-collage" aria-hidden="true">

                    <!-- 1. Attendance card -->
                    <div class="nb-card nb-card--attendance nb-reveal nb-reveal--2">
                        <div class="nb-card__head">
                            <span class="nb-card__icon">
                                <svg viewBox="0 0 24 24" fill="none" focusable="false">
                                    <path d="M5 12.5 L10 17.5 L19 7" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </span>
                            <div>
                                <p class="nb-card__title">حضور اليوم</p>
                                <p class="nb-card__meta">مجموعة السبت · ٤ طلاب</p>
                            </div>
                        </div>

                        <ul class="nb-roster">
                            <li class="nb-roster__row">
                                <span class="nb-avatar">١</span>
                                <span class="nb-roster__name">طالب ١</span>
                                <span class="nb-pill nb-pill--present">
                                    <svg viewBox="0 0 24 24" fill="none" focusable="false"><path d="M5 12.5 L10 17.5 L19 7" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                    حاضر
                                </span>
                            </li>
                            <li class="nb-roster__row">
                                <span class="nb-avatar">٢</span>
                                <span class="nb-roster__name">طالب ٢</span>
                                <span class="nb-pill nb-pill--present">
                                    <svg viewBox="0 0 24 24" fill="none" focusable="false"><path d="M5 12.5 L10 17.5 L19 7" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                    حاضر
                                </span>
                            </li>
                            <li class="nb-roster__row">
                                <span class="nb-avatar">٣</span>
                                <span class="nb-roster__name">طالب ٣</span>
                                <span class="nb-pill nb-pill--absent">غائب</span>
                            </li>
                            <li class="nb-roster__row">
                                <span class="nb-avatar">٤</span>
                                <span class="nb-roster__name">طالب ٤</span>
                                <span class="nb-pill nb-pill--present">
                                    <svg viewBox="0 0 24 24" fill="none" focusable="false"><path d="M5 12.5 L10 17.5 L19 7" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                    حاضر
                                </span>
                            </li>
                        </ul>
                    </div>

                    <!-- 2. Weekly attendance dot grid -->
                    <div class="nb-card nb-card--week nb-reveal nb-reveal--3">
                        <p class="nb-card__title">حضور الأسبوع</p>
                        <div class="nb-week">
                            <span class="nb-week__day">س</span>
                            <span class="nb-week__day">ح</span>
                            <span class="nb-week__day">ن</span>
                            <span class="nb-week__day">ث</span>
                            <span class="nb-week__day">ر</span>
                            <span class="nb-week__day">خ</span>

                            <span class="nb-dot nb-dot--on"></span>
                            <span class="nb-dot nb-dot--on"></span>
                            <span class="nb-dot nb-dot--on"></span>
                            <span class="nb-dot"></span>
                            <span class="nb-dot nb-dot--on"></span>
                            <span class="nb-dot nb-dot--on"></span>

                            <span class="nb-dot nb-dot--on"></span>
                            <span class="nb-dot"></span>
                            <span class="nb-dot nb-dot--on"></span>
                            <span class="nb-dot nb-dot--on"></span>
                            <span class="nb-dot nb-dot--on"></span>
                            <span class="nb-dot nb-dot--on"></span>

                            <span class="nb-dot nb-dot--on"></span>
                            <span class="nb-dot nb-dot--on"></span>
                            <span class="nb-dot nb-dot--on"></span>
                            <span class="nb-dot nb-dot--on"></span>
                            <span class="nb-dot"></span>
                            <span class="nb-dot nb-dot--on"></span>
                        </div>
                    </div>

                    <!-- 3. Fees card -->
                    <div class="nb-card nb-card--fees nb-reveal nb-reveal--4">
                        <div class="nb-card__head">
                            <span class="nb-card__icon">
                                <svg viewBox="0 0 24 24" fill="none" focusable="false">
                                    <rect x="3" y="6" width="18" height="12" rx="2.5" stroke="currentColor" stroke-width="2"/>
                                    <circle cx="12" cy="12" r="2.5" stroke="currentColor" stroke-width="2"/>
                                </svg>
                            </span>
                            <p class="nb-card__title">تم تحصيل ٨٥٪ من المصاريف</p>
                        </div>
                        <div class="nb-progress">
                            <span class="nb-progress__bar nb-progress__bar--85"></span>
                        </div>
                        <p class="nb-card__meta">٦ طلاب لسه مدفعوش الشهر ده</p>
                    </div>

                    <!-- 4. Exam chip -->
                    <div class="nb-chip nb-reveal nb-reveal--5">
                        <span class="nb-chip__icon">
                            <svg viewBox="0 0 24 24" fill="none" focusable="false">
                                <path d="M7 4h7l4 4v12H7z" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/>
                                <path d="M10 13l2 2 3-4" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </span>
                        <span>متوسط الامتحان <strong>١٧/٢٠</strong></span>
                    </div>

                </div>
            </div>
        </aside>

    </main>
@endsection
