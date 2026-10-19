@section('title', 'مراجعة الكشوفات')
@section('payrolls-active', 'active')
@extends('teacher-panel.master')
@section('main')
    <div class="pc-container">
        <div class="pc-content">
            <div class="page-header">
                <div class="page-block">
                    <h1 class="page-header-title">
                        <i class="ph-duotone ph-clipboard-check me-2"></i>مراجعة الكشوفات
                    </h1>
                </div>
            </div>
            @livewire('teacher-panel.pages.payroll.parts.payroll-review')
        </div>
    </div>
@endsection
