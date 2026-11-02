<head>
    <title>نبذة | @yield('title')</title>

    <!-- Resolves light / dark / system before first paint (no flash of the wrong theme) -->
    <script src="{{ asset('assets/teacherPanel/js/theme-mode.js') }}"></script>
    <!-- [Meta] -->
    <meta charset="utf-8" />
    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui"
    />


    <!-- [Favicon] Nabza book mark -->
    <link rel="icon" href="{{ asset('assets/teacherPanel/images/favicon-nabza.svg') }}" type="image/svg+xml">
    <link rel="icon" href="{{ asset('assets/teacherPanel/images/favicon-32.png') }}" type="image/png" sizes="32x32">
    <link rel="apple-touch-icon" href="{{ asset('assets/teacherPanel/images/apple-touch-icon.png') }}">

    <!-- map-vector css -->
    <link
        rel="stylesheet"
        href="{{ asset('assets/teacherPanel') }}/css/plugins/jsvectormap.min.css"
    >
    <!-- [Font: IBM Plex Sans Arabic] -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans+Arabic:wght@400;500;600;700&display=swap"
        rel="stylesheet"
    >

    <!-- [Tabler Icons] https://tablericons.com -->
    <link
        rel="stylesheet"
        href="{{ asset('assets/teacherPanel') }}/fonts/tabler-icons.min.css"
    >
    <!-- [Feather Icons] https://feathericons.com -->
    <link
        rel="stylesheet"
        href="{{ asset('assets/teacherPanel') }}/fonts/feather.css"
    >
    <!-- [Font Awesome Icons] https://fontawesome.com/icons -->
    <link
        rel="stylesheet"
        href="{{ asset('assets/teacherPanel') }}/fonts/fontawesome.css"
    >
    <!-- [Material Icons] https://fonts.google.com/icons -->
    <link
        rel="stylesheet"
        href="{{ asset('assets/teacherPanel') }}/fonts/material.css"
    >
    <!-- [Template CSS Files] -->
    <link
        rel="stylesheet"
        href="{{ asset('assets/teacherPanel') }}/css/style.css"
        id="main-style-link"
    >
    <link
        rel="stylesheet"
        href="{{ asset('assets/teacherPanel') }}/css/style-preset.css"
    >
    @livewireStyles

    <!-- [Nabza design system: loaded after the template CSS so it wins] -->
    <link rel="stylesheet" href="{{ asset('assets/teacherPanel/css/nabza-theme.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/teacherPanel/css/nabza-bridge.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/teacherPanel/css/nabza-dark.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/teacherPanel/css/layout/sidebar.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/teacherPanel/css/layout/header.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/teacherPanel/css/layout/app-frame.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/teacherPanel/css/components.css') }}">
    <!-- Page styles: scoped to their page root (.nb-home ...), safe to load everywhere with wire:navigate -->
    <link rel="stylesheet" href="{{ asset('assets/teacherPanel/css/pages/teacher-home.css') }}">

    <!-- Applies the saved collapsed-sidebar state before first paint (no layout jump) -->
    <script src="{{ asset('assets/teacherPanel/js/layout/sidebar-init.js') }}"></script>
    <script src="{{ asset('assets/teacherPanel/js/layout/sidebar.js') }}" defer></script>
    <script src="{{ asset('assets/teacherPanel/js/layout/header.js') }}" defer></script>
    <!-- Home page behaviour (greeting, live session status); does nothing on other pages -->
    <script src="{{ asset('assets/teacherPanel/js/pages/teacher-home.js') }}" defer></script>

</head>
