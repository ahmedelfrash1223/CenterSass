@section('title','إضافة اشتراك')
@section('subscription-active','active')
@extends('admin-panel.master')
@section('main')
    <div class="pc-container">
        <div class="pc-content">
            <div class="page-header">
                <div class="page-block"><h1 class="page-header-title mb-0"><i
                            class="ph-duotone ph-plus-circle me-2"></i>إضافة اشتراك جديد</h1></div>
            </div>@livewire('admin-panel.pages.subscription.parts.subscription-create')</div>
    </div>
@endsection
