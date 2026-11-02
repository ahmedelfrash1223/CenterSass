{{-- Owner side column: team + subscription --}}
<section class="nb-card" aria-labelledby="nb-team-title">
    <header class="nb-card__head">
        <h3 class="nb-card__title" id="nb-team-title">فريق العمل</h3>
        <a href="{{ route('teacher.staff.index') }}" wire:navigate class="nb-card__link">
            عرض الكل
            <i class="ph-duotone ph-caret-left" aria-hidden="true"></i>
        </a>
    </header>

    <div class="nb-stat">
        <span class="nb-icon-tile" aria-hidden="true"><i class="ph-duotone ph-users-four"></i></span>
        <div class="nb-stat__body">
            <p class="nb-stat__label">أعضاء الفريق</p>
            <p class="nb-stat__value">{{ $ownerStats['staff_count'] }}</p>
            <p class="nb-stat__meta">
                <span class="nb-pill nb-pill--success">نشط: {{ $ownerStats['active_staff_count'] }}</span>
            </p>
        </div>
    </div>
</section>

@if($ownerStats['subscription'])
    @php
        $sub = $ownerStats['subscription'];
        $daysLeft = $sub->ends_at ? (int) now()->diffInDays($sub->ends_at, false) : null;
    @endphp

    <section class="nb-card" aria-labelledby="nb-sub-title">
        <header class="nb-card__head">
            <div class="nb-card__heading">
                <span class="nb-icon-tile nb-icon-tile--sm" aria-hidden="true"><i class="ph-duotone ph-crown"></i></span>
                <h3 class="nb-card__title" id="nb-sub-title">اشتراكك</h3>
            </div>
            <span class="nb-pill nb-pill--success">نشط</span>
        </header>

        <div class="nb-kv">
            <span class="nb-kv__label">الباقة</span>
            <span class="nb-kv__value">{{ $sub->package?->name ?? 'غير محددة' }}</span>
        </div>

        @if($sub->ends_at)
            <div class="nb-kv">
                <span class="nb-kv__label">تاريخ الانتهاء</span>
                <span class="nb-kv__value">{{ $sub->ends_at->format('Y-m-d') }}</span>
            </div>
            <div class="nb-kv">
                <span class="nb-kv__label">المتبقي</span>
                <span class="nb-kv__value">
                    @if($daysLeft > 0)
                        <span class="nb-pill {{ $daysLeft <= 7 ? 'nb-pill--attention' : 'nb-pill--neutral' }}">{{ $daysLeft }} يوم متبقي</span>
                    @elseif($daysLeft === 0)
                        <span class="nb-pill nb-pill--attention">ينتهي اليوم</span>
                    @else
                        <span class="nb-pill nb-pill--danger">انتهى منذ {{ abs($daysLeft) }} يوم</span>
                    @endif
                </span>
            </div>
        @endif

        <a href="{{ route('teacher.profile.subscription') }}" wire:navigate class="nb-btn nb-btn--ghost nb-btn--block nb-home__card-action">
            <i class="ph-duotone ph-crown" aria-hidden="true"></i>
            إدارة الاشتراك
        </a>
    </section>
@endif
