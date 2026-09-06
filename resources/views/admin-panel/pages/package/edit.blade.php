@section('title','الباقات')
@section('package-active','active')
@extends('admin-panel.master')
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
                                        <a href="{{ route('admin.dashboard') }}" class="text-decoration-none">
                                            <i class="ph-duotone ph-house me-1"></i>
                                            الرئيسية
                                        </a>
                                    </li>
                                    <li class="breadcrumb-item">
                                        <a href="{{ route('admin.package.index') }}" class="text-decoration-none">
                                            <i class="ph-duotone ph-package me-1"></i>
                                            الباقات
                                        </a>
                                    </li>
                                    <li class="breadcrumb-item active" aria-current="page">
                                        <i class="ph-duotone ph-pencil-simple me-1"></i>
                                        تعديل الباقة
                                    </li>
                                </ol>
                            </nav>
                        </div>

                        <!-- Title -->
                        <div class="col-md-12">
                            <div class="d-flex align-items-center gap-3">
                                <div class="page-header-icon-wrapper">
                                    <div class="page-header-icon">
                                        <i class="ph-duotone ph-pencil-simple fs-1"></i>
                                    </div>
                                </div>

                                <div>
                                    <h1 class="page-header-title mb-0">
                                        تعديل الباقة
                                        <span class="badge bg-warning bg-opacity-10 text-warning ms-2 fs-6 fw-normal">
                                <i class="ph-duotone ph-edit me-1"></i>
                                            {{$package->title}}
                            </span>
                                    </h1>
                                    <p class="text-muted small mb-0 mt-1">
                                        <i class="ph-duotone ph-dot-outline me-1"></i>
                                        قم بتعديل بيانات الباقة
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Main Content -->
            @livewire('admin-panel.pages.package.parts.package-update', ['package' => $package])
        </div>
    </div>

@endsection
