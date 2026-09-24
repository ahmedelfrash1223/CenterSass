<div class="card border-0 shadow-sm">
    <div class="card-header bg-transparent border-bottom">
        <h6 class="mb-0 fw-bold">
            <i class="ph-duotone ph-chart-bar me-1"></i>
            حصص الأسبوع
        </h6>
    </div>
    <div class="card-body">
        @php $maxWeekly = max(array_values($weeklySchedule)) ?: 1; @endphp
        @foreach($weeklySchedule as $day => $count)
            <div class="d-flex align-items-center gap-2 mb-2">
                <small class="text-muted" style="min-width: 50px;">{{ $day }}</small>
                <div class="progress flex-grow-1" style="height: 8px;">
                    <div class="progress-bar bg-primary"
                         style="width: {{ ($count / $maxWeekly) * 100 }}%"></div>
                </div>
                <small class="text-muted fw-bold"
                       style="min-width: 25px; text-align: end;">{{ $count }}</small>
            </div>
        @endforeach
    </div>
</div>
