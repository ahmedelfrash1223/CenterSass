@section('title', 'عرض الباقة')
@section('package-active', 'active')
@extends('admin-panel.master')
@section('main')
    <div class="pc-container">
        <div class="pc-content">
            @include('admin-panel.components.page-header', [
                'moduleLabel' => 'الباقات',
                'title' => $package->title,
                'description' => 'عرض تفاصيل الباقة وخصائصها',
                'icon' => 'ph-package',
                'pageType' => 'show',
                'pageLabel' => 'عرض',
                'indexRoute' => 'admin.package.index',
                'editRoute' => 'admin.package.edit',
                'item' => $package,
                'createRoute' => 'admin.package.create',
            ])
            @livewire('admin-panel.pages.package.parts.package-show', ['package' => $package])
        </div>
    </div>
@endsection
