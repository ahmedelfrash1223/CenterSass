<div
    class="position-relative profile-dropdown-wrapper"
    wire:click.outside="close"
>
    <li class="pc-h-item header-user-profile">
        <button
            type="button"
            class="pc-head-link dropdown-toggle arrow-none me-0 border-0 bg-transparent p-0 profile-trigger"
            wire:click="toggle"
            aria-haspopup="true"
            aria-expanded="{{ $open ? 'true' : 'false' }}"
        >
            <span class="user-avtar bg-primary text-white d-flex align-items-center justify-content-center fw-bold">
                {{ mb_substr($this->teacher->name, 0, 1) }}
            </span>
        </button>

        {{-- Hover Card: Info Only --}}
        <div class="hover-card">
            <div class="hover-card-header">
                <div class="d-flex align-items-center gap-3">
                    <div class="hover-avatar">
                        {{ mb_substr($this->teacher->name, 0, 1) }}
                    </div>
                    <div class="flex-grow-1 min-w-0">
                        <h6 class="mb-1 fw-bold text-truncate">
                            {{ $this->teacher->name }}
                        </h6>
                        <div class="d-flex align-items-center gap-1 text-muted small text-truncate">
                            <i class="ph-duotone ph-envelope"></i>
                            <span class="text-truncate">{{ $this->teacher->email }}</span>
                        </div>
                    </div>
                </div>

                <div class="mt-3 d-flex align-items-center gap-2 flex-wrap">
                    <span
                        class="badge bg-{{ $this->teacher->role->color() }} bg-opacity-10 text-{{ $this->teacher->role->color() }}">
                        <i class="ph-duotone {{ $this->teacher->role->icon() }} me-1"></i>
                        {{ $this->teacher->role->label() }}
                    </span>
                    @if($this->teacher->code)
                        <span class="badge bg-light text-dark">
                            <i class="ph-duotone ph-identification-card me-1"></i>
                            {{ $this->teacher->code }}
                        </span>
                    @endif
                </div>
            </div>

            <div class="hover-card-hint">
                <i class="ph-duotone ph-cursor-click"></i>
                <span>اضغط لعرض الخيارات</span>
            </div>
        </div>

        {{-- Full Dropdown: Menu (on click) --}}
        @if($open)
            <div class="profile-dropdown show">
                <div class="profile-dropdown-header">
                    <div class="d-flex align-items-center gap-3">
                        <div class="profile-avatar">
                            {{ mb_substr($this->teacher->name, 0, 1) }}
                        </div>
                        <div class="flex-grow-1 min-w-0">
                            <h6 class="mb-1 fw-bold text-truncate">
                                {{ $this->teacher->name }}
                            </h6>
                            <div class="d-flex align-items-center gap-1 text-muted small text-truncate">
                                <i class="ph-duotone ph-envelope"></i>
                                <span class="text-truncate">{{ $this->teacher->email }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="mt-3 d-flex align-items-center gap-2 flex-wrap">
                        <span
                            class="badge bg-{{ $this->teacher->role->color() }} bg-opacity-10 text-{{ $this->teacher->role->color() }}">
                            <i class="ph-duotone {{ $this->teacher->role->icon() }} me-1"></i>
                            {{ $this->teacher->role->label() }}
                        </span>
                        @if($this->teacher->code)
                            <span class="badge bg-light text-dark">
                                <i class="ph-duotone ph-identification-card me-1"></i>
                                {{ $this->teacher->code }}
                            </span>
                        @endif
                    </div>
                </div>

                <div class="profile-dropdown-body">
                    <a href="{{ route('teacher.profile') }}" wire:navigate wire:click="close"
                       class="profile-dropdown-item">
                        <div class="dropdown-item-icon dropdown-item-icon-primary">
                            <i class="ph-duotone ph-user-circle"></i>
                        </div>
                        <div class="flex-grow-1">
                            <span class="dropdown-item-title">الملف الشخصي</span>
                            <span class="dropdown-item-subtitle">تعديل بياناتك</span>
                        </div>
                        <i class="ph-duotone ph-caret-left dropdown-item-arrow"></i>
                    </a>

                    @if(auth('teacher')->user()->isOwner())
                        <a href="{{ route('teacher.profile.subscription') }}" wire:navigate wire:click="close"
                           class="profile-dropdown-item">
                            <div class="dropdown-item-icon dropdown-item-icon-success">
                                <i class="ph-duotone ph-crown"></i>
                            </div>
                            <div class="flex-grow-1">
                                <span class="dropdown-item-title">تفاصيل الاشتراك</span>
                                <span class="dropdown-item-subtitle">إدارة باقتك</span>
                            </div>
                            <i class="ph-duotone ph-caret-left dropdown-item-arrow"></i>
                        </a>
                    @else
                        <div class="profile-dropdown-item profile-dropdown-item-disabled">
                            <div class="dropdown-item-icon dropdown-item-icon-secondary">
                                <i class="ph-duotone ph-crown"></i>
                            </div>
                            <div class="flex-grow-1">
                                <span class="dropdown-item-title">تفاصيل الاشتراك</span>
                                <span class="dropdown-item-subtitle">يديرها المدرس المسئول</span>
                            </div>
                            <span class="badge bg-secondary bg-opacity-10 text-secondary">مقيد</span>
                        </div>
                    @endif

                    <div class="profile-dropdown-divider"></div>

                    <button type="button" wire:click="logout"
                            class="profile-dropdown-item profile-dropdown-item-danger">
                        <div class="dropdown-item-icon dropdown-item-icon-danger">
                            <i class="ph-duotone ph-power"></i>
                        </div>
                        <div class="flex-grow-1">
                            <span class="dropdown-item-title">تسجيل الخروج</span>
                            <span class="dropdown-item-subtitle">إنهاء الجلسة الحالية</span>
                        </div>
                        <i class="ph-duotone ph-caret-left dropdown-item-arrow"></i>
                    </button>
                </div>
            </div>
        @endif
    </li>
</div>
