@php
    // Bar chart drawn as SVG geometry here; all colors come from CSS tokens
    // (components.css: --nb-chart-*), so it follows light / dark mode by itself.
    $dayNames = [
        'Saturday' => 'السبت', 'Sunday' => 'الأحد', 'Monday' => 'الإثنين', 'Tuesday' => 'الثلاثاء',
        'Wednesday' => 'الأربعاء', 'Thursday' => 'الخميس', 'Friday' => 'الجمعة',
    ];
    $todayName = $dayNames[now()->format('l')] ?? null;

    $maxWeekly = max(array_values($weeklySchedule)) ?: 1;
    $totalWeekly = array_sum($weeklySchedule);

    $chartW = 336;
    $slot = $chartW / max(count($weeklySchedule), 1);
    $barW = 22;
    $top = 24;
    $base = 132;
    $plotH = $base - $top;

    $altText = collect($weeklySchedule)->map(fn ($count, $day) => "{$day}: {$count}")->implode('، ');
@endphp

<section class="nb-card" aria-labelledby="nb-week-title">
    <header class="nb-card__head">
        <div>
            <h3 class="nb-card__title" id="nb-week-title">حصص الأسبوع</h3>
            <p class="nb-card__subtitle">{{ $totalWeekly }} حصة في الأسبوع</p>
        </div>
    </header>

    <figure class="nb-week-chart">
        <svg
            class="nb-week-chart__svg"
            viewBox="0 0 {{ $chartW }} 160"
            role="img"
            aria-labelledby="nb-week-title nb-week-desc"
        >
            <desc id="nb-week-desc">عدد الحصص في كل يوم: {{ $altText }}</desc>

            {{-- grid lines: 0, half, max --}}
            @foreach ([0, 0.5, 1] as $step)
                @php $gy = round($base - $plotH * $step, 1); @endphp
                <line class="nb-week-chart__grid" x1="0" x2="{{ $chartW }}" y1="{{ $gy }}" y2="{{ $gy }}"></line>
            @endforeach

            @foreach ($weeklySchedule as $day => $count)
                @php
                    $i = $loop->index;
                    // RTL: the first day (Saturday) on the right
                    $cx = round($chartW - ($i + 0.5) * $slot, 1);
                    $h = $count > 0 ? max(4, round($plotH * $count / $maxWeekly, 1)) : 2;
                    $isToday = $day === $todayName;
                @endphp
                <g class="nb-week-chart__day {{ $isToday ? 'is-today' : '' }} {{ $count === 0 ? 'is-empty' : '' }}">
                    <rect class="nb-week-chart__bar" x="{{ $cx - $barW / 2 }}" y="{{ $base - $h }}" width="{{ $barW }}" height="{{ $h }}" rx="6"></rect>
                    <text class="nb-week-chart__value" x="{{ $cx }}" y="{{ $base - $h - 6 }}" text-anchor="middle">{{ $count }}</text>
                    <text class="nb-week-chart__label" x="{{ $cx }}" y="152" text-anchor="middle">{{ $day }}</text>
                </g>
            @endforeach
        </svg>
    </figure>
</section>
