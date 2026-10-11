@section('title', 'المتابعة')
@section('assignments-active', 'active')
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
                                    <li class="breadcrumb-item active">
                                        <i class="ph-duotone ph-check-square me-1"></i>المتابعة
                                    </li>
                                </ol>
                            </nav>
                        </div>
                        <div class="col-md-12">
                            <h1 class="page-header-title mb-0">
                                <i class="ph-duotone ph-check-square me-2"></i>المتابعة
                                <span class="badge bg-primary bg-opacity-10 text-primary ms-2 fs-6 fw-normal">
                                    <i class="ph-duotone ph-list me-1"></i>إدارة
                                </span>
                            </h1>
                            <p class="text-muted small mb-0 mt-1">
                                الواجبات، التسميع، والتقييمات
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            @livewire('teacher-panel.pages.assignment.parts.assignment-data')
        </div>
    </div>
@endsection
