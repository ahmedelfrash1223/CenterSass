@section('title', 'تعديل المادة')
@section('subjects-active', 'active')
@extends('teacher-panel.master')

@section('main')
    <div class="pc-container"><div class="pc-content">
        <!-- Page Header -->
        <div class="page-header"><div class="page-block"><div class="row align-items-center g-3">
            <div class="col-md-12"><nav aria-label="breadcrumb"><ol class="breadcrumb mb-2"><li class="breadcrumb-item"><a href="{{ route('teacher.dashboard') }}" wire:navigate class="text-decoration-none"><i class="ph-duotone ph-house me-1"></i>الرئيسية</a></li><li class="breadcrumb-item"><a href="{{ route('teacher.subjects.index') }}" wire:navigate><i class="ph-duotone ph-books me-1"></i>المواد الدراسية</a></li><li class="breadcrumb-item active" aria-current="page">تعديل المادة</li></ol></nav></div>
            <div class="col-md-12"><div class="d-flex align-items-center gap-3"><div class="page-header-icon-wrapper"><div class="page-header-icon"><i class="ph-duotone ph-pencil-simple fs-1"></i></div></div><div><h1 class="page-header-title mb-0">تعديل المادة <span class="badge bg-warning bg-opacity-10 text-warning ms-2 fs-6 fw-normal"><i class="ph-duotone ph-pencil-simple me-1"></i>{{ $subject->subject }}</span></h1><p class="text-muted small mb-0 mt-1"><i class="ph-duotone ph-dot-outline me-1"></i>قم بتعديل بيانات المادة الدراسية</p></div></div></div>
        </div></div></div>
        @livewire('teacher-panel.pages.subject.parts.subject-update', ['subject' => $subject])
    </div></div>
@endsection
