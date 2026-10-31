@php
    $themeModes = [
        'light' => ['label' => 'فاتح', 'icon' => 'ph-sun'],
        'dark' => ['label' => 'داكن', 'icon' => 'ph-moon'],
        'system' => ['label' => 'تلقائي', 'icon' => 'ph-cpu'],
    ];
@endphp

<div class="nb-dropdown" data-nb-dropdown>
    <button
        type="button"
        class="nb-icon-btn"
        aria-label="الإعدادات"
        aria-haspopup="true"
        aria-expanded="false"
        aria-controls="nb-settings-menu"
        data-nb-dropdown-toggle
        wire:ignore.self
    >
        <i class="ph-duotone ph-gear-six" aria-hidden="true"></i>
    </button>

    <div
        class="nb-menu nb-settings"
        id="nb-settings-menu"
        data-nb-dropdown-menu
        hidden
        wire:ignore.self
    >
        <div class="nb-menu__header">
            <p class="nb-menu__title">الإعدادات</p>
            <p class="nb-menu__meta">وضع العرض</p>
        </div>

        <div class="nb-settings__section">
            <p class="nb-settings__label" id="nb-settings-mode-label">وضع المظهر</p>
            <div class="nb-segmented" role="group" aria-labelledby="nb-settings-mode-label">
                @foreach ($themeModes as $mode => $meta)
                    <button
                        type="button"
                        class="nb-segmented__btn {{ $themeMode === $mode ? 'is-active' : '' }}"
                        aria-pressed="{{ $themeMode === $mode ? 'true' : 'false' }}"
                        wire:click="setThemeMode('{{ $mode }}')"
                        @if ($mode === 'system') title="يتم اختيار المظهر تلقائياً حسب إعدادات نظام التشغيل." @endif
                    >
                        <i class="ph-duotone {{ $meta['icon'] }}" aria-hidden="true"></i>
                        {{ $meta['label'] }}
                    </button>
                @endforeach
            </div>
        </div>

        <hr class="nb-menu__divider">

        <button type="button" class="nb-menu__item" wire:click="resetSettings">
            <i class="ph-duotone ph-arrow-counter-clockwise" aria-hidden="true"></i>
            إعادة تعيين الإعدادات
        </button>
    </div>
</div>
