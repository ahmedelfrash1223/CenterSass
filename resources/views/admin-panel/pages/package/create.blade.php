@section('title','إضافة باقة')
@section('package-active','active')
@extends('admin-panel.master')
@section('main')
<div class="pc-container"><div class="pc-content">
@include('admin-panel.components.page-header', ['moduleLabel' => 'الباقات', 'title' => 'إضافة باقة جديدة', 'description' => 'قم بإدخال بيانات الباقة الجديدة', 'icon' => 'ph-package', 'pageType' => 'create', 'pageLabel' => 'إضافة باقة جديدة', 'indexRoute' => 'admin.package.index', 'createRoute' => 'admin.package.create'])
@livewire('admin-panel.pages.package.parts.package-create')
</div></div>
@endsection
