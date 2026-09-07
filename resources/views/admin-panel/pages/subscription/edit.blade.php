@section('title','تعديل الاشتراك')
@section('subscription-active','active')
@extends('admin-panel.master')
@section('main')<div class="pc-container"><div class="pc-content"><div class="page-header"><div class="page-block"><h1 class="page-header-title mb-0"><i class="ph-duotone ph-pencil-simple me-2"></i>تعديل الاشتراك</h1></div></div>@livewire('admin-panel.pages.subscription.parts.subscription-update', ['subscription' => $subscription])</div></div>@endsection
