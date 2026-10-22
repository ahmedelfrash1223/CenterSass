<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <div class="row g-3 align-items-end">

            {{-- الشهر --}}
            <div class="col-md-6">
                <label class="form-label small fw-semibold text-body-secondary">
                    <i class="ph-duotone ph-calendar me-1"></i>الشهر
                </label>

                <div class="d-flex align-items-center gap-2">
                    <input type="month" wire:model.live="month" class="form-control">

                    @if(!$isCurrentMonth)
                        <button type="button" wire:click="goToCurrentMonth" wire:loading.attr="disabled"
                            wire:target="goToCurrentMonth" class="btn btn-outline-primary flex-shrink-0"
                            title="العودة للشهر الحالي">
                            <span wire:loading.remove wire:target="goToCurrentMonth">
                                <i class="ph-duotone ph-arrow-counter-clockwise me-1"></i>
                                الشهر الحالي
                            </span>
                            <span wire:loading wire:target="goToCurrentMonth">
                                <span class="spinner-border spinner-border-sm"></span>
                            </span>
                        </button>
                    @endif
                </div>
            </div>

            {{-- الفرع --}}
            <div class="col-md-6">
                <label class="form-label small fw-semibold text-body-secondary">
                    <i class="ph-duotone ph-git-branch me-1"></i>الفرع
                </label>
                @livewire('teacher-panel.pages.payment.parts.branch-selector')
            </div>

        </div>
    </div>
</div>
