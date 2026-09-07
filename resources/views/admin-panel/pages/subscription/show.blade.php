@section('title','عرض الاشتراك')
@section('subscription-active','active')
@extends('admin-panel.master')
@section('main')
<div class="pc-container"><div class="pc-content">
@include('admin-panel.components.page-header', ['moduleLabel' => 'الاشتراكات', 'title' => 'اشتراك #'.$subscription->id, 'description' => 'عرض تفاصيل الاشتراك وسجل التغييرات', 'icon' => 'ph-repeat', 'pageType' => 'show', 'pageLabel' => 'عرض', 'indexRoute' => 'admin.subscriptions.index', 'editRoute' => 'admin.subscriptions.edit', 'item' => $subscription, 'createRoute' => 'admin.subscriptions.create'])
@livewire('admin-panel.pages.subscription.parts.subscription-show', ['subscription' => $subscription])
</div></div>
@endsection
