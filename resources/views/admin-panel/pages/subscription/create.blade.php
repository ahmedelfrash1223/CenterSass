@section('title','إضافة اشتراك')
@section('subscription-active','active')
@extends('admin-panel.master')
@section('main')
<div class="pc-container"><div class="pc-content">
@include('admin-panel.components.page-header', ['moduleLabel' => 'الاشتراكات', 'title' => 'إضافة اشتراك جديد', 'description' => 'قم بإدخال بيانات الاشتراك الجديد', 'icon' => 'ph-repeat', 'pageType' => 'create', 'pageLabel' => 'إضافة اشتراك جديد', 'indexRoute' => 'admin.subscriptions.index', 'createRoute' => 'admin.subscriptions.create'])
@livewire('admin-panel.pages.subscription.parts.subscription-create')
</div></div>
@endsection
