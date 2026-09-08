<nav class="pc-sidebar">
    <div class="navbar-wrapper">
        <div class="m-header">
            <a
                href="{{ route('teacher.dashboard') }}"
                class="b-brand text-primary"
            >
                <img
                    style="max-height: 95px;"
                    src="{{ asset('assets/teacherPanel/images/logo.png') }}"
                    alt="logo image"
                />
            </a>
        </div>
        <div class="navbar-content">
            <ul class="pc-navbar">

                <li class="pc-item @yield('home-active')">
                    <a
                        wire:navigate
                        href="{{ route('teacher.dashboard') }}"
                        class="pc-link"
                    >
                        <span class="pc-micon">
                            <i class="ph-duotone ph-gauge"></i>
                        </span>
                        <span class="pc-mtext">الصفحة الرئيسية</span>
                    </a>
                </li>

                <li class="pc-item @yield('branches-active')">
                    <a
                        wire:navigate
                        href="{{ route('teacher.branches.index') }}"
                        class="pc-link"
                    >
                        <span class="pc-micon">
                            <i class="ph-duotone ph-buildings"></i>
                        </span>
                        <span class="pc-mtext">الأفرع</span>
                    </a>
                </li>

                <li class="pc-item @yield('subjects-active')">
                    <a
                        wire:navigate
                        href="{{ route('teacher.subjects.index') }}"
                        class="pc-link"
                    >
                        <span class="pc-micon">
                            <i class="ph-duotone ph-books"></i>
                        </span>
                        <span class="pc-mtext">المواد الدراسية</span>
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
                            <div class="teacher-sidebar-avatar">
                                {{ mb_substr(Auth::guard('teacher')->user()->name, 0, 1) }}
                            </div>
                        </div>

                        <div class="flex-grow-1 me-3">
                            <div class="dropdown">
                                <div class="d-flex align-items-center">
                                    <div class="flex-grow-1 ms-2">
                                        <h6 class="mb-0 teacher-sidebar-name">
                                            {{ Auth::guard('teacher')->user()->name }}
                                        </h6>
                                        <small class="teacher-sidebar-role">
                                            {{Auth::guard('teacher')->user()->email}}
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <style>
                    .teacher-sidebar-avatar {
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

                    .teacher-sidebar-name {
                        font-weight: 700;
                    }

                    .teacher-sidebar-role {
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
