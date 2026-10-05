<!DOCTYPE html>
<html lang="ar" dir="rtl">
@include('student-panel.parts.head')
<body class="student-panel" data-pc-preset="preset-1" data-pc-sidebar-theme="light" data-pc-theme="light" data-pc-direction="rtl"
      data-pc-sidebar-caption="true"
      x-data="{ sidebarOpen: false, sidebarHidden: false }"
      x-on:keydown.escape.window="sidebarOpen = false"
      x-on:resize.window="if (window.innerWidth >= 1025) sidebarOpen = false"
      x-on:livewire:navigating.window="sidebarOpen = false">
    @include('student-panel.parts.sidebar')
    <button type="button" class="student-menu-overlay" x-cloak x-show="sidebarOpen" x-on:click="sidebarOpen = false" aria-label="إغلاق القائمة"></button>
    @include('student-panel.parts.header')
    @yield('main')
    @include('student-panel.parts.footer')
    @include('student-panel.parts.scripts')
</body>
</html>
