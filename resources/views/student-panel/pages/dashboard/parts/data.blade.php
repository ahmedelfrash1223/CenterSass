<div>
    <section class="student-welcome mb-4">
        <div>
            <span class="student-eyebrow">أهلاً بعودتك</span>
            <h2>مرحباً، {{ $student->name }}</h2>
            <p>هذه مساحتك الخاصة. يمكنك مراجعة بيانات حسابك وتحديثها، وسنضيف أدواتك التعليمية هنا قريباً.</p>
            <a href="{{ route('student.profile') }}" wire:navigate class="btn btn-light">عرض الملف الشخصي <i class="ti ti-arrow-left ms-2" aria-hidden="true"></i></a>
        </div>
        <i class="ti ti-school student-welcome-icon" aria-hidden="true"></i>
    </section>
    <div class="row g-4">
        @foreach(['الاختبارات' => 'ti-pencil', 'الحضور' => 'ti-calendar', 'الجدول الدراسي' => 'ti-calendar', 'النتائج' => 'ti-chart-bar'] as $label => $icon)
            <div class="col-sm-6 col-xl-3">
                <section class="card student-module-card h-100 mb-0">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between mb-4">
                            <span class="student-module-icon"><i class="ti {{ $icon }}" aria-hidden="true"></i></span>
                            <span class="badge student-coming-soon">قريباً</span>
                        </div>
                        <h3 class="h5">{{ $label }}</h3>
                        <p class="text-muted small mb-0">ستتوفر هذه الخدمة في مرحلة لاحقة.</p>
                    </div>
                </section>
            </div>
        @endforeach
    </div>
    <div class="student-empty-state mt-4">
        <i class="ti ti-stars fs-2" aria-hidden="true"></i>
        <div><h3 class="h6 mb-1">مساحتك تنمو معك</h3><p class="text-muted mb-0 small">لا توجد خدمات تعليمية متاحة هنا بعد. تابعنا مع التحديثات القادمة.</p></div>
    </div>
</div>
