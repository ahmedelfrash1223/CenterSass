@section('title', 'عرض المدير')
@section('admin-active', 'active')
@extends('admin-panel.master')
@section('main')
    <div class="pc-container">
        <div class="pc-content">
            @include('admin-panel.components.page-header', [
                'moduleLabel' => 'المديرين',
                'title' => $admin->name,
                'description' => 'عرض بيانات المدير وصلاحياته',
                'icon' => 'ph-shield-user',
                'pageType' => 'show',
                'pageLabel' => 'عرض',
                'indexRoute' => 'admin.admins.index',
                'editRoute' => 'admin.admins.edit',
                'item' => $admin,
                'createRoute' => 'admin.admins.create',
            ])
            @livewire('admin-panel.pages.admin.parts.admin-show', ['admin' => $admin])
        </div>
    </div>
@endsection
