@section('title', 'عرض المدرس')
@section('teacher-active', 'active')
@extends('admin-panel.master')
@section('main')
    <div class="pc-container">
        <div class="pc-content">
            @include('admin-panel.components.page-header', [
                'moduleLabel' => 'المدرسين',
                'title' => $teacher->name,
                'description' => 'عرض بيانات المدرس والطلاب المرتبطين به',
                'icon' => 'ph-chalkboard-teacher',
                'pageType' => 'show',
                'pageLabel' => 'عرض',
                'indexRoute' => 'admin.teacher.index',
                'editRoute' => 'admin.teacher.edit',
                'item' => $teacher,
                'createRoute' => 'admin.teacher.create',
            ])
            @livewire('admin-panel.pages.teacher.parts.teacher-show', ['teacher' => $teacher])
        </div>
    </div>
@endsection
