{{-- Staff side column: my sessions today + my branches --}}
<section class="nb-card nb-stat" aria-label="حصصي النهارده">
    <span class="nb-icon-tile" aria-hidden="true"><i class="ph-duotone ph-calendar-check"></i></span>
    <div class="nb-stat__body">
        <p class="nb-stat__label">حصصي النهارده</p>
        <p class="nb-stat__value">{{ $staffInfo['my_schedules_count'] }}</p>
    </div>
</section>

<section class="nb-card" aria-labelledby="nb-my-branches-title">
    <header class="nb-card__head">
        <h3 class="nb-card__title" id="nb-my-branches-title">فروعي</h3>
        <span class="nb-pill">{{ $staffInfo['my_branches']->count() }}</span>
    </header>

    @if($staffInfo['my_branches']->count() > 0)
        <ul class="nb-home__list">
            @foreach($staffInfo['my_branches'] as $branch)
                <li class="nb-home__list-item" wire:key="branch-{{ $branch->id }}">
                    <span class="nb-icon-tile nb-icon-tile--sm" aria-hidden="true"><i class="ph-duotone ph-map-pin"></i></span>
                    <span class="nb-home__list-text">
                        <strong>{{ $branch->name }}</strong>
                        @if($branch->phone)
                            <small dir="ltr">{{ $branch->phone }}</small>
                        @endif
                    </span>
                </li>
            @endforeach
        </ul>
    @else
        <div class="nb-empty">
            <span class="nb-empty__icon" aria-hidden="true"><i class="ph-duotone ph-map-pin"></i></span>
            <p class="nb-empty__text">لم يتم إضافتك لأي فرع بعد</p>
        </div>
    @endif
</section>
