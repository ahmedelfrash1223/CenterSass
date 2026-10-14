@section('title', 'إعدادات الأجر — ' . $teacher->name)
@section('payrolls-active', 'active')
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
                                        <a href="{{ route('teacher.payrolls.index') }}" wire:navigate>حسابات المدرسين</a>
                                    </li>
                                    <li class="breadcrumb-item active">{{ $teacher->name }}</li>
                                </ol>
                            </nav>
                        </div>
                        <div class="col-md-12">
                            <h1 class="page-header-title mb-0">
                                <i class="ph-duotone ph-gear me-2"></i>إعدادات الأجر — {{ $teacher->name }}
                            </h1>
                        </div>
                    </div>
                </div>
            </div>
            @livewire('teacher-panel.pages.payroll.parts.payroll-settings', ['teacher' => $teacher])
        </div>
    </div>
@endsection
