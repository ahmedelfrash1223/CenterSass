<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <div class="row g-3 align-items-end">
            {{-- الشهر --}}
            <div class="col-md-6">
                <label class="form-label small fw-semibold text-body-secondary">
                    <i class="ph-duotone ph-calendar me-1"></i>الشهر
                </label>
                <input type="month" wire:model.live="month" class="form-control">
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
