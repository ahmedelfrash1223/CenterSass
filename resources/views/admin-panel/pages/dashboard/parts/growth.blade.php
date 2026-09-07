<div class="mt-3 small">
    <span class="text-{{ $growth >= 0 ? 'success' : 'danger' }}">
        <i class="ph-duotone ph-{{ $growth >= 0 ? 'trend-up' : 'trend-down' }} me-1"></i>
        {{ number_format(abs($growth), 1) }}%
        {{ $growth >= 0 ? 'زيادة' : 'انخفاض' }}
    </span>
    <span class="text-muted">مقارنة بالشهر الماضي</span>
</div>