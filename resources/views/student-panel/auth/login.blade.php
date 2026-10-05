@extends('student-panel.auth.master')
@section('title', 'دخول الطالب')
@section('main')
    <main class="student-login-shell">
        <section class="student-login-story" aria-labelledby="student-story-title">
            <span class="student-eyebrow"><i class="ti ti-school" aria-hidden="true"></i> مساحة الطالب</span>
            <h1 id="student-story-title">خطوتك القادمة<br><span>تبدأ من هنا.</span></h1>
            <p>مساحتك الخاصة في Classio. سجّل دخولك وابدأ رحلتك التعليمية بكل وضوح وثقة.</p>
            <div class="student-story-art" aria-hidden="true">
                <div class="student-art-orbit"></div>
                <div class="student-art-book"><i class="ti ti-book"></i></div>
                <span class="student-art-note"><i class="ti ti-stars"></i> كل يوم، خطوة للأمام</span>
                <span class="student-art-badge"><i class="ti ti-school"></i></span>
            </div>
            <div class="student-story-footer"><i class="ti ti-shield-check" aria-hidden="true"></i> حسابك، مساحتك، رحلتك.</div>
        </section>
        <section class="student-login-card" aria-labelledby="student-login-title">
            <img class="student-brand-logo" src="{{ asset('assets/studentPanel/images/logo.png') }}" alt="Classio">
            <span class="student-eyebrow text-primary">أهلاً بك في Classio</span>
            <h2 id="student-login-title">سعيدون بعودتك</h2>
            <p class="text-muted mb-4">سجّل الدخول إلى حساب الطالب الخاص بك.</p>
            @livewire('student-panel.auth.login-form')
            <p class="student-login-help"><i class="ti ti-help" aria-hidden="true"></i> تحتاج بيانات الدخول؟ تواصل مع المدرس أو إدارة المركز.</p>
        </section>
    </main>
@endsection
