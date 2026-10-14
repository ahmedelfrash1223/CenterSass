@section('title', 'حسابات المدرسين')
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
                                    <li class="breadcrumb-item active">
                                        <i class="ph-duotone ph-user-circle-gear me-1"></i>حسابات المدرسين
                                    </li>
                                </ol>
                            </nav>
                        </div>
                        <div class="col-md-12">
                            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                                <h1 class="page-header-title mb-0">
                                    <i class="ph-duotone ph-user-circle-gear me-2"></i>حسابات المدرسين
                                </h1>
                                @if(auth('teacher')->user()->hasPermission(\App\Enums\Teacher\TeacherPermissionEnum::PAYROLL_GENERATE))
                                    <a href="{{ route('teacher.payrolls.generate') }}" wire:navigate class="btn btn-primary">
                                        <i class="ph-duotone ph-file-plus me-1"></i>توليد كشوف جديدة
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @livewire('teacher-panel.pages.payroll.parts.payroll-data')
        </div>
    </div>
@endsection
