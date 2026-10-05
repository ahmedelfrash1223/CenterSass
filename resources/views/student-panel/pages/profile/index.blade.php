@extends('student-panel.master')
@section('title', 'الملف الشخصي')
@section('profile-active', 'active')
@section('main')
    <main class="pc-container">
        <div class="pc-content">
            @include('student-panel.components.page-header', ['title' => 'الملف الشخصي', 'description' => 'بياناتك الشخصية وإعدادات أمان حسابك.'])
            @livewire('student-panel.pages.profile.parts.profile-form')
        </div>
    </main>
@endsection
