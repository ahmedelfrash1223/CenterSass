<nav id="student-sidebar" class="pc-sidebar" x-bind:class="{ 'mob-sidebar-active': sidebarOpen, 'pc-sidebar-hide': sidebarHidden }" aria-label="القائمة الرئيسية">
    <div class="navbar-wrapper">
        <div class="m-header">
            <a href="{{ route('student.dashboard') }}" wire:navigate class="b-brand">
                <img class="student-sidebar-logo" src="{{ asset('assets/brand.svg') }}" alt="نبذة">
            </a>
        </div>
        <div class="navbar-content">
            <p class="student-nav-caption">مساحة الطالب</p>
            <ul class="pc-navbar">
                <li class="pc-item @yield('home-active')">
                    <a href="{{ route('student.dashboard') }}" wire:navigate class="pc-link" @if(request()->routeIs('student.dashboard')) aria-current="page" @endif>
                        <span class="pc-micon"><i class="ti ti-dashboard" aria-hidden="true"></i></span>
                        <span class="pc-mtext">الرئيسية</span>
                    </a>
                </li>
                <li class="pc-item @yield('profile-active')">
                    <a href="{{ route('student.profile') }}" wire:navigate class="pc-link" @if(request()->routeIs('student.profile')) aria-current="page" @endif>
                        <span class="pc-micon"><i class="ti ti-user" aria-hidden="true"></i></span>
                        <span class="pc-mtext">الملف الشخصي</span>
                    </a>
                </li>
            </ul>
            <div class="student-sidebar-note"><i class="ti ti-stars fs-3" aria-hidden="true"></i><p>رحلتك التعليمية<br>تستحق بداية مميزة.</p></div>
        </div>
    </div>
</nav>
