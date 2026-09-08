@section('title', 'إضافة مجموعة')
@section('groups-active', 'active')
@extends('teacher-panel.master')
@section('main')
<div class="pc-container"><div class="pc-content"><div class="page-header"><div class="page-block"><div class="row align-items-center g-3"><div class="col-md-12"><nav aria-label="breadcrumb"><ol class="breadcrumb mb-2"><li class="breadcrumb-item"><a href="{{ route('teacher.dashboard') }}" wire:navigate><i class="ph-duotone ph-house me-1"></i>الرئيسية</a></li><li class="breadcrumb-item"><a href="{{ route('teacher.groups.index') }}" wire:navigate>المجموعات</a></li><li class="breadcrumb-item active">إضافة مجموعة</li></ol></nav></div><div class="col-md-12"><h1 class="page-header-title mb-0"><i class="ph-duotone ph-plus-circle me-2"></i>إضافة مجموعة جديدة <span class="badge bg-success bg-opacity-10 text-success ms-2 fs-6 fw-normal">جديد</span></h1></div></div></div></div>@livewire('teacher-panel.pages.group.parts.group-create')</div></div>
@endsection
