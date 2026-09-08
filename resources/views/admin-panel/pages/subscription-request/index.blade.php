@section('title', 'طلبات الاشتراك')
@section('subscription-request-active', 'active')
@extends('admin-panel.master')
@section('main')
    <div class="pc-container">
        <div class="pc-content">
            @include('admin-panel.components.page-header', [
                'moduleLabel' => 'طلبات الاشتراك',
                'title' => 'طلبات الاشتراك',
                'description' => 'عرض وإدارة طلبات الاشتراك',
                'icon' => 'ph-repeat',
                'pageType' => 'index',
                'indexRoute' => 'admin.subscription-request',
                'createRoute' => '',
            ])
            @livewire('admin-panel.pages.subscription-request.parts.request-data')
        </div>
    </div>
@endsection
