@section('title', 'الباقات')
@section('package-active', 'active')
@extends('admin-panel.master')
@section('main')
    <div class="pc-container">
        <div class="pc-content">
            @include('admin-panel.components.page-header', [
                'moduleLabel' => 'الباقات',
                'title' => 'الباقات',
                'description' => 'عرض وإدارة جميع الباقات المتاحة',
                'icon' => 'ph-package',
                'pageType' => 'index',
                'indexRoute' => 'admin.package.index',
                'createRoute' => 'admin.package.create',
            ])
            @livewire('admin-panel.pages.package.parts.package-data')
        </div>
    </div>
@endsection
