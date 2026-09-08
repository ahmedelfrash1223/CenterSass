<div
    class="position-relative"
    wire:click.outside="close"
>
    <li class="pc-h-item header-user-profile">
        <button
            type="button"
            class="pc-head-link dropdown-toggle arrow-none me-0 border-0 bg-transparent p-0"
            wire:click="toggle"
            aria-haspopup="true"
            aria-expanded="{{ $open ? 'true' : 'false' }}"
        >
            <span class="user-avtar bg-primary text-white d-flex align-items-center justify-content-center fw-bold">
                {{ mb_substr($this->teacher->name, 0, 1) }}
            </span>
        </button>

        @if ($open)
            <div
                class="dropdown-menu dropdown-user-profile dropdown-menu-end pc-h-dropdown show"
                style="display:block;"
            >
                <div class="dropdown-header d-flex align-items-center justify-content-between">
                    <h5 class="m-0">{{ $this->teacher->name }}</h5>
                    <button
                        type="button"
                        class="btn btn-sm btn-link text-muted p-0"
                        wire:click="close"
                    >
                        <i class="ti ti-x fs-5"></i>
                    </button>
                </div>

                <div class="dropdown-body p-2">
                    <a
                        href="{{ route('teacher.profile') }}"
                        wire:navigate
                        class="dropdown-item"
                    >
                        <span class="d-flex align-items-center gap-2">
                            <i class="ph-duotone ph-user-circle"></i>
                            <span>الملف الشخصي</span>
                        </span>
                    </a>

                    <a
                        href="{{ route('teacher.profile.subscription') }}"
                        wire:navigate
                        class="dropdown-item"
                    >
                        <span class="d-flex align-items-center gap-2">
                            <i class="ph-duotone ph-repeat"></i>
                            <span>تفاصيل الاشتراكات</span>
                        </span>
                    </a>

                    <button
                        type="button"
                        wire:click="logout"
                        class="dropdown-item"
                    >
                        <span class="d-flex align-items-center gap-2">
                            <i class="ph-duotone ph-power"></i>
                            <span>تسجيل الخروج</span>
                        </span>
                    </button>
                </div>
            </div>
        @endif
    </li>
</div>

<style>
    .header-user-profile .user-avtar {
        width: 30px !important;
        height: 30px !important;
        min-width: 30px !important;
        min-height: 30px !important;
        border-radius: 50% !important;
        aspect-ratio: 1 / 1;
        padding: 0 !important;
    }
</style>
