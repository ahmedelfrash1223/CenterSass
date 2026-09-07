@section('title','المديرين')
@section('admin-active','active')
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
                                    <li class="breadcrumb-item"><a wire:navigate href="{{ route('admin.dashboard') }}"
                                                                   class="text-decoration-none"><i
                                                class="ph-duotone ph-house me-1"></i>الرئيسية</a></li>
                                    <li class="breadcrumb-item active"><i class="ph-duotone ph-users me-1"></i>المديرين
                                    </li>
                                </ol>
                            </nav>
                        </div>
                        <div class="col-md-12"><h1 class="page-header-title mb-0"><i
                                    class="ph-duotone ph-users me-2"></i>المديرين</h1>
                            <p class="text-muted small mb-0 mt-1">عرض وإدارة جميع المديرين المسجلين</p></div>
                    </div>
                </div>
            </div>@livewire('admin-panel.pages.admin.parts.admin-data')</div>
    </div>
@endsection
