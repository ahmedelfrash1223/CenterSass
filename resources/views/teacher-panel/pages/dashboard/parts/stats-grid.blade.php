@php
    $kpis = [
        ['icon' => 'ph-student',     'label' => 'إجمالي الطلاب',     'value' => $stats['students_count']],
        ['icon' => 'ph-users-three', 'label' => 'إجمالي المجموعات', 'value' => $stats['groups_count']],
        ['icon' => 'ph-map-pin-line', 'label' => $isOwner ? 'إجمالي الفروع' : 'الفروع المتاحة لك', 'value' => $stats['branches_count']],
        ['icon' => 'ph-notebook',    'label' => 'إجمالي المواد',     'value' => $stats['subjects_count']],
    ];
@endphp

<section class="nb-home__kpis" aria-label="أرقام سريعة">
    @foreach ($kpis as $kpi)
        <div class="nb-card nb-stat">
            <span class="nb-icon-tile" aria-hidden="true">
                <i class="ph-duotone {{ $kpi['icon'] }}"></i>
            </span>
            <div class="nb-stat__body">
                <p class="nb-stat__label">{{ $kpi['label'] }}</p>
                <p class="nb-stat__value">{{ number_format($kpi['value']) }}</p>
            </div>
        </div>
    @endforeach
</section>
