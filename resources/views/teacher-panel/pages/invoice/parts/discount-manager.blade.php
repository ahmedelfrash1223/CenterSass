<div>
    @if((float) $invoice->paid_amount == 0 && !$invoice->isCancelled())

        @if($invoice->has_discount && !$showForm)
            <div class="alert alert-info d-flex align-items-start gap-3">
                <i class="ph-duotone ph-tag fs-3"></i>
                <div class="flex-grow-1">
                    <strong>خصم مطبّق: {{ number_format((float) $invoice->discount, 2) }} ج.م</strong>
                    <div class="small mt-1">
                        النوع: {{ $invoice->discount_type_label }}
                        @if($invoice->discount_type === 'percentage')
                            ({{ (float) $invoice->discount_value }}%)
                        @else
                            ({{ number_format((float) $invoice->discount_value, 2) }} ج.م)
                        @endif
                    </div>
                    @if($invoice->discount_reason)
                        <div class="small mt-1 text-body-secondary">
                            <i class="ph-duotone ph-chat-text me-1"></i>
                            السبب: {{ $invoice->discount_reason }}
                        </div>
                    @endif
                </div>
                <div class="d-flex gap-2">
                    <button type="button" wire:click="openForm" class="btn btn-sm btn-outline-primary">
                        <i class="ph-duotone ph-pencil-simple me-1"></i>تعديل
                    </button>
                    <button type="button"
                            wire:click="remove"
                            wire:confirm="متأكد من إزالة الخصم؟"
                            class="btn btn-sm btn-outline-danger">
                        <i class="ph-duotone ph-trash"></i>
                    </button>
                </div>
            </div>
        @elseif($showForm)
            <div class="card border-primary border-2 mb-3">
                <div class="card-header bg-primary-subtle">
                    <h6 class="mb-0 fw-bold">
                        <i class="ph-duotone ph-tag me-1"></i>
                        تطبيق خصم
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">نوع الخصم <span class="text-danger">*</span></label>
                            <select wire:model.live="discount_type" class="form-select">
                                @foreach($discountTypes as $value => $label)
                                    <option value="{{ $value }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold">
                                @if($discount_type === 'percentage')
                                    النسبة (%)
                                @else
                                    المبلغ (ج.م)
                                @endif
                                <span class="text-danger">*</span>
                            </label>
                            <input type="number"
                                   step="0.01"
                                   min="0"
                                   @if($discount_type === 'percentage') max="100" @endif
                                   wire:model.live="discount_value"
                                   class="form-control @error('discount_value') is-invalid @enderror">
                            @error('discount_value')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold">القيمة بعد الخصم</label>
                            <div class="form-control bg-body-secondary" style="pointer-events: none;">
                                @php
                                    $subtotal = (float) $invoice->subtotal;
                                    $value = (float) $discount_value;
                                    $calc = $discount_type === 'percentage'
                                        ? round($subtotal * ($value / 100), 2)
                                        : $value;
                                    $calc = min($calc, $subtotal);
                                @endphp
                                <strong class="text-success">
                                    {{ number_format(max(0, $subtotal - $calc), 2) }} ج.م
                                </strong>
                                <small class="text-body-secondary ms-2">
                                    (خصم {{ number_format($calc, 2) }})
                                </small>
                            </div>
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold">سبب الخصم</label>
                            <input type="text"
                                   wire:model="discount_reason"
                                   class="form-control"
                                   placeholder="مثال: أخوة، تفوق، خصم خاص..."
                                   maxlength="255">
                            @error('discount_reason')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="d-flex gap-2 mt-3">
                        <button type="button" wire:click="apply" class="btn btn-primary">
                            <i class="ph-duotone ph-check me-1"></i>تطبيق
                        </button>
                        <button type="button" wire:click="cancel" class="btn btn-outline-secondary">
                            إلغاء
                        </button>
                    </div>
                </div>
            </div>
        @else
            <button type="button" wire:click="openForm" class="btn btn-outline-primary btn-sm mb-3">
                <i class="ph-duotone ph-tag me-1"></i>إضافة خصم
            </button>
        @endif

    @endif
</div>
