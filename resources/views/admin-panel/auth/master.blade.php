@php
    $themeSettings = session('admin_theme_settings', [
        'theme_mode' => 'light',
        'sidebar_theme' => 'light',
        'accent_color' => 'preset-1',
        'layout_width' => 'full',
    ]);
@endphp

    <!DOCTYPE html>
<html lang="en">

<!-- [Head] start -->
@include('admin-panel.auth.parts.head')
<!-- [Head] end -->

<!-- [Body] Start -->

<body data-pc-preset="{{ $themeSettings['accent_color'] ?? 'preset-1' }}"
      data-pc-sidebar-theme="{{ $themeSettings['sidebar_theme'] ?? 'light' }}"
      data-pc-theme="{{ $themeSettings['theme_mode'] ?? 'light' }}"
      data-pc-sidebar-caption="true"
      data-pc-direction="rtl">

<!-- [ Pre-loader ] start -->
<div class="loader-bg">
    <div class="loader-track">
        <div class="loader-fill"></div>
    </div>
</div>
<!-- [ Pre-loader ] End -->

<!-- [ Main Content ] start -->
@yield('main')
<!-- [ Main Content ] end -->

@include('admin-panel.auth.parts.scripts')
@livewireScripts

</body>
<!-- [Body] end -->
</html>
