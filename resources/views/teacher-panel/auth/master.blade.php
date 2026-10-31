<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#FFFFFF">
    <title>نبذة | @yield('title')</title>

    <link rel="icon" href="{{ asset('assets/teacherPanel/images/favicon-nabza.svg') }}" type="image/svg+xml">
    <link rel="icon" href="{{ asset('assets/teacherPanel/images/favicon-32.png') }}" type="image/png" sizes="32x32">
    <link rel="apple-touch-icon" href="{{ asset('assets/teacherPanel/images/apple-touch-icon.png') }}">

    <!-- Font: IBM Plex Sans Arabic -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans+Arabic:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Nabza design tokens -->
    <link rel="stylesheet" href="{{ asset('assets/teacherPanel/css/nabza-theme.css') }}">
    @stack('styles')
</head>

<body class="nb-body">

    @yield('main')

    @livewireScripts
    @stack('scripts')

</body>

</html>
