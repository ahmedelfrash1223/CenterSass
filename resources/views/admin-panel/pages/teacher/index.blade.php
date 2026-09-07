@section('title', 'المدرسين')
@section('teacher-active', 'active')
@extends('admin-panel.master')
@section('main')
    <div class="pc-container">
        <div class="pc-content">
            @include('admin-panel.components.page-header', [
                'moduleLabel' => 'المدرسين',
                'title' => 'المدرسين',
                'description' => 'عرض وإدارة جميع المدرسين المسجلين',
                'icon' => 'ph-chalkboard-teacher',
                'pageType' => 'index',
                'indexRoute' => 'admin.teacher.index',
                'createRoute' => 'admin.teacher.create',
            ])
            @livewire('admin-panel.pages.teacher.parts.teacher-data')
        </div>
    </div>
@endsection
