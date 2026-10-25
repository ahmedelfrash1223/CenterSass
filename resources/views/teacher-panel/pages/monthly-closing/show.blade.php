@section('title', 'تفاصيل تقفيل ' . $closing->reference)
@section('monthly-closing-active', 'active')
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
                                        <a href="{{ route('teacher.monthly-closing.index') }}" wire:navigate>تقفيل الشهر</a>
                                    </li>
                                    <li class="breadcrumb-item active">{{ $closing->reference }}</li>
                                </ol>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>

            @livewire('teacher-panel.pages.monthly-closing.parts.monthly-closing-show', ['closing' => $closing])
        </div>
    </div>
@endsection
