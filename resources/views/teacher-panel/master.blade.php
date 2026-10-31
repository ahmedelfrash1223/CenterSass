@php
    $teacher = \Illuminate\Support\Facades\Auth::guard('teacher')->user();
    $themeSettings = $teacher?->themeSettings;

    // Light / dark / system preference (DB). "system" is resolved before first paint by theme-mode.js.
    $themePreference = in_array($themeSettings?->theme_mode, ['light', 'dark', 'system'], true)
        ? $themeSettings->theme_mode
        : 'light';
    $themeMode = $themePreference === 'dark' ? 'dark' : 'light';
@endphp
<!DOCTYPE html>
<html lang="ar" dir="rtl" data-pc-theme="{{ $themeMode }}" data-nb-theme-pref="{{ $themePreference }}">

<!-- [Head] start -->
@include('teacher-panel.parts.head')
<!-- [Head] end -->

<!-- [Body] Start -->

<body
    class="nb-app"
    data-pc-sidebar-caption="true"
    data-pc-direction="rtl"
>

    <!-- [ Pre-loader ] start -->
    <div class="loader-bg">
        <div class="loader-track">
            <div class="loader-fill"></div>
        </div>
    </div>
    <!-- [ Pre-loader ] End -->

    <!-- [ Sidebar ] start -->
    @include('teacher-panel.parts.sidebar')
    <!-- [ Sidebar ] end -->

    <!-- [ Header ] start -->
    @include('teacher-panel.parts.header')
    <!-- [ Header ] end -->

    <!-- [ Main Content ] start -->
    @yield('main')
    <!-- [ Main Content ] end -->

    @include('teacher-panel.parts.footer')
    @include('teacher-panel.parts.scripts')
</body>
<!-- [Body] end -->

</html>
