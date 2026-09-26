<!DOCTYPE html>
<html lang="ar" dir="rtl" data-bs-theme="light">

@include('landing.parts.head')

<body>

<!-- Header -->
@include('landing.parts.header')
<main style="padding-top:5rem;" class="position-relative">
    <div class="hero-glow-a"></div>

    @yield('main')
</main>

@include('landing.parts.footer')
@include('landing.parts.scripts')
</body>
</html>
