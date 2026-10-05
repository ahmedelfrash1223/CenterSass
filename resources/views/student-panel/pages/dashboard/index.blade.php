@extends('student-panel.master')
@section('title', 'الرئيسية')
@section('home-active', 'active')
@section('main')
    <main class="pc-container">
        <div class="pc-content">
            @include('student-panel.components.page-header', ['title' => 'الرئيسية', 'description' => 'بداية جديدة لرحلتك التعليمية.'])
            @livewire('student-panel.pages.dashboard.parts.data')
        </div>
    </main>
@endsection
