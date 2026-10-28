<nav class="pc-sidebar">
    <div class="navbar-wrapper">
        <div class="m-header">
            <a wire:navigate
               href="{{ route('admin.dashboard') }}"
               class="b-brand text-primary"
            >
                <img
                    style="max-height: 85px;"
                    src="{{ asset('assets/brand.svg') }}"
                    alt="نبذة"
                />
            </a>
        </div>
        <div class="navbar-content">
            <ul class="pc-navbar">

                <li class="pc-item @yield('home-active')">
                    <a
                        wire:navigate
                        href="{{ route('admin.dashboard') }}"
                        class="pc-link"
                    >
                        <span class="pc-micon">
                            <i class="ph-duotone ph-gauge"></i>
                        </span>
                        <span class="pc-mtext">الصفحة الرئيسية</span>
                    </a>
                </li>

                <li class="pc-item @yield('admin-active')">
                    <a
                        wire:navigate
                        href="{{ route('admin.admins.index') }}"
                        class="pc-link"
                    >
                        <span class="pc-micon">
                            <i class="ph-duotone ph-users"></i>
                        </span>
                        <span class="pc-mtext">المديرين</span>
                    </a>
                </li>

                <li class="pc-item @yield('package-active')">
                    <a
                        wire:navigate
                        href="{{ route('admin.package.index') }}"
                        class="pc-link"
                    >
                        <span class="pc-micon">
                            <i class="ph-duotone ph-package"></i>
                        </span>
                        <span class="pc-mtext">الباقات</span>
                    </a>
                </li>

                <li class="pc-item @yield('teacher-active')">
                    <a
                        wire:navigate
                        href="{{ route('admin.teacher.index') }}"
                        class="pc-link"
                    >
                        <span class="pc-micon">
                            <i class="ph-duotone ph-folder-user"></i>
                        </span>
                        <span class="pc-mtext">المدرسين</span>
                    </a>
                </li>

                <li class="pc-item @yield('subscription-active')">
                    <a
                        wire:navigate
                        href="{{ route('admin.subscriptions.index') }}"
                        class="pc-link"
                    >
                        <span class="pc-micon">
                            <i class="ph-duotone ph-repeat"></i>
                        </span>
                        <span class="pc-mtext">الاشتراكات</span>
                    </a>
                </li>

                <li class="pc-item @yield('support-active')">
                    <a
                        wire:navigate
                        href="{{ route('admin.support.index') }}"
                        class="pc-link"
                    >
                        <span class="pc-micon">
                            <i class="ph-duotone ph-lifebuoy"></i>
                        </span>
                        <span class="pc-mtext">الدعم الفني</span>
                    </a>
                </li>

                <li class="pc-item @yield('card-requests-active')">
                    <a wire:navigate href="{{ route('admin.card-requests.index') }}" class="pc-link">
                        <span class="pc-micon">
                            <i class="ph-duotone ph-identification-card"></i>
                        </span>
                        <span class="pc-mtext">طلبات الكروت</span>
                    </a>
                </li>

                <li class="pc-item @yield('subscription-request-active')">
                    <a
                        wire:navigate
                        href="{{ route('admin.subscription-request') }}"
                        class="pc-link position-relative"
                    >
                        <span class="pc-micon">
                            <i class="ph-duotone ph-target"></i>
                        </span>
                        <span class="pc-mtext">طلبات الاشتراك</span>

                        @php
                            $pendingCount = \App\Models\SubscriptionRequest::where('status', \App\Enums\Admin\SubscriptionRequestStatusEnum::PENDING->value)->count();
                        @endphp

                        @if($pendingCount > 0)
                            <span
                                class="pc-badge"
                                style="font-size: 0.65rem; margin-top: 2px; margin-left: -10px;">
                                {{ $pendingCount }}
                                <span class="visually-hidden">طلبات قيد الانتظار</span>
                            </span>
                        @endif
                    </a>
                </li>

            </ul>
        </div>
        <div class="pc-sidebar-footer">
            <div
                class="card pc-user-card"
                dir="rtl"
            >
                <div class="card-body">

                    <div class="d-flex align-items-center">

                        <div class="flex-shrink-0">
                            <div class="admin-sidebar-avatar">
                                {{ mb_substr(auth('admin')->user()->name, 0, 1) }}
                            </div>
                        </div>

                        <div class="flex-grow-1 me-3">
                            <div class="dropdown">
                                <div class="d-flex align-items-center">
                                    <div class="flex-grow-1 ms-2">
                                        <h6 class="mb-0 admin-sidebar-name">
                                            {{ auth('admin')->user()->name }}
                                        </h6>
                                        <small class="admin-sidebar-role">
                                            مدير النظام
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <style>
                    .admin-sidebar-avatar {
                        width: 45px;
                        height: 45px;
                        border-radius: 13px;
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        background: linear-gradient(135deg, #4680ff, #6f9cff);
                        color: #fff;
                        font-size: 19px;
                        font-weight: 700;
                        box-shadow: 0 5px 15px rgba(70, 128, 255, .2);
                    }

                    .admin-sidebar-name {
                        font-weight: 700;
                    }

                    .admin-sidebar-role {
                        color: #8c8c8c;
                        font-size: 11px;
                    }

                    .pc-user-links {
                        cursor: pointer;
                    }
                </style>
            </div>
        </div>
    </div>
</nav>
