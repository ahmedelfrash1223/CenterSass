<header class="nb-header" data-nb-header>
    <div class="nb-header__start">
        <button
            type="button"
            class="nb-icon-btn nb-header__toggle"
            aria-label="إظهار وإخفاء القائمة الجانبية"
            aria-controls="nb-sidebar"
            aria-expanded="true"
            data-nb-sidebar-toggle
        >
            <svg viewBox="0 0 24 24" fill="none" aria-hidden="true" focusable="false">
                <path d="M4 6.5h16M4 12h16M4 17.5h16" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
            </svg>
        </button>

        <h1 class="nb-header__title">
            @hasSection('page_title')
                @yield('page_title')
            @else
                @yield('title', 'لوحة التحكم')
            @endif
        </h1>

        <button type="button" class="nb-icon-btn nb-header__refresh" aria-label="تحديث" data-nb-refresh>
            <svg viewBox="0 0 24 24" fill="none" aria-hidden="true" focusable="false">
                <path d="M19.5 12a7.5 7.5 0 1 1-2.2-5.3" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                <path d="M19.5 4.5v3.75h-3.75" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </button>
    </div>

    <div class="nb-header__end">
        @livewire('teacher-panel.theme-settings')

        @livewire('teacher-panel.parts.teacher-profile-card')
    </div>
</header>
