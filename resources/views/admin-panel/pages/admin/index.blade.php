@section('title','المديرين')
@section('admin-active','active')
@extends('admin-panel.master')
@section('main')
    <div class="pc-container">
        <div class="pc-content">
            @include('admin-panel.components.page-header', ['moduleLabel' => 'المديرين', 'title' => 'المديرين', 'description' => 'عرض وإدارة جميع المديرين المسجلين', 'icon' => 'ph-shield-user', 'pageType' => 'index', 'indexRoute' => 'admin.admins.index', 'createRoute' => 'admin.admins.create'])
            @livewire('admin-panel.pages.admin.parts.admin-data')
        </div>
    </div>
@endsection
