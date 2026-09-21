@section('title', 'تفاصيل الفاتورة')
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
                                        <a href="{{ route('teacher.invoices.index') }}" wire:navigate class="text-decoration-none">
                                            الفواتير
                                        </a>
                                    </li>
                                    <li class="breadcrumb-item active" aria-current="page">
                                        <i class="ph-duotone ph-receipt me-1"></i>{{ $invoice->invoice_number }}
                                    </li>
                                </ol>
                            </nav>
                        </div>
                        <div class="col-md-12">
                            <div class="d-flex align-items-center gap-3">
                                <div class="page-header-icon-wrapper">
                                    <div class="page-header-icon">
                                        <i class="ph-duotone ph-receipt fs-1"></i>
                                    </div>
                                </div>
                                <div>
                                    <h1 class="page-header-title mb-0">
                                        {{ $invoice->invoice_number }}
                                        <span class="badge bg-primary bg-opacity-10 text-primary ms-2 fs-6 fw-normal">
                                            <i class="ph-duotone ph-file-text me-1"></i>تفاصيل
                                        </span>
                                    </h1>
                                    <p class="text-muted small mb-0 mt-1">
                                        <i class="ph-duotone ph-dot-outline me-1"></i>عرض تفاصيل الفاتورة والمدفوعات
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @livewire('teacher-panel.pages.invoice.parts.invoice-show', ['invoice' => $invoice])
        </div>
    </div>
@endsection
