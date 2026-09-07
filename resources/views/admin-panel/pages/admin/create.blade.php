@section('title','إضافة مدير')
@section('admin-active','active')
@extends('admin-panel.master')
@section('main')
<div class="pc-container"><div class="pc-content">
@include('admin-panel.components.page-header', ['moduleLabel' => 'المديرين', 'title' => 'إضافة مدير جديد', 'description' => 'قم بإدخال بيانات المدير الجديد', 'icon' => 'ph-shield-user', 'pageType' => 'create', 'pageLabel' => 'إضافة مدير جديد', 'indexRoute' => 'admin.admins.index', 'createRoute' => 'admin.admins.create'])
@livewire('admin-panel.pages.admin.parts.admin-create')
</div></div>
@endsection
