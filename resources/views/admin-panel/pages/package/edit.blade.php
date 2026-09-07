@section('title', 'تعديل الباقة')
@section('package-active', 'active')
@extends('admin-panel.master')
@section('main')
    <div class="pc-container">
        <div class="pc-content">
            @include('admin-panel.components.page-header', [
                'moduleLabel' => 'الباقات',
                'title' => 'تعديل الباقة',
                'description' => 'قم بتعديل بيانات الباقة',
                'icon' => 'ph-package',
                'pageType' => 'edit',
                'pageLabel' => 'تعديل الباقة',
                'indexRoute' => 'admin.package.index',
                'editRoute' => 'admin.package.edit',
                'item' => $package,
                'createRoute' => 'admin.package.create',
            ])
            @livewire('admin-panel.pages.package.parts.package-update', ['package' => $package])
        </div>
    </div>
@endsection
