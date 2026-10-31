<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#F6F8F1">
    <title>نبذة | @yield('title')</title>

    <link rel="icon" href="{{ asset('assets/teacherPanel/images/favicon.png') }}" type="image/png">

    <!-- Fonts: Reem Kufi (headings) + IBM Plex Sans Arabic (body) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans+Arabic:wght@400;500;600&family=Reem+Kufi:wght@600;700&display=swap" rel="stylesheet">

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
