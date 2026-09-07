@section('title','الاشتراكات')
@section('subscription-active','active')
@extends('admin-panel.master')
@section('main')
<div class="pc-container"><div class="pc-content">
@include('admin-panel.components.page-header', ['moduleLabel' => 'الاشتراكات', 'title' => 'الاشتراكات', 'description' => 'عرض وإدارة اشتراكات المدرسين', 'icon' => 'ph-repeat', 'pageType' => 'index', 'indexRoute' => 'admin.subscriptions.index', 'createRoute' => 'admin.subscriptions.create'])
@livewire('admin-panel.pages.subscription.parts.subscription-data')
</div></div>
@endsection
