<div>
    <li class="pc-h-item">
        <div
            class="position-relative theme-dropdown-wrapper"
            wire:click.outside="close"
        >
            <button
                type="button"
                class="pc-head-link dropdown-toggle arrow-none me-0 border-0 bg-transparent p-0 theme-trigger"
                wire:click="toggle"
                aria-haspopup="true"
                aria-expanded="{{ $open ? 'true' : 'false' }}"
            >
                <i class="ph-duotone ph-gear-six"></i>
            </button>

            {{-- Hover Preview Card --}}
            <div class="theme-hover-card">
                <div class="theme-hover-header">
                    <div class="d-flex align-items-center gap-3">
                        <div class="theme-hover-icon">
                            <i class="ph-duotone ph-palette"></i>
                        </div>
                        <div class="flex-grow-1 min-w-0">
                            <h6 class="mb-1 fw-bold">الإعدادات</h6>
                            <div class="text-muted small">تخصيص المظهر والألوان</div>
                        </div>
                    </div>
                </div>

                <div class="theme-hover-body">
                    <div class="hover-row">
                        <span class="hover-label">
                            <i class="ph-duotone ph-circle-half"></i>
                            الوضع الحالي
                        </span>
                        <span class="badge bg-{{ $themeMode === 'dark' ? 'dark' : ($themeMode === 'system' ? 'info' : 'warning') }} bg-opacity-10 text-{{ $themeMode === 'dark' ? 'dark' : ($themeMode === 'system' ? 'info' : 'warning') }}">
                            {{ $themeMode === 'dark' ? 'داكن' : ($themeMode === 'system' ? 'تلقائي' : 'فاتح') }}
                        </span>
                    </div>

                    <div class="hover-row">
                        <span class="hover-label">
                            <i class="ph-duotone ph-sidebar"></i>
                            الشريط الجانبي
                        </span>
                        <span class="badge bg-{{ $sidebarTheme === 'dark' ? 'dark' : 'light' }} bg-opacity-10 text-{{ $sidebarTheme === 'dark' ? 'dark' : 'secondary' }}">
                            {{ $sidebarTheme === 'dark' ? 'داكن' : 'فاتح' }}
                        </span>
                    </div>

                    <div class="hover-row">
                        <span class="hover-label">
                            <i class="ph-duotone ph-paint-brush"></i>
                            اللون الأساسي
                        </span>
                        <span class="accent-dot accent-{{ $themePreset }}"></span>
                    </div>
                </div>

                <div class="theme-hover-hint">
                    <i class="ph-duotone ph-cursor-click"></i>
                    <span>اضغط لتخصيص المظهر</span>
                </div>
            </div>

            {{-- Full Dropdown --}}
            @if($open)
                <div
                    class="theme-settings-dropdown show"
                    wire:ignore.self
                >
                    <div class="dropdown-header d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center gap-2">
                            <div class="header-icon">
                                <i class="ph-duotone ph-palette"></i>
                            </div>
                            <div>
                                <h5 class="m-0">الإعدادات</h5>
                                <small class="text-muted">تخصيص المظهر والألوان</small>
                            </div>
                        </div>
                        <button
                            type="button"
                            class="btn btn-sm btn-link text-muted p-0"
                            wire:click="close"
                        >
                            <i class="ph-duotone ph-x fs-5"></i>
                        </button>
                    </div>

                    <div class="dropdown-body">
                        <div class="profile-notification-scroll position-relative" style="max-height:calc(100vh - 120px)">
                            <ul class="list-group list-group-flush w-100">
                                <li class="list-group-item">
                                    <div class="pc-dark">
                                        <h6 class="mb-1">وضع المظهر</h6>
                                        <p class="text-muted text-sm">اختر بين الوضع الفاتح أو الداكن أو الوضع التلقائي</p>
                                        <div class="row theme-color theme-layout">
                                            <div class="col-4">
                                                <div class="d-grid">
                                                    <button
                                                        type="button"
                                                        class="preset-btn btn {{ $themeMode === 'light' ? 'active' : '' }}"
                                                        wire:click="setThemeMode('light')"
                                                    >
                                                        <span class="btn-label">فاتح</span>
                                                        <span class="pc-lay-icon"><span></span><span></span><span></span><span></span></span>
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="col-4">
                                                <div class="d-grid">
                                                    <button
                                                        type="button"
                                                        class="preset-btn btn {{ $themeMode === 'dark' ? 'active' : '' }}"
                                                        wire:click="setThemeMode('dark')"
                                                    >
                                                        <span class="btn-label">داكن</span>
                                                        <span class="pc-lay-icon"><span></span><span></span><span></span><span></span></span>
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="col-4">
                                                <div class="d-grid">
                                                    <button
                                                        type="button"
                                                        class="preset-btn btn {{ $themeMode === 'system' ? 'active' : '' }}"
                                                        wire:click="setThemeMode('system')"
                                                        title="يتم اختيار المظهر تلقائياً حسب إعدادات نظام التشغيل."
                                                    >
                                                        <span class="btn-label">تلقائي</span>
                                                        <span class="pc-lay-icon d-flex align-items-center justify-content-center">
                                                            <i class="ph-duotone ph-cpu"></i>
                                                        </span>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                                <li class="list-group-item">
                                    <h6 class="mb-1">مظهر الشريط الجانبي</h6>
                                    <p class="text-muted text-sm">اختر مظهر الشريط الجانبي</p>
                                    <div class="row theme-color theme-sidebar-color">
                                        <div class="col-6">
                                            <div class="d-grid">
                                                <button
                                                    type="button"
                                                    class="preset-btn btn {{ $sidebarTheme === 'dark' ? 'active' : '' }}"
                                                    wire:click="setSidebarTheme('dark')"
                                                >
                                                    <span class="btn-label">داكن</span>
                                                    <span class="pc-lay-icon"><span></span><span></span><span></span><span></span></span>
                                                </button>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="d-grid">
                                                <button
                                                    type="button"
                                                    class="preset-btn btn {{ $sidebarTheme === 'light' ? 'active' : '' }}"
                                                    wire:click="setSidebarTheme('light')"
                                                >
                                                    <span class="btn-label">فاتح</span>
                                                    <span class="pc-lay-icon"><span></span><span></span><span></span><span></span></span>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                                <li class="list-group-item">
                                    <h6 class="mb-1">لون الواجهة</h6>
                                    <p class="text-muted text-sm">اختر اللون الأساسي للموقع</p>
                                    <div class="theme-color preset-color">
                                        @for ($i = 1; $i <= 10; $i++)
                                            <a
                                                href="#"
                                                data-value="preset-{{ $i }}"
                                                class="{{ $themePreset === 'preset-' . $i ? 'active' : '' }}"
                                                wire:click.prevent="setAccentColor('preset-{{ $i }}')"
                                            >
                                                <i class="ti ti-check"></i>
                                            </a>
                                        @endfor
                                    </div>
                                </li>
                                <li class="list-group-item">
                                    <button
                                        type="button"
                                        class="btn btn-light-secondary w-100 d-inline-flex align-items-center justify-content-center gap-2"
                                        wire:click="resetSettings"
                                    >
                                        <i class="ph-duotone ph-arrow-counter-clockwise"></i>
                                        إعادة تعيين الإعدادات
                                    </button>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            @endif

            <script>
                document.addEventListener('livewire:init', function() {
                    Livewire.on('apply-theme', function(event) {
                        if (event.mode === 'system') {
                            layout_change_default();
                        } else {
                            layout_change(event.mode);
                        }
                    });
                    Livewire.on('apply-sidebar-theme', function(event) {
                        layout_sidebar_change(event.theme);
                    });
                    Livewire.on('apply-accent-color', function(event) {
                        preset_change(event.preset);
                    });
                    Livewire.on('theme-settings-reset', function() {
                        layout_change('light');
                        layout_sidebar_change('light');
                        change_box_container('false');
                        preset_change('preset-1');
                    });
                });
                document.addEventListener('livewire:navigated', function() {
                    if (typeof feather !== 'undefined') {
                        feather.replace();
                    }
                });
            </script>
        </div>
    </li>
</div>
