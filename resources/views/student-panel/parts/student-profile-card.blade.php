<li class="pc-h-item header-user-profile position-relative profile-dropdown-wrapper"
    wire:click.outside="close" wire:keydown.escape="close">
    <button type="button" class="pc-head-link dropdown-toggle arrow-none me-0 border-0 bg-transparent p-0 profile-trigger"
            wire:click="toggle" aria-haspopup="true" aria-controls="student-profile-dropdown"
            aria-expanded="{{ $open ? 'true' : 'false' }}" aria-label="خيارات حساب {{ $this->student->name }}">
        <span class="user-avtar bg-primary text-white d-flex align-items-center justify-content-center fw-bold">
            {{ mb_substr($this->student->name, 0, 1) }}
        </span>
    </button>
    <div class="hover-card">
        <div class="hover-card-header">
            <div class="d-flex align-items-center gap-3">
                <div class="hover-avatar">{{ mb_substr($this->student->name, 0, 1) }}</div>
                <div class="flex-grow-1 min-w-0">
                    <h6 class="mb-1 fw-bold text-truncate">{{ $this->student->name }}</h6>
                    <div class="d-flex align-items-center gap-1 text-muted small text-truncate">
                        <i class="ph-duotone ph-envelope" aria-hidden="true"></i>
                        <span class="text-truncate">{{ $this->student->email }}</span>
                    </div>
                </div>
            </div>
            <div class="mt-3 d-flex align-items-center gap-2 flex-wrap">
                <span class="badge bg-primary bg-opacity-10 text-primary">طالب</span>
                <span class="badge bg-light text-dark">{{ $this->student->code }}</span>
            </div>
        </div>
        <div class="hover-card-hint">
            <i class="ph-duotone ph-cursor-click" aria-hidden="true"></i>
            <span>اضغط لعرض الخيارات</span>
        </div>
    </div>
    @if($open)
        <div id="student-profile-dropdown" class="profile-dropdown show">
            <div class="profile-dropdown-header">
                <div class="d-flex align-items-center gap-3">
                    <div class="profile-avatar">{{ mb_substr($this->student->name, 0, 1) }}</div>
                    <div class="flex-grow-1 min-w-0">
                        <h6 class="mb-1 fw-bold text-truncate">{{ $this->student->name }}</h6>
                        <div class="d-flex align-items-center gap-1 text-muted small text-truncate">
                            <i class="ph-duotone ph-envelope" aria-hidden="true"></i>
                            <span class="text-truncate">{{ $this->student->email }}</span>
                        </div>
                    </div>
                </div>
                <div class="mt-3 d-flex align-items-center gap-2 flex-wrap">
                    <span class="badge bg-primary bg-opacity-10 text-primary">طالب</span>
                    <span class="badge bg-light text-dark">{{ $this->student->code }}</span>
                </div>
            </div>
            <div class="profile-dropdown-body">
                <a href="{{ route('student.profile') }}" wire:navigate class="profile-dropdown-item">
                    <div class="dropdown-item-icon dropdown-item-icon-primary"><i class="ph-duotone ph-user-circle" aria-hidden="true"></i></div>
                    <div class="flex-grow-1">
                        <span class="dropdown-item-title">الملف الشخصي</span>
                        <span class="dropdown-item-subtitle">تعديل بياناتك</span>
                    </div>
                    <i class="ph-duotone ph-caret-left dropdown-item-arrow" aria-hidden="true"></i>
                </a>
                <div class="profile-dropdown-divider"></div>
                <button type="button" wire:click="logout" wire:loading.attr="disabled" wire:target="logout"
                        class="profile-dropdown-item profile-dropdown-item-danger">
                    <div class="dropdown-item-icon dropdown-item-icon-danger"><i class="ph-duotone ph-power" aria-hidden="true"></i></div>
                    <div class="flex-grow-1">
                        <span class="dropdown-item-title">تسجيل الخروج</span>
                        <span class="dropdown-item-subtitle">إنهاء الجلسة الحالية</span>
                    </div>
                    <i class="ph-duotone ph-caret-left dropdown-item-arrow" aria-hidden="true"></i>
                </button>
            </div>
        </div>
    @endif
</li>
