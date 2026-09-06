@php
    $themeSettings = session('admin_theme_settings', [
        'theme_mode' => 'light',
        'sidebar_theme' => 'light',
        'accent_color' => 'preset-1',
        'layout_width' => 'full',
    ]);
@endphp

    <!-- [Page Specific JS] start -->
<script src="{{asset('assets/adminPanel')}}/js/plugins/apexcharts.min.js"></script>
<script src="{{asset('assets/adminPanel')}}/js/plugins/jsvectormap.min.js"></script>
<script src="{{asset('assets/adminPanel')}}/js/plugins/world.js"></script>
<script src="{{asset('assets/adminPanel')}}/js/plugins/world-merc.js"></script>
<!-- [Page Specific JS] end -->

<!-- Required Js -->
<script src="{{asset('assets/adminPanel')}}/js/plugins/popper.min.js"></script>
<script src="{{asset('assets/adminPanel')}}/js/plugins/simplebar.min.js"></script>
<script src="{{asset('assets/adminPanel')}}/js/plugins/bootstrap.min.js"></script>
<script src="{{asset('assets/adminPanel')}}/js/fonts/custom-font.js"></script>
<script src="{{asset('assets/adminPanel')}}/js/pcoded.js"></script>
<script src="{{asset('assets/adminPanel')}}/js/plugins/feather.min.js"></script>

<!-- Theme Settings Js -->
<script>layout_change('{{ $themeSettings['theme_mode'] ?? 'light' }}');</script>
<script>layout_sidebar_change('{{ $themeSettings['sidebar_theme'] ?? 'light' }}');</script>
<script>change_box_container('{{ ($themeSettings['layout_width'] ?? 'full') === 'boxed' ? 'true' : 'false' }}');</script>
<script>layout_caption_change('true');</script>
<script>layout_rtl_change('true');</script>
<script>preset_change('{{ $themeSettings['accent_color'] ?? 'preset-1' }}');</script>
