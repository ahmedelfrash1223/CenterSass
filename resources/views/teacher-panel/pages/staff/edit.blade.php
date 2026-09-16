@section('title', 'تعديل عضو')
@section('staff-active', 'active')
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
                                    <li class="breadcrumb-item">
                                        <a href="{{ route('teacher.staff.index') }}" wire:navigate>فريق العمل</a>
                                    </li>
                                    <li class="breadcrumb-item active">تعديل عضو</li>
                                </ol>
                            </nav>
                        </div>
                        <div class="col-md-12">
                            <h1 class="page-header-title mb-0">
                                <i class="ph-duotone ph-pencil-simple me-2"></i>تعديل عضو
                                <span class="badge bg-warning bg-opacity-10 text-warning ms-2 fs-6 fw-normal">
                                    {{ $staff->name }}
                                </span>
                            </h1>
                        </div>
                    </div>
                </div>
            </div>
            @livewire('teacher-panel.pages.staff.parts.staff-update', ['teacher' => $staff])
        </div>
    </div>
@endsection
