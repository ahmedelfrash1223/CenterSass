@php
    $teacher = \Illuminate\Support\Facades\Auth::guard('teacher')->user();
    $themeSettings = $teacher?->themeSettings;
@endphp
<!DOCTYPE html>
<html lang="en">

<!-- [Head] start -->
@include('teacher-panel.parts.head')
<!-- [Head] end -->

<!-- [Body] Start -->

<body
    data-pc-preset="{{ $themeSettings?->accent_color ?? 'preset-1' }}"
    data-pc-sidebar-theme="{{ $themeSettings?->sidebar_theme ?? 'light' }}"
    data-pc-theme="{{ $themeSettings?->theme_mode ?? 'light' }}"
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

    <!-- [ Sidebar Menu ] start -->
    @include('teacher-panel.parts.sidebar')
    <!-- [ Sidebar Menu ] end -->

    <!-- [ Header Topbar ] start -->
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
