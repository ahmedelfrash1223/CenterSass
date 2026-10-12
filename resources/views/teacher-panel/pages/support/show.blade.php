@section('title', 'تذكرة ' . $ticket->reference)
@section('support-active', 'active')
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
                                        <a href="{{ route('teacher.support.index') }}" wire:navigate>الدعم الفني</a>
                                    </li>
                                    <li class="breadcrumb-item active">{{ $ticket->reference }}</li>
                                </ol>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
            @livewire('teacher-panel.pages.support.parts.support-ticket-show', ['ticket' => $ticket])
        </div>
    </div>
@endsection
