@section('title','تعديل المدير')
@section('admin-active','active')
@extends('admin-panel.master')
@section('main')
<div class="pc-container"><div class="pc-content">
@include('admin-panel.components.page-header', ['moduleLabel' => 'المديرين', 'title' => 'تعديل المدير', 'description' => 'قم بتعديل بيانات المدير', 'icon' => 'ph-shield-user', 'pageType' => 'edit', 'pageLabel' => 'تعديل المدير', 'indexRoute' => 'admin.admins.index', 'editRoute' => 'admin.admins.edit', 'item' => $admin, 'createRoute' => 'admin.admins.create'])
@livewire('admin-panel.pages.admin.parts.admin-update', ['admin' => $admin])
</div></div>
@endsection
