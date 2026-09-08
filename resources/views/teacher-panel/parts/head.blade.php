<head>
    <title>Classio | @yield('title')</title>
    <!-- [Meta] -->
    <meta charset="utf-8" />
    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui"
    />


    <!-- [Favicon] icon -->
    <link
        rel="icon"
        href="{{ asset('assets/teacherPanel/images/favicon.png') }}"
        type="image/x-icon"
    />

    <!-- map-vector css -->
    <link
        rel="stylesheet"
        href="{{ asset('assets/teacherPanel') }}/css/plugins/jsvectormap.min.css"
    >
    <!-- [Google Font : Public Sans] icon -->
    <link
        href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@400;500;600;700&display=swap"
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
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Cairo:wght@600&display=swap');

        html,
        body,
        * {
            font-family: 'Cairo', sans-serif !important;
            font-weight: 600 !important;
        }
    </style>

</head>
