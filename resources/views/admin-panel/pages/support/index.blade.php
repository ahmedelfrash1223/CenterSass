@section('title', 'الدعم الفني')
@section('support-active', 'active')
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
                                    <li class="breadcrumb-item active">
                                        <i class="ph-duotone ph-lifebuoy me-1"></i>الدعم الفني
                                    </li>
                                </ol>
                            </nav>
                        </div>
                        <div class="col-md-12">
                            <h1 class="page-header-title mb-0">
                                <i class="ph-duotone ph-lifebuoy me-2"></i>الدعم الفني
                            </h1>
                        </div>
                    </div>
                </div>
            </div>
            @livewire('admin-panel.pages.support.parts.support-ticket-data')
        </div>
    </div>
@endsection
