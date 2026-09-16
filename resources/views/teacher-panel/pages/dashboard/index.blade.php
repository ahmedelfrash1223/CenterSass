@section('home-active', 'active')
@section('title', 'لوحة التحكم')
@extends('teacher-panel.master')

@section('main')

    <div class="pc-container">
        <div class="pc-content">

            <!-- Breadcrumb -->
            <div class="page-header">
                <div class="page-block">
                    <div class="row align-items-center g-3">
                        <!-- Breadcrumb -->
                        <div class="col-md-12">
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb mb-2">
                                    <li class="breadcrumb-item">
                                        <a
                                            wire:navigate
                                            href="{{ route('teacher.dashboard') }}"
                                            class="text-decoration-none"
                                        >
                                            <i class="ph-duotone ph-house me-1"></i>
                                            الرئيسية
                                        </a>
                                    </li>
                                    <li
                                        class="breadcrumb-item active"
                                        aria-current="page"
                                    >
                                        <i class="ph-duotone ph-gauge me-1"></i>
                                        لوحة التحكم
                                    </li>
                                </ol>
                            </nav>
                        </div>

                        <!-- Title with Icon and Stats -->
                        <div class="col-md-12">
                            <div class="d-flex align-items-center gap-3 flex-wrap">
                                <div class="page-header-icon-wrapper">
                                    <div class="page-header-icon">
                                        <i class="ph-duotone ph-gauge fs-1"></i>
                                    </div>
                                </div>

                                <div class="flex-grow-1">
                                    <h1 class="page-header-title mb-1">
                                        لوحة التحكم
                                        <span class="badge bg-primary bg-opacity-10 text-primary ms-2 fs-6 fw-normal">
                                            <i class="ph-duotone ph-check-circle me-1"></i>
                                            مرحباً بعودتك
                                        </span>
                                    </h1>
                                    <p class="text-muted mb-0">
                                        <i class="ph-duotone ph-calendar-blank me-1"></i>
                                        {{ now()->format('l، d F Y') }}
                                        <span class="mx-2">|</span>
                                        <i class="ph-duotone ph-clock me-1"></i>
                                        {{ now()->format('h:i A') }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @livewire('teacher-panel.pages.dashboard.parts.data')
        </div>
    </div>

@endsection
