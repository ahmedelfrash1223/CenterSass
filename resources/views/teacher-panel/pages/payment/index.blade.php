@section('title', 'المدفوعات')
@section('payments-active', 'active')
@extends('teacher-panel.master')
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
                                        <a href="{{ route('teacher.dashboard') }}" wire:navigate>
                                            <i class="ph-duotone ph-house me-1"></i>الرئيسية
                                        </a>
                                    </li>
                                    <li class="breadcrumb-item active">المدفوعات</li>
                                </ol>
                            </nav>
                        </div>
                        <div class="col-md-12">
                            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                                <div>
                                    <h1 class="page-header-title mb-1">
                                        <i class="ph-duotone ph-currency-circle-dollar me-2"></i>المدفوعات
                                    </h1>
                                    <p class="text-muted small mb-0">ابحث عن الطالب وسجّل دفعاته</p>
                                </div>
                                <a href="{{ route('teacher.invoices.index') }}" wire:navigate
                                   class="btn btn-outline-primary">
                                    <i class="ph-duotone ph-receipt me-1"></i>الفواتير
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @livewire('teacher-panel.pages.payment.parts.payment-search')
            @livewire('teacher-panel.pages.payment.parts.student-payment-card')
        </div>
    </div>
@endsection
