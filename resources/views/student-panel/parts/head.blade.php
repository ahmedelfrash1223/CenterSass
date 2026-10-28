<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>نبذة | @yield('title')</title>
    <link rel="icon" href="{{ asset('assets/studentPanel/images/favicon.png') }}">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;500;600;700;800&display=swap">
    <link rel="stylesheet" href="{{ asset('assets/studentPanel/fonts/tabler-icons.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/studentPanel/css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/studentPanel/css/style-preset.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/studentPanel/css/student.css') }}">
    @livewireStyles
</head>
