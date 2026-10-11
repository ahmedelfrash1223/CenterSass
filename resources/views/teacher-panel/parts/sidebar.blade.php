@php
    use Illuminate\Support\Facades\Auth;
    $user = Auth::guard('teacher')->user();
@endphp

<nav class="pc-sidebar">
    <div class="navbar-wrapper">
        <div class="m-header">
            <a href="{{ route('teacher.dashboard') }}" class="b-brand text-primary">
                <img style="max-height: 95px;" src="{{ asset('assets/teacherPanel/images/logo.png') }}"
                    alt="logo image" />
            </a>
        </div>
        <div class="navbar-content">
            <ul class="pc-navbar">

                {{-- Home Page --}}
                <li class="pc-item @yield('home-active')">
                    <a wire:navigate href="{{ route('teacher.dashboard') }}" class="pc-link">
                        <span class="pc-micon">
                            <i class="ph-duotone ph-squares-four"></i>
                        </span>
                        <span class="pc-mtext">الصفحة الرئيسية</span>
                    </a>
                </li>

                {{-- Time Table Page --}}
                @if(auth('teacher')->user()->hasPermission(\App\Enums\Teacher\TeacherPermissionEnum::TIMETABLE_VIEW))
                    <li class="pc-item @yield('timetable-active')">
                        <a wire:navigate href="{{ route('teacher.timetable.index') }}" class="pc-link">
                            <span class="pc-micon">
                                <i class="ph-duotone ph-calendar"></i>
                            </span>
                            <span class="pc-mtext">جدول الأسبوع</span>
                        </a>
                    </li>
                @endif



                {{-- Branches Page --}}
                @if(auth('teacher')->user()->hasPermission(\App\Enums\Teacher\TeacherPermissionEnum::BRANCH_VIEW))
                    <li class="pc-item @yield('branches-active')">
                        <a wire:navigate href="{{ route('teacher.branches.index') }}" class="pc-link">
                            <span class="pc-micon">
                                <i class="ph-duotone ph-map-pin-line"></i>
                            </span>
                            <span class="pc-mtext">الأفرع</span>
                        </a>
                    </li>
                @endif

                {{-- Bookings Page --}}
                @if(auth('teacher')->user()->hasPermission(\App\Enums\Teacher\TeacherPermissionEnum::BOOKING_VIEW))
                    <li class="pc-item @yield('bookings-active')">
                        <a wire:navigate href="{{ route('teacher.bookings.index') }}" class="pc-link">
                            <span class="pc-micon">
                                <i class="ph-duotone ph-clipboard-text"></i>
                            </span>
                            <span class="pc-mtext">الحجوزات</span>
                        </a>
                    </li>
                @endif

                {{-- Subjects Page --}}
                @if(auth('teacher')->user()->hasPermission(\App\Enums\Teacher\TeacherPermissionEnum::SUBJECT_VIEW))
                    <li class="pc-item @yield('subjects-active')">
                        <a wire:navigate href="{{ route('teacher.subjects.index') }}" class="pc-link">
                            <span class="pc-micon">
                                <i class="ph-duotone ph-notebook"></i>
                            </span>
                            <span class="pc-mtext">المواد الدراسية</span>
                        </a>
                    </li>
                @endif

                {{-- Staff Page --}}
                @if(auth('teacher')->user()->hasPermission(\App\Enums\Teacher\TeacherPermissionEnum::STAFF_VIEW))
                    <li class="pc-item @yield('staff-active')">
                        <a wire:navigate href="{{ route('teacher.staff.index') }}" class="pc-link">
                            <span class="pc-micon">
                                <i class="ph-duotone ph-users-four"></i>
                            </span>
                            <span class="pc-mtext">فريق العمل</span>
                        </a>
                    </li>
                @endif

                {{-- Groups Page --}}
                @if(auth('teacher')->user()->hasPermission(\App\Enums\Teacher\TeacherPermissionEnum::GROUP_VIEW))
                    <li class="pc-item @yield('groups-active')">
                        <a wire:navigate href="{{ route('teacher.groups.index') }}" class="pc-link">
                            <span class="pc-micon">
                                <i class="ph-duotone ph-user-list"></i>
                            </span>
                            <span class="pc-mtext">المجموعات</span>
                        </a>
                    </li>
                @endif

                {{-- Students Page --}}
                @if(auth('teacher')->user()->hasPermission(\App\Enums\Teacher\TeacherPermissionEnum::STUDENT_VIEW))
                    <li class="pc-item @yield('students-active')">
                        <a wire:navigate href="{{ route('teacher.students.index') }}" class="pc-link">
                            <span class="pc-micon">
                                <i class="ph-duotone ph-student"></i>
                            </span>
                            <span class="pc-mtext">الطلاب</span>
                        </a>
                    </li>
                @endif


                {{-- Attendance Page --}}
                @if(auth('teacher')->user()->hasPermission(\App\Enums\Teacher\TeacherPermissionEnum::ATTENDANCE_VIEW))
                    <li class="pc-item @yield('attendance-active')">
                        <a wire:navigate href="{{ route('teacher.attendance.index') }}" class="pc-link">
                            <span class="pc-micon">
                                <i class="ph-duotone ph-scan"></i>
                            </span>
                            <span class="pc-mtext">الحضور</span>
                        </a>
                    </li>
                @endif


                {{-- Close Day Page --}}
                @if(auth('teacher')->user()->hasPermission(\App\Enums\Teacher\TeacherPermissionEnum::ATTENDANCE_CLOSE_DAY))
                    <li class="pc-item @yield('attendance-close-day-active')">
                        <a wire:navigate href="{{ route('teacher.attendance.close-day.index') }}" class="pc-link">
                            <span class="pc-micon">
                                <i class="ph-duotone ph-lock"></i>
                            </span>
                            <span class="pc-mtext">تقفيل اليوم</span>
                        </a>
                    </li>
                @endif

                {{-- Assignments Page --}}
                @if(auth('teacher')->user()->hasPermission(\App\Enums\Teacher\TeacherPermissionEnum::ASSIGNMENT_VIEW))
                    <li class="pc-item @yield('assignments-active')">
                        <a wire:navigate href="{{ route('teacher.assignments.index') }}" class="pc-link">
                            <span class="pc-micon">
                                <i class="ph-duotone ph-check-square"></i>
                            </span>
                            <span class="pc-mtext">المتابعة</span>
                        </a>
                    </li>
                @endif

                {{-- Payments Page --}}
                @if(auth('teacher')->user()->hasPermission(\App\Enums\Teacher\TeacherPermissionEnum::PAYMENT_VIEW))
                    <li class="pc-item @yield('payments-active')">
                        <a wire:navigate href="{{ route('teacher.payments.index') }}" class="pc-link">
                            <span class="pc-micon">
                                <i class="ph-duotone ph-currency-circle-dollar"></i>
                            </span>
                            <span class="pc-mtext">المدفوعات</span>
                        </a>
                    </li>
                @endif

                {{-- Exams Page --}}
                @if(auth('teacher')->user()->hasPermission(\App\Enums\Teacher\TeacherPermissionEnum::EXAM_VIEW))
                    <li class="pc-item @yield('exams-active')">
                        <a wire:navigate href="{{ route('teacher.exams.index') }}" class="pc-link">
                            <span class="pc-micon">
                                <i class="ph-duotone ph-exam"></i>
                            </span>
                            <span class="pc-mtext">الامتحانات</span>
                        </a>
                    </li>
                @endif

            </ul>
        </div>
        <div class="pc-sidebar-footer">
            <div class="card pc-user-card" dir="rtl">
                <div class="card-body">

                    <div class="d-flex align-items-center">

                        <div class="flex-shrink-0">
                            <div class="teacher-sidebar-avatar">
                                {{ mb_substr(auth('teacher')->user()->name, 0, 1) }}
                            </div>
                        </div>

                        <div class="flex-grow-1 me-3">
                            <div class="dropdown">
                                <div class="d-flex align-items-center">
                                    <div class="flex-grow-1 ms-2">
                                        <h6 class="mb-0 teacher-sidebar-name">
                                            {{ auth('teacher')->user()->name }}
                                        </h6>
                                        <small class="teacher-sidebar-role">
                                            {{ auth('teacher')->user()->email }}
                                        </small>
                                        <div class="mt-1">
                                            <span
                                                class="badge bg-{{ auth('teacher')->user()->role->color() }} bg-opacity-10 text-{{ Auth::guard('teacher')->user()->role->color() }}"
                                                style="font-size: 10px;">
                                                <i
                                                    class="ph-duotone {{ Auth::guard('teacher')->user()->role->icon() }} me-1"></i>
                                                {{ auth('teacher')->user()->role->label() }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</nav>
