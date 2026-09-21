@section('title', 'الفواتير')
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
                                        <a href="{{ route('teacher.dashboard') }}" wire:navigate class="text-decoration-none">
                                            <i class="ph-duotone ph-house me-1"></i>الرئيسية
                                        </a>
                                    </li>
                                    <li class="breadcrumb-item">
                                        <a href="{{ route('teacher.payments.index') }}" wire:navigate class="text-decoration-none">
                                            المدفوعات
                                        </a>
                                    </li>
                                    <li class="breadcrumb-item active" aria-current="page">
                                        <i class="ph-duotone ph-receipt me-1"></i>الفواتير
                                    </li>
                                </ol>
                            </nav>
                        </div>
                        <div class="col-md-12">
                            <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="page-header-icon-wrapper">
                                        <div class="page-header-icon">
                                            <i class="ph-duotone ph-receipt fs-1"></i>
                                        </div>
                                    </div>
                                    <div>
                                        <h1 class="page-header-title mb-0">
                                            الفواتير
                                            <span class="badge bg-primary bg-opacity-10 text-primary ms-2 fs-6 fw-normal">
                                                <i class="ph-duotone ph-list me-1"></i>إدارة
                                            </span>
                                        </h1>
                                        <p class="text-muted small mb-0 mt-1">
                                            <i class="ph-duotone ph-dot-outline me-1"></i>عرض وإدارة كل الفواتير
                                        </p>
                                    </div>
                                </div>
                                <div class="d-flex gap-2">
                                    <a href="{{ route('teacher.payments.index') }}" wire:navigate class="btn btn-light border">
                                        <i class="ph-duotone ph-currency-circle-dollar me-1"></i>صفحة الدفع
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @livewire('teacher-panel.pages.payment.parts.month-selector', ['context' => 'invoices'])

            @livewire('teacher-panel.pages.invoice.parts.invoice-data')
        </div>
    </div>
@endsection
