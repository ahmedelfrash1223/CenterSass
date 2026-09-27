@section('title', 'تعديل الامتحان')
@section('exams-active', 'active')
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
                                        <a href="{{ route('teacher.exams.index') }}" wire:navigate>الامتحانات</a>
                                    </li>
                                    <li class="breadcrumb-item active">تعديل الامتحان</li>
                                </ol>
                            </nav>
                        </div>
                        <div class="col-md-12">
                            <h1 class="page-header-title mb-0">
                                <i class="ph-duotone ph-pencil-simple me-2"></i>تعديل الامتحان
                                <span class="badge bg-warning bg-opacity-10 text-warning ms-2 fs-6 fw-normal">{{ $exam->title }}</span>
                            </h1>
                        </div>
                    </div>
                </div>
            </div>

            @livewire('teacher-panel.pages.exam.parts.exam-update', ['exam' => $exam])
        </div>
    </div>
@endsection
