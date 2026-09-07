@section('title', 'تعديل الاشتراك')
@section('subscription-active', 'active')
@extends('admin-panel.master')
@section('main')
    <div class="pc-container">
        <div class="pc-content">
            @include('admin-panel.components.page-header', [
                'moduleLabel' => 'الاشتراكات',
                'title' => 'تعديل الاشتراك',
                'description' => 'قم بتعديل بيانات الاشتراك',
                'icon' => 'ph-repeat',
                'pageType' => 'edit',
                'pageLabel' => 'تعديل الاشتراك',
                'indexRoute' => 'admin.subscriptions.index',
                'editRoute' => 'admin.subscriptions.edit',
                'item' => $subscription,
                'createRoute' => 'admin.subscriptions.create',
            ])
            @livewire('admin-panel.pages.subscription.parts.subscription-update', ['subscription' => $subscription])
        </div>
    </div>
@endsection
