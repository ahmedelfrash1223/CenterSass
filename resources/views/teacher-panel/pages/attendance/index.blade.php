@section('title', 'تسجيل الحضور')
@section('attendance-active', 'active')
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
                                    <li class="breadcrumb-item active">
                                        <i class="ph-duotone ph-scan me-1"></i>تسجيل الحضور
                                    </li>
                                </ol>
                            </nav>
                        </div>
                        <div class="col-md-12">
                            <div class="d-flex align-items-center justify-content-between gap-3 flex-wrap">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="page-header-icon-wrapper">
                                        <div class="page-header-icon">
                                            <i class="ph-duotone ph-scan fs-1"></i>
                                        </div>
                                    </div>
                                    <div>
                                        <h1 class="page-header-title mb-0">
                                            تسجيل الحضور
                                            <span class="badge bg-primary bg-opacity-10 text-primary ms-2 fs-6 fw-normal">
                                                <i class="ph-duotone ph-lightning me-1"></i>سكان سريع
                                            </span>
                                        </h1>
                                        <p class="text-muted small mb-0 mt-1">
                                            امسح كارت الطالب بالكاميرا أو الاسكانر، أو سجّل يدوي
                                        </p>
                                    </div>
                                </div>
                                <a href="{{ route('teacher.attendance.records') }}" wire:navigate
                                   class="btn btn-light border">
                                    <i class="ph-duotone ph-clipboard-text me-1"></i>
                                    سجل الحضور
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @livewire('teacher-panel.pages.attendance.parts.attendance-scanner')
        </div>
    </div>
@endsection
