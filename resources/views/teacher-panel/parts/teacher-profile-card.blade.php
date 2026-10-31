@php
    $teacher = $this->teacher;
    $nameParts = preg_split('/\s+/u', trim($teacher->name ?? ''), -1, PREG_SPLIT_NO_EMPTY);
    $initials = collect($nameParts)->take(2)->map(fn ($part) => mb_substr($part, 0, 1))->implode('');
@endphp

<div class="nb-dropdown nb-profile" data-nb-dropdown>
    <button
        type="button"
        class="nb-profile__trigger"
        aria-haspopup="true"
        aria-expanded="false"
        aria-controls="nb-profile-menu"
        aria-label="حساب {{ $teacher->name }}"
        data-nb-dropdown-toggle
        wire:ignore.self
    >
        <span class="nb-avatar" aria-hidden="true">{{ $initials }}</span>
        <span class="nb-profile__name">{{ $teacher->name }}</span>
        <svg class="nb-profile__chevron" viewBox="0 0 24 24" fill="none" aria-hidden="true" focusable="false">
            <path d="m6 9 6 6 6-6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
    </button>

    <div
        class="nb-menu nb-profile__menu"
        id="nb-profile-menu"
        data-nb-dropdown-menu
        hidden
        wire:ignore.self
    >
        <div class="nb-menu__header nb-profile__header">
            <span class="nb-avatar nb-avatar--lg" aria-hidden="true">{{ $initials }}</span>
            <div class="nb-profile__info">
                <p class="nb-menu__title">{{ $teacher->name }}</p>
                @if ($teacher->email)
                    <p class="nb-menu__meta nb-profile__email">{{ $teacher->email }}</p>
                @endif
                <div class="nb-profile__tags">
                    <span class="nb-badge">{{ $teacher->role->label() }}</span>
                    @if ($teacher->code)
                        <span class="nb-badge nb-badge--neutral">{{ $teacher->code }}</span>
                    @endif
                </div>
            </div>
        </div>

        <hr class="nb-menu__divider">

        <a href="{{ route('teacher.profile') }}" wire:navigate class="nb-menu__item">
            <i class="ph-duotone ph-user-circle" aria-hidden="true"></i>
            الملف الشخصي
        </a>

        @if (auth('teacher')->user()->isOwner())
            <a href="{{ route('teacher.profile.subscription') }}" wire:navigate class="nb-menu__item">
                <i class="ph-duotone ph-crown" aria-hidden="true"></i>
                تفاصيل الاشتراك
            </a>
        @else
            <div class="nb-menu__item nb-menu__item--disabled" aria-disabled="true">
                <i class="ph-duotone ph-crown" aria-hidden="true"></i>
                تفاصيل الاشتراك
                <span class="nb-badge nb-badge--neutral nb-menu__item-end">يديرها المدرس المسئول</span>
            </div>
        @endif

        <hr class="nb-menu__divider">

        <button type="button" wire:click="logout" class="nb-menu__item nb-menu__item--danger">
            <i class="ph-duotone ph-power" aria-hidden="true"></i>
            تسجيل الخروج
        </button>
    </div>
</div>
