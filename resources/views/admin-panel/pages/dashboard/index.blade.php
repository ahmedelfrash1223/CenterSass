@section('home-active','active')
@section('title','لوحة التحكم')
@extends('admin-panel.master')

@section('main')

    <style>
        .dashboard-card {
            transition: all .3s ease;
        }

        .dashboard-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 30px rgba(0, 0, 0, .08);
        }

        .dashboard-icon {
            width: 52px;
            height: 52px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 14px;
            font-size: 27px;
            flex-shrink: 0;
        }

        .icon-primary {
            background: rgba(70, 128, 255, .12);
            color: #4680ff;
        }

        .icon-success {
            background: rgba(46, 184, 92, .12);
            color: #2eb85c;
        }

        .icon-warning {
            background: rgba(255, 193, 7, .14);
            color: #ffb900;
        }

        .icon-danger {
            background: rgba(220, 53, 69, .12);
            color: #dc3545;
        }

        .icon-info {
            background: rgba(23, 162, 184, .12);
            color: #17a2b8;
        }

        .dashboard-stat {
            position: relative;
            overflow: hidden;
        }

        .dashboard-stat::after {
            content: '';
            position: absolute;
            width: 120px;
            height: 120px;
            border-radius: 50%;
            background: rgba(255, 255, 255, .08);
            left: -35px;
            bottom: -55px;
            transition: all .4s ease;
        }

        .dashboard-stat:hover::after {
            transform: scale(1.4);
        }

        .welcome-card {
            background: linear-gradient(135deg, #4680ff 0%, #6f9cff 100%);
            color: #fff;
            overflow: hidden;
            position: relative;
        }

        .welcome-card::before {
            content: '';
            position: absolute;
            width: 220px;
            height: 220px;
            border-radius: 50%;
            background: rgba(255, 255, 255, .08);
            left: -70px;
            top: -90px;
        }

        .welcome-card::after {
            content: '';
            position: absolute;
            width: 160px;
            height: 160px;
            border-radius: 50%;
            background: rgba(255, 255, 255, .08);
            right: 8%;
            bottom: -100px;
        }

        .welcome-content {
            position: relative;
            z-index: 2;
        }

        .quick-action {
            transition: all .25s ease;
            cursor: pointer;
        }

        .quick-action:hover {
            transform: translateY(-4px);
        }

        .quick-action .dashboard-icon {
            transition: transform .3s ease;
        }

        .quick-action:hover .dashboard-icon {
            transform: scale(1.08) rotate(-4deg);
        }

        .activity-item {
            position: relative;
            padding-right: 45px;
        }

        .activity-icon {
            position: absolute;
            right: 0;
            top: 0;
            width: 34px;
            height: 34px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .student-avatar {
            width: 42px;
            height: 42px;
            object-fit: cover;
            border-radius: 12px;
        }

        .progress {
            border-radius: 20px;
        }

        .progress-bar {
            border-radius: 20px;
        }

        .dashboard-title {
            font-weight: 700;
        }

        .stat-number {
            font-size: 26px;
            font-weight: 700;
        }

        .stat-label {
            font-size: 13px;
        }
    </style>

    <div class="pc-container">
        <div class="pc-content">

            <!-- Breadcrumb -->
            <div class="page-header">
                <div class="page-block">
                    <div class="row align-items-center g-3">
                        <!-- Breadcrumb -->
                        <div class="col-md-12">
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb mb-2">
                                    <li class="breadcrumb-item">
                                        <a href="{{ route('admin.dashboard') }}" class="text-decoration-none">
                                            <i class="ph-duotone ph-house me-1"></i>
                                            الرئيسية
                                        </a>
                                    </li>
                                    <li class="breadcrumb-item active" aria-current="page">
                                        <i class="ph-duotone ph-gauge me-1"></i>
                                        لوحة التحكم
                                    </li>
                                </ol>
                            </nav>
                        </div>

                        <!-- Title with Icon and Stats -->
                        <div class="col-md-12">
                            <div class="d-flex align-items-center gap-3 flex-wrap">
                                <div class="page-header-icon-wrapper">
                                    <div class="page-header-icon">
                                        <i class="ph-duotone ph-gauge fs-1"></i>
                                    </div>
                                </div>

                                <div class="flex-grow-1">
                                    <h1 class="page-header-title mb-1">
                                        لوحة التحكم
                                        <span class="badge bg-primary bg-opacity-10 text-primary ms-2 fs-6 fw-normal">
                                <i class="ph-duotone ph-check-circle me-1"></i>
                                مرحباً بعودتك
                            </span>
                                    </h1>
                                    <p class="text-muted mb-0">
                                        <i class="ph-duotone ph-calendar-blank me-1"></i>
                                        {{ now()->format('l، d F Y') }}
                                        <span class="mx-2">|</span>
                                        <i class="ph-duotone ph-clock me-1"></i>
                                        {{ now()->format('h:i A') }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Main Content -->
            @livewire('admin-panel.pages.dashboard.parts.data')
        </div>
    </div>

@endsection
