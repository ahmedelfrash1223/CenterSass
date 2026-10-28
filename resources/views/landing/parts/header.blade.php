{{-- Header --}}
<header class="site-header">
    <div class="container-xl d-flex align-items-center justify-content-between" style="height:5rem;">
        {{-- Brand --}}
        <div class="d-flex align-items-center gap-2">
            <div class="d-flex align-items-center justify-content-center" style="width:2rem;height:2rem;">
                <img src="{{ asset('assets/landing/images/favicon.png') }}" alt="نبذة"
                     style="width:2rem;height:2rem;object-fit:contain;">
            </div>
            <span class="fw-bold fs-5 text-on-surface">نبذة</span>
            <span class="brand-badge d-none d-sm-inline-flex">النسخة 2.0</span>
        </div>

        {{-- Desktop Navigation --}}
        <nav class="d-none d-md-flex align-items-center gap-4">
            <a href="{{ route('landing.index') }}" wire:navigate
               class="nav-link-custom @yield('index-active')">الرئيسية</a>
            <a href="{{ route('landing.features') }}" wire:navigate class="nav-link-custom @yield('features-active')">المميزات</a>
            <a href="{{ route('landing.pricing') }}" wire:navigate class="nav-link-custom @yield('pricing-active')">الخطط
                والأسعار</a>
        </nav>

        {{-- Header Actions --}}
        <div class="d-flex align-items-center gap-2">
            <button type="button" class="theme-toggle-btn" id="appThemeToggle" aria-label="تبديل المظهر">
                <span class="material-symbols-outlined theme-icon-dark" style="font-size:20px;">dark_mode</span>
                <span class="material-symbols-outlined theme-icon-light d-none"
                      style="font-size:20px;color:var(--primary-fixed);">light_mode</span>
            </button>
        </div>
    </div>
</header>

{{-- Mobile Bottom Navigation --}}
<nav class="mobile-bottom-nav d-md-none">
    <div class="mobile-bottom-nav-inner">
        <a href="{{ route('landing.index') }}" wire:navigate class="mobile-bottom-link @yield('index-active')">
            <span class="material-symbols-outlined">home</span>
            <span>الرئيسية</span>
        </a>
        <a href="{{ route('landing.features') }}" wire:navigate class="mobile-bottom-link @yield('features-active')">
            <span class="material-symbols-outlined">auto_awesome</span>
            <span>المميزات</span>
        </a>
        <a href="{{ route('landing.pricing') }}" wire:navigate class="mobile-bottom-link @yield('pricing-active')">
            <span class="material-symbols-outlined">payments</span>
            <span>الأسعار</span>
        </a>
    </div>
</nav>
