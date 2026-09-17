@section('title', 'تقفيل اليوم')
@section('attendance-close-day-active', 'active')
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
                                        <i class="ph-duotone ph-lock me-1"></i>تقفيل اليوم
                                    </li>
                                </ol>
                            </nav>
                        </div>
                        <div class="col-md-12">
                            <div class="d-flex align-items-center gap-3">
                                <div class="page-header-icon-wrapper">
                                    <div class="page-header-icon">
                                        <i class="ph-duotone ph-lock fs-1"></i>
                                    </div>
                                </div>
                                <div>
                                    <h1 class="page-header-title mb-0">
                                        تقفيل اليوم
                                        <span class="badge bg-primary bg-opacity-10 text-primary ms-2 fs-6 fw-normal">
                                            <i class="ph-duotone ph-calendar-check me-1"></i>يومي
                                        </span>
                                    </h1>
                                    <p class="text-muted small mb-0 mt-1">
                                        <i class="ph-duotone ph-info me-1"></i>
                                        سجّل غياب الطلاب لكل مجموعة لم يحضروا
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @livewire('teacher-panel.pages.attendance-close-day.parts.close-day-index')
        </div>
    </div>
@endsection
