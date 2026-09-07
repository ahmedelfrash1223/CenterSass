@section('title', 'تعديل المدرس')
@section('teacher-active', 'active')
@extends('admin-panel.master')
@section('main')
    <div class="pc-container">
        <div class="pc-content">
            @include('admin-panel.components.page-header', [
                'moduleLabel' => 'المدرسين',
                'title' => 'تعديل المدرس',
                'description' => 'قم بتعديل بيانات المدرس',
                'icon' => 'ph-chalkboard-teacher',
                'pageType' => 'edit',
                'pageLabel' => 'تعديل المدرس',
                'indexRoute' => 'admin.teacher.index',
                'editRoute' => 'admin.teacher.edit',
                'item' => $teacher,
                'createRoute' => 'admin.teacher.create',
            ])
            @livewire('admin-panel.pages.teacher.parts.teacher-update', ['teacher' => $teacher])
        </div>
    </div>
@endsection
