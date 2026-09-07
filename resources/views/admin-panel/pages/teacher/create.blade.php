@section('title','إضافة مدرس')
@section('teacher-active','active')
@extends('admin-panel.master')
@section('main')
<div class="pc-container"><div class="pc-content">
@include('admin-panel.components.page-header', ['moduleLabel' => 'المدرسين', 'title' => 'إضافة مدرس جديد', 'description' => 'قم بإدخال بيانات المدرس الجديد', 'icon' => 'ph-chalkboard-teacher', 'pageType' => 'create', 'pageLabel' => 'إضافة مدرس جديد', 'indexRoute' => 'admin.teacher.index', 'createRoute' => 'admin.teacher.create'])
@livewire('admin-panel.pages.teacher.parts.teacher-create')
</div></div>
@endsection
