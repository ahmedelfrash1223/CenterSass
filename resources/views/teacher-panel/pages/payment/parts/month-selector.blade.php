<div class="card border-0 shadow-sm mb-4">
    <div class="card-body d-flex align-items-center justify-content-between flex-wrap gap-3">
        <div class="d-flex align-items-center gap-2">
            <div class="rounded-3 d-flex align-items-center justify-content-center bg-primary-subtle text-primary"
                 style="width: 44px; height: 44px;">
                <i class="ph-duotone ph-calendar fs-4"></i>
            </div>
            <div>
                <h6 class="mb-0 fw-bold">فلتر الشهر</h6>
                <small class="text-body-secondary">
                    كل البيانات المعروضة خاصة بالشهر المختار
                </small>
            </div>
        </div>

        <div class="d-flex align-items-center gap-2">
            <input type="month"
                   wire:model.live="month"
                   class="form-control"
                   style="max-width: 180px;">

            @if($month !== now()->format('Y-m'))
                <button type="button"
                        wire:click="$set('month', '{{ now()->format('Y-m') }}')"
                        class="btn btn-outline-secondary btn-sm">
                    <i class="ph-duotone ph-arrow-counter-clockwise me-1"></i>الشهر الحالي
                </button>
            @endif
        </div>
    </div>
</div>
