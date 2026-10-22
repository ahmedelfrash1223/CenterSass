@section('title', 'تفاصيل الطلب ' . $cardRequest->reference)
@section('card-requests-active', 'active')
@extends('admin-panel.master')
@section('main')
    <div class="pc-container">
        <div class="pc-content">
            <div class="page-header">
                <div class="page-block">
                    <div class="row align-items-center g-3">
                        <div class="col-md-12">
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb mb-2">
                                    <li class="breadcrumb-item">
                                        <a href="{{ route('admin.dashboard') }}" wire:navigate>
                                            <i class="ph-duotone ph-house me-1"></i>الرئيسية
                                        </a>
                                    </li>
                                    <li class="breadcrumb-item">
                                        <a href="{{ route('admin.card-requests.index') }}" wire:navigate>طلبات الكروت</a>
                                    </li>
                                    <li class="breadcrumb-item active">{{ $cardRequest->reference }}</li>
                                </ol>
                            </nav>
                        </div>
                        <div class="col-md-12">
                            <h1 class="page-header-title mb-0">
                                <i class="ph-duotone ph-identification-card me-2"></i>
                                طلب {{ $cardRequest->reference }}
                            </h1>
                        </div>
                    </div>
                </div>
            </div>
            @livewire('admin-panel.pages.card-request.parts.card-request-show', ['cardRequest' => $cardRequest])
        </div>
    </div>
@endsection
