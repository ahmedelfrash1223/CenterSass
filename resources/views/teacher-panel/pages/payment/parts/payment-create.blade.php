<div>
    @if($showModal && $invoice)
        <div class="payment-modal-backdrop" wire:click="closeModal"></div>
        <div class="payment-modal-wrapper">
            <div class="payment-modal-content" wire:click.stop>

                {{-- Header --}}
                <div class="payment-modal-header">
                    <div class="payment-modal-icon">
                        <i class="ph-duotone ph-currency-circle-dollar"></i>
                    </div>
                    <div class="flex-grow-1">
                        <h5 class="mb-1 fw-bold">تسجيل دفعة</h5>
                        <small class="text-muted">
                            {{ $invoice->period_label }} — {{ $invoice->invoice_number }}
                        </small>
                    </div>
                    <button type="button" wire:click="closeModal" class="btn-close-modal">
                        <i class="ph-duotone ph-x"></i>
                    </button>
                </div>

                {{-- Summary --}}
                <div class="payment-modal-summary">
                    <div class="summary-row">
                        <span class="text-muted">الإجمالي:</span>
                        <strong>{{ number_format((float) $invoice->total, 2) }} ج.م</strong>
                    </div>
                    <div class="summary-row">
                        <span class="text-muted">المدفوع:</span>
                        <strong class="text-success">{{ number_format((float) $invoice->paid_amount, 2) }} ج.م</strong>
                    </div>
                    <div class="summary-row summary-row-highlight">
                        <span class="text-muted">المتبقي:</span>
                        <strong class="text-danger">{{ number_format((float) $invoice->remaining_amount, 2) }} ج.م</strong>
                    </div>
                </div>

                {{-- Form --}}
                <div class="payment-modal-body">

                    {{-- Amount --}}
                    <div class="mb-3">
                        <label class="form-label">المبلغ <span class="text-danger">*</span></label>
                        <div class="amount-input-wrapper">
                            <input type="number"
                                   step="0.01"
                                   min="0.01"
                                   max="{{ $invoice->remaining_amount }}"
                                   wire:model.live="amount"
                                   class="form-control form-control-lg text-center @error('amount') is-invalid @enderror"
                                   style="font-weight: 700; font-size: 20px;">
                            <span class="amount-suffix">ج.م</span>
                        </div>
                        @error('amount')<div class="text-danger small mt-1">{{ $message }}</div>@enderror

                        {{-- Quick Amount Buttons --}}
                        <div class="quick-amounts mt-2">
                            <button type="button"
                                    wire:click="$set('amount', '{{ $invoice->remaining_amount }}')"
                                    class="quick-amount-btn">
                                المبلغ الكامل
                            </button>
                            <button type="button"
                                    wire:click="$set('amount', '{{ round($invoice->remaining_amount / 2, 2) }}')"
                                    class="quick-amount-btn">
                                النصف
                            </button>
                        </div>
                    </div>

                    {{-- Payment Method --}}
                    <div class="mb-3">
                        <label class="form-label">طريقة الدفع <span class="text-danger">*</span></label>
                        <div class="payment-methods">
                            @foreach($paymentMethods as $value => $label)
                                <label class="payment-method-item {{ $payment_method === $value ? 'is-selected' : '' }}">
                                    <input type="radio"
                                           name="payment_method"
                                           value="{{ $value }}"
                                           wire:model.live="payment_method"
                                           class="d-none">
                                    <i class="ph-duotone {{ \App\Enums\Teacher\PaymentMethodEnum::tryFrom($value)?->icon() }}"></i>
                                    <span>{{ $label }}</span>
                                </label>
                            @endforeach
                        </div>
                        @error('payment_method')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>

                    {{-- Reference --}}
                    @if(in_array($payment_method, ['vodafone_cash', 'instapay', 'bank_transfer', 'cheque']))
                        <div class="mb-3">
                            <label class="form-label">رقم العملية / المرجع</label>
                            <input type="text"
                                   wire:model="reference"
                                   class="form-control @error('reference') is-invalid @enderror"
                                   placeholder="أدخل رقم العملية">
                            @error('reference')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    @endif

                    {{-- Date --}}
                    <div class="mb-3">
                        <label class="form-label">تاريخ الدفع <span class="text-danger">*</span></label>
                        <input type="date"
                               wire:model="paid_at"
                               class="form-control @error('paid_at') is-invalid @enderror">
                        @error('paid_at')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    {{-- Notes --}}
                    <div class="mb-3">
                        <label class="form-label">ملاحظات</label>
                        <textarea rows="2"
                                  wire:model="notes"
                                  class="form-control @error('notes') is-invalid @enderror"
                                  placeholder="ملاحظات اختيارية"></textarea>
                        @error('notes')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>

                {{-- Actions --}}
                <div class="payment-modal-actions">
                    <button type="button"
                            wire:click="closeModal"
                            class="btn btn-light border">
                        <i class="ph-duotone ph-x me-1"></i>
                        إلغاء
                    </button>
                    <button type="button"
                            wire:click="save"
                            wire:loading.attr="disabled"
                            wire:target="save"
                            class="btn btn-success">
                        <span wire:loading.remove wire:target="save">
                            <i class="ph-duotone ph-check-circle me-1"></i>
                            تسجيل الدفعة
                        </span>
                        <span wire:loading wire:target="save">
                            <span class="spinner-border spinner-border-sm me-1"></span>
                            جاري التسجيل...
                        </span>
                    </button>
                </div>

            </div>
        </div>
    @endif
</div>
