<div>
    @if($student)

        {{-- Student Header --}}
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body d-flex align-items-center justify-content-between flex-wrap gap-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-circle text-white d-flex align-items-center justify-content-center fw-bold"
                         style="width: 56px; height: 56px; background: var(--bs-primary);">
                        {{ mb_substr($student->name, 0, 1) }}
                    </div>
                    <div>
                        <h5 class="mb-1 fw-bold">{{ $student->name }}</h5>
                        <div class="d-flex gap-2 flex-wrap small text-body-secondary">
                            <span>{{ $student->code }}</span>
                            @if($student->branch)
                                <span>·</span><span>{{ $student->branch->name }}</span>
                            @endif
                            <span>·</span><span>{{ $student->active_groups_count }} مجموعة</span>
                        </div>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-2">
                    @php $totalDue = collect($dueGroups)->sum('remaining'); @endphp
                    @if($totalDue > 0)
                        <span
                            class="badge bg-danger-subtle text-danger-emphasis border border-danger-subtle fs-6 py-2 px-3">
                            متأخرات: {{ number_format($totalDue, 2) }} ج.م
                        </span>
                    @endif
                    <button type="button" wire:click="clear" class="btn btn-outline-secondary">
                        <i class="ph-duotone ph-x"></i>
                    </button>
                </div>
            </div>
        </div>

        <div class="row g-4">
            {{-- Due Groups --}}
            <div class="col-lg-7">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-body d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <h6 class="mb-0 fw-bold">المجموعات المستحقة</h6>
                    </div>
                    <div class="card-body">
                        @if(count($dueGroups) > 0)
                            <div class="d-flex flex-column gap-3">
                                @foreach($dueGroups as $due)
                                    @php
                                        $group = $due['group'];
                                        $itemId = $due['item_id'];
                                        $item = $due['item'];
                                        $isFullyPaid = $due['is_fully_paid'];
                                        $isSelected = isset($selectedAmounts[$itemId]);
                                        $pct = $due['amount'] > 0 ? min(100, ($due['paid'] / $due['amount']) * 100) : 0;
                                        $hasDiscount = $item && $item->has_discount;
                                        $showForm = $showDiscountForm[$itemId] ?? false;
                                    @endphp

                                    <div
                                        class="card border {{ $isSelected ? 'border-primary border-2' : '' }} {{ $isFullyPaid ? 'bg-success-subtle' : '' }}"
                                        wire:key="due-{{ $group->id }}-{{ $itemId }}"
                                        @if(!$isFullyPaid) wire:click="toggleItem({{ $itemId }})"
                                        style="cursor: pointer;" @endif>
                                        <div class="card-body">
                                            {{-- Top --}}
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <div class="fw-bold">{{ $group->name }}</div>
                                                <div>
                                                    @if($isFullyPaid)
                                                        <span class="badge bg-success-subtle text-success-emphasis">مدفوع بالكامل</span>
                                                    @else
                                                        <span class="fw-bold text-danger">
                                                            {{ number_format($due['remaining'], 2) }} <small>ج.م</small>
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>

                                            @if(!$isFullyPaid)
                                                {{-- Progress --}}
                                                <div class="progress mb-2" style="height: 6px;">
                                                    <div class="progress-bar" style="width: {{ $pct }}%"></div>
                                                </div>

                                                {{-- Amounts --}}
                                                <div class="small text-body-secondary mb-2">
                                                    <div class="d-flex justify-content-between">
                                                        <span>مدفوع: {{ number_format($due['paid'], 2) }}</span>
                                                        <span>الإجمالي: {{ number_format($due['amount'], 2) }}</span>
                                                    </div>

                                                    @if($hasDiscount)
                                                        <div class="d-flex justify-content-between text-success mt-1">
                                                            <span>
                                                                <i class="ph-duotone ph-tag me-1"></i>
                                                                خصم
                                                                @if($item->discount_type === 'percentage')
                                                                    ({{ (float) $item->discount_value }}%)
                                                                @else
                                                                    (ثابت)
                                                                @endif
                                                                :
                                                            </span>
                                                            <strong>- {{ number_format((float) $item->discount_amount, 2) }}</strong>
                                                        </div>
                                                        <div class="d-flex justify-content-between mt-1">
                                                            <span>الصافي:</span>
                                                            <strong>{{ number_format($item->net_total, 2) }}
                                                                ج.م</strong>
                                                        </div>
                                                        @if($item->discount_reason)
                                                            <div class="mt-1 small text-body-secondary fst-italic">
                                                                <i class="ph-duotone ph-chat-text me-1"></i>
                                                                {{ $item->discount_reason }}
                                                            </div>
                                                        @endif
                                                    @endif
                                                </div>

                                                <div class="form-check mb-2">
                                                    <input type="checkbox"
                                                           class="form-check-input"
                                                           id="item-{{ $itemId }}"
                                                           wire:click.stop="toggleItem({{ $itemId }})"
                                                           @checked($isSelected)
                                                           style="width: 22px; height: 22px; cursor: pointer;">
                                                    <label class="form-check-label fw-semibold ms-2"
                                                           for="item-{{ $itemId }}"
                                                           style="cursor: pointer; font-size: 15px;">
                                                        اختر للدفع
                                                    </label>
                                                </div>

                                                {{-- Discount Section --}}
                                                @if($due['paid'] == 0)
                                                    <div class="border-top pt-2 mt-2" wire:click.stop>
                                                        @if(!$hasDiscount && !$showForm)
                                                            <button type="button"
                                                                    wire:click.stop="toggleDiscountForm({{ $itemId }})"
                                                                    class="btn btn-sm btn-outline-success">
                                                                <i class="ph-duotone ph-tag me-1"></i>إضافة خصم
                                                            </button>
                                                        @endif

                                                        @if($showForm || $hasDiscount)
                                                            <div class="border rounded p-2 bg-body-tertiary">
                                                                <div class="row g-2">
                                                                    <div class="col-4">
                                                                        <label
                                                                            class="form-label small mb-1">النوع</label>
                                                                        <select
                                                                            wire:model.live="discountTypes.{{ $itemId }}"
                                                                            wire:click.stop
                                                                            class="form-select form-select-sm">
                                                                            @foreach($discountTypeList as $key => $label)
                                                                                <option
                                                                                    value="{{ $key }}">{{ $label }}</option>
                                                                            @endforeach
                                                                        </select>
                                                                    </div>
                                                                    <div class="col-4">
                                                                        <label class="form-label small mb-1">
                                                                            @if(($discountTypes[$itemId] ?? '') === 'percentage')
                                                                                النسبة %
                                                                            @else
                                                                                المبلغ
                                                                            @endif
                                                                        </label>
                                                                        <input type="number"
                                                                               step="0.01"
                                                                               min="0"
                                                                               @if(($discountTypes[$itemId] ?? '') === 'percentage') max="100"
                                                                               @endif
                                                                               wire:model.live="discountValues.{{ $itemId }}"
                                                                               wire:click.stop
                                                                               class="form-control form-control-sm">
                                                                    </div>
                                                                    <div class="col-4">
                                                                        <label class="form-label small mb-1">بعد
                                                                            الخصم</label>
                                                                        <div
                                                                            class="form-control form-control-sm bg-body-secondary text-center"
                                                                            style="pointer-events: none;">
                                                                            @php
                                                                                $total = (float) $due['amount'];
                                                                                $val = (float) ($discountValues[$itemId] ?? 0);
                                                                                $dt = $discountTypes[$itemId] ?? 'fixed';
                                                                                $calc = $dt === 'percentage' ? round($total * ($val / 100), 2) : $val;
                                                                                $calc = min($calc, $total);
                                                                            @endphp
                                                                            <strong class="text-success small">
                                                                                {{ number_format(max(0, $total - $calc), 2) }}
                                                                            </strong>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-12">
                                                                        <label
                                                                            class="form-label small mb-1">السبب</label>
                                                                        <input type="text"
                                                                               wire:model="discountReasons.{{ $itemId }}"
                                                                               wire:click.stop
                                                                               class="form-control form-control-sm"
                                                                               placeholder="مثال: أخوة، تفوق، خصم خاص..."
                                                                               maxlength="255">
                                                                    </div>
                                                                </div>

                                                                <div class="d-flex gap-2 mt-2">
                                                                    <button type="button"
                                                                            wire:click.stop="applyDiscountToItem({{ $itemId }})"
                                                                            class="btn btn-sm btn-success">
                                                                        <i class="ph-duotone ph-check me-1"></i>تطبيق
                                                                    </button>
                                                                    @if($hasDiscount)
                                                                        {{-- ✅ من غير wire:confirm --}}
                                                                        <button type="button"
                                                                                wire:click.stop="removeDiscountFromItem({{ $itemId }})"
                                                                                class="btn btn-sm btn-outline-danger">
                                                                            <i class="ph-duotone ph-trash me-1"></i>إزالة
                                                                        </button>
                                                                    @endif
                                                                    <button type="button"
                                                                            wire:click.stop="toggleDiscountForm({{ $itemId }})"
                                                                            class="btn btn-sm btn-outline-secondary ms-auto">
                                                                        إلغاء
                                                                    </button>
                                                                </div>
                                                            </div>
                                                        @endif
                                                    </div>
                                                @endif

                                                {{-- Amount input --}}
                                                @if($isSelected)
                                                    <div class="input-group input-group-sm mt-2" wire:click.stop>
                                                        <span class="input-group-text">المبلغ</span>
                                                        <input type="number"
                                                               step="0.01"
                                                               min="0.01"
                                                               max="{{ $due['remaining'] }}"
                                                               wire:model.blur="selectedAmounts.{{ $itemId }}"
                                                               wire:key="amount-{{ $itemId }}"
                                                               wire:click.stop
                                                               class="form-control text-center">
                                                        <span class="input-group-text">ج.م</span>
                                                    </div>
                                                @endif
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center text-body-secondary py-5">
                                <i class="ph-duotone ph-check-circle text-success fs-1 d-block mb-2"></i>
                                <h6 class="fw-bold">لا توجد مستحقات</h6>
                                <p class="small mb-0">الطالب دفع كل مستحقات الشهر دا.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Payment Form --}}
            <div class="col-lg-5" data-payment-form>
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-body">
                        <h6 class="mb-0 fw-bold">تسجيل دفعة</h6>
                    </div>
                    <div class="card-body">

                        @if($this->selected_count > 0)
                            <div class="alert alert-primary mb-3">
                                <div class="d-flex justify-content-between small mb-1">
                                    <span>عدد المجموعات:</span>
                                    <strong>{{ $this->selected_count }}</strong>
                                </div>
                                @if($this->total_discount > 0)
                                    <div class="d-flex justify-content-between small mb-1">
                                        <span>إجمالي الخصم:</span>
                                        <strong class="text-success">- {{ number_format($this->total_discount, 2) }}
                                            ج.م</strong>
                                    </div>
                                @endif
                                <div class="d-flex justify-content-between small">
                                    <span>الإجمالي المطلوب:</span>
                                    <strong class="text-success">{{ number_format($this->total_selected, 2) }}
                                        ج.م</strong>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-semibold">طريقة الدفع <span
                                        class="text-danger">*</span></label>
                                <div class="row g-2">
                                    @foreach($paymentMethods as $value => $label)
                                        @php
                                            $enum = \App\Enums\Teacher\PaymentMethodEnum::tryFrom($value);
                                            $isSel = $payment_method === $value;
                                        @endphp
                                        <div class="col-4">
                                            <button type="button"
                                                    wire:click="$set('payment_method', '{{ $value }}')"
                                                    class="btn w-100 d-flex flex-column align-items-center py-2 {{ $isSel ? 'btn-primary' : 'btn-outline-secondary' }}">
                                                <i class="ph-duotone {{ $enum?->icon() }} fs-5 mb-1"></i>
                                                <small>{{ $label }}</small>
                                            </button>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            @if(\App\Enums\Teacher\PaymentMethodEnum::tryFrom($payment_method)?->requiresReference())
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">رقم العملية</label>
                                    <input type="text" wire:model="reference" class="form-control" placeholder="المرجع">
                                </div>
                            @endif

                            <div class="mb-3">
                                <label class="form-label fw-semibold">التاريخ <span class="text-danger">*</span></label>
                                <input type="date" wire:model="paid_at"
                                       class="form-control @error('paid_at') is-invalid @enderror">
                                @error('paid_at')
                                <div class="text-danger small mt-1">{{ $message }}</div>@enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-semibold">ملاحظات</label>
                                <textarea wire:model="notes" rows="2" class="form-control"
                                          placeholder="اختياري"></textarea>
                            </div>

                            @if($showConfirm)
                                <div class="alert alert-warning border-warning">
                                    <div class="d-flex gap-2 mb-2">
                                        <i class="ph-duotone ph-warning-circle fs-4"></i>
                                        <div>
                                            <strong>تأكيد الدفع</strong>
                                            <p class="small mb-0 mt-1">
                                                هتسجل دفعة بمبلغ
                                                <strong>{{ number_format($this->total_selected, 2) }} ج.م</strong>
                                                على {{ $this->selected_count }} مجموعة.
                                            </p>
                                        </div>
                                    </div>
                                    <div class="d-flex gap-2">
                                        <button type="button"
                                                wire:click="recordPayment"
                                                wire:loading.attr="disabled"
                                                class="btn btn-success flex-fill">
                                            <span wire:loading.remove wire:target="recordPayment">
                                                <i class="ph-duotone ph-check me-1"></i>تأكيد
                                            </span>
                                            <span wire:loading wire:target="recordPayment">
                                                <span class="spinner-border spinner-border-sm me-1"></span>
                                                جاري التسجيل...
                                            </span>
                                        </button>
                                        <button type="button" wire:click="cancelConfirm"
                                                class="btn btn-outline-secondary flex-fill">
                                            إلغاء
                                        </button>
                                    </div>
                                </div>
                            @else
                                <button type="button"
                                        wire:click="confirmPayment"
                                        class="btn btn-success btn-lg w-100">
                                    <i class="ph-duotone ph-check-circle me-1"></i>
                                    تسجيل {{ number_format($this->total_selected, 2) }} ج.م
                                </button>
                            @endif
                        @else
                            <div class="text-center text-body-secondary py-5">
                                <i class="ph-duotone ph-hand-pointing fs-1 d-block mb-2"></i>
                                <h6 class="fw-bold">اختر مجموعة</h6>
                                <p class="small mb-0">اضغط على أي مجموعة لتحديدها.</p>
                            </div>
                        @endif

                    </div>
                </div>
            </div>
        </div>

        {{-- Receipt --}}
        @if($activeReceipt)
            <div class="mt-4" data-receipt-section wire:key="receipt-{{ $activeReceipt->id }}">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-body d-flex justify-content-between align-items-center">
                        <h6 class="mb-0 fw-bold">
                            <i class="ph-duotone ph-receipt me-1"></i>
                            إيصال الدفع — {{ $activeReceipt->receipt_number }}
                        </h6>
                        <div class="d-flex gap-2">
                            <button type="button" onclick="printReceipt()" class="btn btn-primary btn-sm">
                                <i class="ph-duotone ph-printer me-1"></i>طباعة
                            </button>
                            <button type="button" wire:click="closeReceipt" class="btn btn-outline-secondary btn-sm">
                                <i class="ph-duotone ph-x"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body bg-body-tertiary">
                        <div class="bg-white text-dark rounded-3 p-4 mx-auto border"
                             id="printable-receipt" style="max-width: 400px;">
                            @include('teacher-panel.pages.payment.parts.receipt-content', [
                                'payment'   => $activeReceipt,
                                'breakdown' => $activeReceiptBreakdown,
                            ])
                        </div>
                    </div>
                </div>
            </div>
        @endif

    @endif

    @script
    <script>
        window.printReceipt = function () {
            const content = document.getElementById('printable-receipt');
            if (!content) return;

            const printWindow = window.open('', '_blank', 'height=800,width=600');
            printWindow.document.write(`
                <!DOCTYPE html>
                <html dir="rtl">
                <head>
                    <meta charset="UTF-8">
                    <title>طباعة الإيصال</title>
                    <style>
                        * { margin: 0; padding: 0; box-sizing: border-box; }
                        body { font-family: 'Cairo', 'DejaVu Sans', sans-serif; padding: 8mm; direction: rtl; font-size: 11px; color: #212529; background: #fff; }
                        .receipt-paper { max-width: 80mm; margin: 0 auto; }
                        .receipt-header { text-align: center; padding-bottom: 10px; border-bottom: 2px solid #212529; margin-bottom: 12px; }
                        .receipt-shop-name { font-size: 16px; font-weight: bold; margin-bottom: 4px; }
                        .receipt-shop-phone { font-size: 10px; color: #6c757d; }
                        .receipt-title { font-size: 13px; font-weight: bold; color: #0d6efd; margin-top: 8px; }
                        .receipt-number { font-size: 10px; color: #495057; margin-top: 4px; }
                        .receipt-divider { border-top: 2px solid #212529; margin: 10px 0; }
                        .receipt-divider-dashed { border-top: 1px dashed #999; margin: 10px 0; }
                        .receipt-info-row { display: flex; justify-content: space-between; padding: 3px 0; font-size: 10px; }
                        .receipt-info-row span:first-child { color: #6c757d; }
                        .receipt-info-row strong { font-weight: bold; }
                        .receipt-barcode, .receipt-qr { text-align: center; margin: 10px 0; }
                        .receipt-barcode svg, .receipt-qr svg { max-width: 100%; height: auto; }
                        .barcode-value, .qr-caption { font-family: monospace; font-size: 10px; letter-spacing: 1.5px; margin-top: 4px; }
                        .receipt-amount-section { background: #f5f5f5; padding: 10px; border-radius: 6px; margin: 10px 0; }
                        .receipt-amount-total { display: flex; justify-content: space-between; padding-top: 8px; margin-top: 8px; border-top: 2px solid #ccc; font-size: 13px; font-weight: bold; color: #198754; }
                        .receipt-paid-stamp { text-align: center; padding: 8px; background: #e7f5eb; color: #198754; border-radius: 6px; font-weight: bold; font-size: 11px; }
                        .receipt-footer { text-align: center; font-size: 9px; color: #6c757d; margin-top: 14px; padding-top: 8px; border-top: 1px dashed #ccc; }
                        @page { margin: 5mm; size: 80mm auto; }
                    </style>
                </head>
                <body>${content.innerHTML}</body>
                </html>
            `);
            printWindow.document.close();
            setTimeout(() => {
                printWindow.focus();
                printWindow.print();
                printWindow.close();
            }, 300);
        };

        $wire.on('scroll-to-payment-form', () => {
            setTimeout(() => {
                const el = document.querySelector('[data-payment-form]');
                if (el) el.scrollIntoView({behavior: 'smooth', block: 'start'});
            }, 250);
        });

        $wire.on('scroll-to-receipt', () => {
            setTimeout(() => {
                const el = document.querySelector('[data-receipt-section]');
                if (el) el.scrollIntoView({behavior: 'smooth', block: 'start'});
            }, 400);
        });
    </script>
    @endscript
</div>
