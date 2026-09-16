@section('title', 'عرض المجموعة')
@section('groups-active', 'active')
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
                                    <li class="breadcrumb-item"><a href="{{ route('teacher.dashboard') }}"
                                                                   wire:navigate><i
                                                class="ph-duotone ph-house me-1"></i>الرئيسية</a></li>
                                    <li class="breadcrumb-item"><a href="{{ route('teacher.groups.index') }}"
                                                                   wire:navigate>المجموعات</a></li>
                                    <li class="breadcrumb-item active">عرض</li>
                                </ol>
                            </nav>
                        </div>
                        <div class="col-md-12"><h1 class="page-header-title mb-0"><i
                                    class="ph-duotone ph-users-three me-2"></i>{{ $group->name }}</h1></div>
                    </div>
                </div>
            </div>@livewire('teacher-panel.pages.group.parts.group-show', ['group' => $group])</div>
    </div>
@endsection
