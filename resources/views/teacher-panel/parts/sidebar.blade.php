@php
    use App\Enums\Teacher\TeacherPermissionEnum as Perm;
    use App\Models\SupportTicketMessage;
    use Illuminate\Support\Facades\Auth;

    $user = Auth::guard('teacher')->user();

    $unreadSupport = $user
        ? SupportTicketMessage::query()
            ->whereNull('read_at')
            ->where('sender_type', 'admin')
            ->whereHas('ticket', fn($q) => $q->where('teacher_id', $user->getOwnerId()))
            ->count()
        : 0;

    $can = fn ($permission) => $user && $user->hasPermission($permission);

    /*
     * Menu definition: the same items, labels, routes, permissions and @yield('*-active')
     * sections as before, grouped into collapsible sections. Inside each group the items
     * keep their original relative order. A group with 'label' => null has no header.
     * An item may also define 'children' => [...] (same shape) to render as an accordion.
     */
    $menuGroups = [
        [
            'id' => 'home',
            'label' => null,
            'items' => [
                ['label' => 'الصفحة الرئيسية', 'route' => 'teacher.dashboard', 'icon' => 'ph-squares-four', 'active' => 'home-active', 'visible' => true],
            ],
        ],
        [
            'id' => 'students',
            'label' => 'الطلاب والمجموعات',
            'items' => [
                ['label' => 'الحجوزات', 'route' => 'teacher.bookings.index', 'icon' => 'ph-clipboard-text', 'active' => 'bookings-active', 'visible' => $can(Perm::BOOKING_VIEW)],
                ['label' => 'المواد الدراسية', 'route' => 'teacher.subjects.index', 'icon' => 'ph-notebook', 'active' => 'subjects-active', 'visible' => $can(Perm::SUBJECT_VIEW)],
                ['label' => 'المجموعات', 'route' => 'teacher.groups.index', 'icon' => 'ph-user-list', 'active' => 'groups-active', 'visible' => $can(Perm::GROUP_VIEW)],
                ['label' => 'الطلاب', 'route' => 'teacher.students.index', 'icon' => 'ph-student', 'active' => 'students-active', 'visible' => $can(Perm::STUDENT_VIEW)],
            ],
        ],
        [
            'id' => 'attendance',
            'label' => 'الحضور والجدول',
            'items' => [
                ['label' => 'جدول الأسبوع', 'route' => 'teacher.timetable.index', 'icon' => 'ph-calendar', 'active' => 'timetable-active', 'visible' => $can(Perm::TIMETABLE_VIEW)],
                ['label' => 'الحضور', 'route' => 'teacher.attendance.index', 'icon' => 'ph-scan', 'active' => 'attendance-active', 'visible' => $can(Perm::ATTENDANCE_VIEW)],
                ['label' => 'تقفيل اليوم', 'route' => 'teacher.attendance.close-day.index', 'icon' => 'ph-lock', 'active' => 'attendance-close-day-active', 'visible' => $can(Perm::ATTENDANCE_CLOSE_DAY)],
            ],
        ],
        [
            'id' => 'finance',
            'label' => 'المالية والمصاريف',
            'items' => [
                ['label' => 'المدفوعات', 'route' => 'teacher.payments.index', 'icon' => 'ph-currency-circle-dollar', 'active' => 'payments-active', 'visible' => $can(Perm::PAYMENT_VIEW)],
                ['label' => 'الإيرادات', 'route' => 'teacher.revenues.index', 'icon' => 'ph-chart-line-up', 'active' => 'revenues-active', 'visible' => $can(Perm::REVENUE_VIEW)],
                ['label' => 'حسابات المدرسين', 'route' => 'teacher.payrolls.index', 'icon' => 'ph-user-circle-gear', 'active' => 'payrolls-active', 'visible' => $can(Perm::PAYROLL_VIEW)],
                ['label' => 'المصروفات', 'route' => 'teacher.expenses.index', 'icon' => 'ph-arrow-circle-down', 'active' => 'expenses-active', 'visible' => $can(Perm::EXPENSE_VIEW)],
                ['label' => 'تقفيل الشهر', 'route' => 'teacher.monthly-closing.index', 'icon' => 'ph-lock', 'active' => 'monthly-closing-active', 'visible' => $can(Perm::REVENUE_VIEW)],
            ],
        ],
        [
            'id' => 'exams',
            'label' => 'الامتحانات والمتابعة',
            'items' => [
                ['label' => 'المتابعة', 'route' => 'teacher.assignments.index', 'icon' => 'ph-check-square', 'active' => 'assignments-active', 'visible' => $can(Perm::ASSIGNMENT_VIEW)],
                ['label' => 'الامتحانات', 'route' => 'teacher.exams.index', 'icon' => 'ph-exam', 'active' => 'exams-active', 'visible' => $can(Perm::EXAM_VIEW)],
            ],
        ],
        [
            'id' => 'services',
            'label' => 'الخدمات والدعم',
            'items' => [
                ['label' => 'طلبات الكروت', 'route' => 'teacher.card-requests.index', 'icon' => 'ph-identification-card', 'active' => 'card-requests-active', 'visible' => $can(Perm::CARD_REQUEST_VIEW)],
                ['label' => 'الدعم الفني', 'route' => 'teacher.support.index', 'icon' => 'ph-lifebuoy', 'active' => 'support-active', 'visible' => $can(Perm::SUPPORT_VIEW), 'badge' => $unreadSupport],
            ],
        ],
        [
            'id' => 'admin',
            'label' => 'الإدارة',
            'items' => [
                ['label' => 'الأفرع', 'route' => 'teacher.branches.index', 'icon' => 'ph-map-pin-line', 'active' => 'branches-active', 'visible' => $can(Perm::BRANCH_VIEW)],
                ['label' => 'فريق العمل', 'route' => 'teacher.staff.index', 'icon' => 'ph-users-four', 'active' => 'staff-active', 'visible' => $can(Perm::STAFF_VIEW)],
            ],
        ],
    ];

    $isActive = fn (array $item) => trim($__env->yieldContent($item['active'] ?? '')) !== '';

    $itemIsActive = function (array $item) use ($isActive) {
        if ($isActive($item)) {
            return true;
        }
        foreach ($item['children'] ?? [] as $child) {
            if ($isActive($child)) {
                return true;
            }
        }
        return false;
    };

    $teacherName = $user?->name ?? '';
    $nameParts = preg_split('/\s+/u', trim($teacherName), -1, PREG_SPLIT_NO_EMPTY);
    $initials = collect($nameParts)->take(2)->map(fn ($part) => mb_substr($part, 0, 1))->implode('');
@endphp

<aside class="nb-sidebar" id="nb-sidebar" data-nb-sidebar aria-label="الشريط الجانبي">

    <!-- Brand -->
    <div class="nb-sidebar__brand">
        <a wire:navigate href="{{ route('teacher.dashboard') }}" class="nb-sidebar__brand-link" aria-label="نبذة - الصفحة الرئيسية">
            <svg class="nb-sidebar__mark" viewBox="0 0 64 64" fill="none" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">
                <path d="M32 18c-6-5-15-6-24-4v34c9-2 18-1 24 4 6-5 15-6 24-4V14c-9-2-18-1-24 4z" stroke="#FFFFFF" stroke-width="3.2"/>
                <path d="M32 18v34" stroke="#FFFFFF" stroke-width="3.2"/>
                <path d="M14 26c4-1 8-1 12 1M14 33c4-1 8-1 12 1" stroke="#A4B388" stroke-width="3"/>
                <path d="M37 32l5 5 9-11" stroke="#A4B388" stroke-width="4"/>
            </svg>
            <span class="nb-sidebar__wordmark" aria-hidden="true">نبذة</span>
        </a>

        <button type="button" class="nb-icon-btn nb-sidebar__close" data-nb-drawer-close aria-label="إغلاق القائمة">
            <svg viewBox="0 0 24 24" fill="none" aria-hidden="true" focusable="false">
                <path d="M6 6l12 12M18 6 6 18" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
            </svg>
        </button>
    </div>

    <!-- Navigation -->
    <nav class="nb-nav" aria-label="القائمة الرئيسية" data-nb-nav>
        @foreach ($menuGroups as $groupIndex => $group)
            @php
                $visibleItems = array_values(array_filter($group['items'], fn ($item) => $item['visible']));
                $groupHasActive = collect($visibleItems)->contains(fn ($item) => $itemIsActive($item));
                $groupPanelId = 'nb-nav-group-' . $group['id'];
            @endphp

            @if (count($visibleItems))
                <div
                    class="nb-nav__group {{ $group['label'] ? 'nb-nav__group--collapsible' : '' }}"
                    data-nb-group="{{ $group['id'] }}"
                    @if ($groupHasActive) data-nb-group-active @endif
                >
                    @if ($group['label'])
                        <button
                            type="button"
                            class="nb-nav__group-toggle"
                            aria-expanded="true"
                            aria-controls="{{ $groupPanelId }}"
                            data-nb-group-toggle
                        >
                            <span class="nb-nav__group-label">{{ $group['label'] }}</span>
                            <svg class="nb-nav__group-chevron" viewBox="0 0 24 24" fill="none" aria-hidden="true" focusable="false">
                                <path d="m6 9 6 6 6-6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </button>
                    @endif

                    <div class="nb-nav__group-panel" id="{{ $groupPanelId }}" data-nb-group-panel>
                        <ul class="nb-nav__list">
                            @foreach ($visibleItems as $itemIndex => $item)
                                @php
                                    $children = array_values(array_filter($item['children'] ?? [], fn ($child) => $child['visible'] ?? true));
                                @endphp

                                @if (count($children))
                                    @php
                                        $hasActiveChild = collect($children)->contains(fn ($child) => $isActive($child));
                                        $submenuId = 'nb-submenu-' . $group['id'] . '-' . $itemIndex;
                                    @endphp
                                    <li class="nb-nav__item nb-nav__item--parent {{ $hasActiveChild ? 'is-open is-active-parent' : '' }}" data-nb-accordion>
                                        <button
                                            type="button"
                                            class="nb-nav__link"
                                            aria-expanded="{{ $hasActiveChild ? 'true' : 'false' }}"
                                            aria-controls="{{ $submenuId }}"
                                            data-nb-accordion-toggle
                                            data-nb-tooltip="{{ $item['label'] }}"
                                        >
                                            <i class="nb-nav__icon ph-duotone {{ $item['icon'] }}" aria-hidden="true"></i>
                                            <span class="nb-nav__text">{{ $item['label'] }}</span>
                                            <svg class="nb-nav__chevron" viewBox="0 0 24 24" fill="none" aria-hidden="true" focusable="false">
                                                <path d="m15 6-6 6 6 6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                                            </svg>
                                        </button>

                                        <div class="nb-nav__submenu" id="{{ $submenuId }}" data-nb-accordion-panel @unless ($hasActiveChild) hidden @endunless>
                                            <ul class="nb-nav__sublist">
                                                @foreach ($children as $child)
                                                    @php $childActive = $isActive($child); @endphp
                                                    <li>
                                                        <a
                                                            wire:navigate
                                                            href="{{ route($child['route']) }}"
                                                            class="nb-nav__sublink {{ $childActive ? 'is-active' : '' }}"
                                                            @if ($childActive) aria-current="page" @endif
                                                        >
                                                            <span class="nb-nav__dot" aria-hidden="true"></span>
                                                            <span class="nb-nav__text">{{ $child['label'] }}</span>
                                                        </a>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    </li>
                                @else
                                    @php $active = $isActive($item); @endphp
                                    <li class="nb-nav__item">
                                        <a
                                            wire:navigate
                                            href="{{ route($item['route']) }}"
                                            class="nb-nav__link {{ $active ? 'is-active' : '' }}"
                                            data-nb-tooltip="{{ $item['label'] }}"
                                            @if ($active) aria-current="page" @endif
                                        >
                                            <i class="nb-nav__icon ph-duotone {{ $item['icon'] }}" aria-hidden="true"></i>
                                            <span class="nb-nav__text">{{ $item['label'] }}</span>

                                            @if (! empty($item['badge']))
                                                <span class="nb-nav__badge" aria-label="{{ $item['badge'] }} رسالة جديدة">{{ $item['badge'] }}</span>
                                            @endif
                                        </a>
                                    </li>
                                @endif
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif
        @endforeach
    </nav>

    <!-- Teacher card -->
    @if ($user)
        <div class="nb-sidebar__footer">
            <div class="nb-user-card">
                <span class="nb-user-card__avatar" aria-hidden="true">{{ $initials }}</span>

                <div class="nb-user-card__text">
                    <p class="nb-user-card__name">{{ $user->name }}</p>
                    <p class="nb-user-card__meta">{{ $user->role?->label() }}</p>
                </div>

                <button
                    type="button"
                    class="nb-user-card__logout"
                    aria-label="تسجيل الخروج"
                    data-nb-tooltip="تسجيل الخروج"
                    data-nb-tooltip-always
                    data-nb-logout
                >
                    <svg viewBox="0 0 24 24" fill="none" aria-hidden="true" focusable="false">
                        <path d="M14 4.75h3.25a2 2 0 0 1 2 2v10.5a2 2 0 0 1-2 2H14" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                        <path d="M10.5 8 6.5 12l4 4M7 12h9" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </button>
            </div>
        </div>
    @endif
</aside>

<div class="nb-sidebar-overlay" data-nb-drawer-overlay hidden></div>

<!-- Restores each group's open/closed state right after the markup (before first paint) -->
<script src="{{ asset('assets/teacherPanel/js/layout/sidebar-groups-init.js') }}"></script>
