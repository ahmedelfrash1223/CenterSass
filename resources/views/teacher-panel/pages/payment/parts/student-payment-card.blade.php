<div>
    @if($student)

        {{-- ============================
             Student Header
             ============================ --}}
        <div class="card border shadow-sm mb-4">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">

                    <div class="d-flex align-items-center gap-3">
                        <div
                            class="rounded-circle bg-primary bg-opacity-10 text-primary d-flex align-items-center justify-content-center fw-bold flex-shrink-0"
                            style="width: 60px; height: 60px; font-size: 24px;">
                            {{ mb_substr($student->name, 0, 1) }}
                        </div>
                        <div>
                            <h5 class="mb-1 fw-bold">{{ $student->name }}</h5>
                            <div class="d-flex gap-3 flex-wrap small text-muted">
                                <span><i class="ph-duotone ph-identification-card me-1"></i>{{ $student->code }}</span>
                                @if($student->branch)
                                    <span><i
                                            class="ph-duotone ph-git-branch me-1"></i>{{ $student->branch->name }}</span>
                                @endif
                                <span><i class="ph-duotone ph-users-three me-1"></i>{{ $student->groups_count }} مجموعة</span>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex align-items-center gap-2">
                        @if($student->total_unpaid > 0)
                            <span
                                class="badge bg-danger-subtle text-danger-emphasis border border-danger-subtle fs-6 py-2 px-3">
                                <i class="ph-duotone ph-warning-circle me-1"></i>
                                متأخرات: {{ number_format($student->total_unpaid, 2) }} ج.م
                            </span>
                        @endif
                        <button type="button" wire:click="clear" class="btn btn-outline-secondary">
                            <i class="ph-duotone ph-x me-1"></i>إغلاق
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4">

            {{-- ============================
                 Invoices List
                 ============================ --}}
            <div class="col-lg-7">
                <div class="card border shadow-sm h-100">
                    <div
                        class="card-header bg-body-tertiary d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <h6 class="mb-0 fw-bold">
                            <i class="ph-duotone ph-receipt me-1"></i>
                            الفواتير
                            <span class="badge bg-primary ms-1">{{ $invoices->count() }}</span>
                        </h6>

                        @if($canManage)
                            <div class="d-flex gap-2 align-items-center">
                                <input type="month" wire:model="generateMonth" class="form-control form-control-sm"
                                       style="width: 140px;">
                                <button type="button"
                                        wire:click="generateInvoice"
                                        wire:loading.attr="disabled"
                                        class="btn btn-sm btn-primary">
                                    <i class="ph-duotone ph-plus me-1"></i>
                                    توليد
                                </button>
                            </div>
                        @endif
                    </div>

                    <div class="card-body">
                        @if($invoices->count() > 0)
                            <div class="d-flex flex-column gap-3">
                                @foreach($invoices as $invoice)
                                    @php
                                        $isSelected = $selectedInvoiceId === $invoice->id;
                                        $canPay = $invoice->has_remaining && !$invoice->is_cancelled;
                                    @endphp

                                    <div
                                        class="card border {{ $isSelected ? 'border-primary border-2' : '' }} {{ $invoice->is_cancelled ? 'opacity-50' : '' }}">
                                        <div class="card-body p-3">

                                            {{-- Header --}}
                                            <div
                                                class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-2">
                                                <div class="d-flex align-items-center gap-2">
                                                    <span
                                                        class="badge bg-{{ $invoice->status_color }}-subtle text-{{ $invoice->status_color }}-emphasis">
                                                        <i class="ph-duotone {{ $invoice->status_icon }} me-1"></i>
                                                        {{ $invoice->status_label }}
                                                    </span>
                                                    <span class="fw-bold">{{ $invoice->period_label }}</span>
                                                </div>

                                                <div class="text-end">
                                                    <div
                                                        class="fw-bold fs-5">{{ number_format((float) $invoice->total, 2) }}
                                                        ج.م
                                                    </div>
                                                    @if($invoice->remaining_amount > 0)
                                                        <small class="text-danger">
                                                            متبقي: {{ number_format((float) $invoice->remaining_amount, 2) }}
                                                            ج.م
                                                        </small>
                                                    @endif
                                                </div>
                                            </div>

                                            {{-- Invoice number --}}
                                            <small class="text-muted d-block mb-2">
                                                <i class="ph-duotone ph-hash me-1"></i>{{ $invoice->invoice_number }}
                                            </small>

                                            {{-- Items --}}
                                            @if($invoice->items->count() > 0)
                                                <div class="d-flex flex-wrap gap-1 mb-2">
                                                    @foreach($invoice->items as $item)
                                                        <span class="badge bg-body-tertiary text-body border">
                                                            {{ $item->description }}
                                                        </span>
                                                    @endforeach
                                                </div>
                                            @endif

                                            {{-- Actions --}}
                                            <div
                                                class="d-flex justify-content-between align-items-center gap-2 flex-wrap mt-2 pt-2 border-top">
                                                <div class="d-flex gap-1">
                                                    <a href="{{ route('teacher.invoices.pdf', $invoice->id) }}"
                                                       target="_blank"
                                                       class="btn btn-sm btn-outline-danger">
                                                        <i class="ph-duotone ph-file-pdf me-1"></i>PDF
                                                    </a>
                                                    <button type="button"
                                                            class="btn btn-sm btn-outline-secondary"
                                                            onclick="window.print()">
                                                        <i class="ph-duotone ph-printer me-1"></i>طباعة
                                                    </button>
                                                </div>

                                                @if($canPay)
                                                    <button type="button"
                                                            wire:click="selectInvoice({{ $invoice->id }})"
                                                            class="btn btn-sm {{ $isSelected ? 'btn-primary' : 'btn-outline-primary' }}">
                                                        <i class="ph-duotone ph-currency-circle-dollar me-1"></i>
                                                        {{ $isSelected ? 'محددة' : 'اختر للدفع' }}
                                                    </button>
                                                @elseif($invoice->is_paid)
                                                    <span class="badge bg-success-subtle text-success-emphasis">
                                                        <i class="ph-duotone ph-check-circle me-1"></i>مدفوع
                                                    </span>
                                                @endif
                                            </div>

                                            {{-- Payments --}}
                                            @if($invoice->payments->count() > 0)
                                                <div class="mt-2 pt-2 border-top">
                                                    <small class="text-muted d-block mb-1">الدفعات:</small>
                                                    <div class="d-flex flex-wrap gap-1">
                                                        @foreach($invoice->payments as $payment)
                                                            <button type="button"
                                                                    wire:click="showReceipt({{ $payment->id }})"
                                                                    class="btn btn-sm btn-outline-success">
                                                                <i class="ph-duotone ph-receipt me-1"></i>
                                                                {{ number_format((float) $payment->amount, 2) }}
                                                            </button>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-5 text-muted">
                                <i class="ph-duotone ph-receipt fs-1 d-block mb-2"></i>
                                <h6>لا توجد فواتير</h6>
                                <small>استخدم زرار "توليد" فوق.</small>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- ============================
                 Payment Form
                 ============================ --}}
            <div class="col-lg-5">
                <div class="card border shadow-sm h-100">
                    <div class="card-header bg-body-tertiary">
                        <h6 class="mb-0 fw-bold">
                            <i class="ph-duotone ph-currency-circle-dollar me-1"></i>
                            تسجيل دفعة
                        </h6>
                    </div>

                    <div class="card-body">
                        @if($selectedInvoiceId && ($inv = $invoices->firstWhere('id', $selectedInvoiceId)))
                            {{-- Selected Invoice Summary --}}
                            <div class="alert alert-primary mb-3">
                                <div class="d-flex justify-content-between mb-1 small">
                                    <span>الشهر:</span>
                                    <strong>{{ $inv->period_label }}</strong>
                                </div>
                                <div class="d-flex justify-content-between mb-1 small">
                                    <span>المتبقي:</span>
                                    <strong>{{ number_format((float) $inv->remaining_amount, 2) }} ج.م</strong>
                                </div>
                            </div>

                            {{-- Amount --}}
                            <div class="mb-3">
                                <label class="form-label fw-semibold">المبلغ <span class="text-danger">*</span></label>
                                <div class="input-group input-group-lg">
                                    <input type="number"
                                           step="0.01"
                                           min="0.01"
                                           max="{{ $inv->remaining_amount }}"
                                           wire:model.live="amount"
                                           class="form-control text-center fw-bold @error('amount') is-invalid @enderror">
                                    <span class="input-group-text">ج.م</span>
                                </div>
                                @error('amount')
                                <div class="text-danger small mt-1">{{ $message }}</div>@enderror

                                <div class="d-flex gap-2 mt-2">
                                    <button type="button"
                                            wire:click="$set('amount', '{{ $inv->remaining_amount }}')"
                                            class="btn btn-sm btn-outline-primary flex-fill">
                                        المبلغ كامل
                                    </button>
                                    <button type="button"
                                            wire:click="$set('amount', '{{ round($inv->remaining_amount / 2, 2) }}')"
                                            class="btn btn-sm btn-outline-primary flex-fill">
                                        النصف
                                    </button>
                                </div>
                            </div>

                            {{-- Payment Method --}}
                            <div class="mb-3">
                                <label class="form-label fw-semibold">طريقة الدفع <span
                                        class="text-danger">*</span></label>
                                <div class="d-flex flex-wrap gap-2">
                                    @foreach($paymentMethods as $value => $label)
                                        @php $isSel = $payment_method === $value; @endphp
                                        <button type="button"
                                                wire:click="$set('payment_method', '{{ $value }}')"
                                                class="btn btn-sm {{ $isSel ? 'btn-primary' : 'btn-outline-secondary' }} flex-fill"
                                                style="min-width: 90px;">
                                            <i class="ph-duotone {{ \App\Enums\Teacher\PaymentMethodEnum::tryFrom($value)?->icon() }} d-block mb-1"></i>
                                            <small>{{ $label }}</small>
                                        </button>
                                    @endforeach
                                </div>
                                @error('payment_method')
                                <div class="text-danger small mt-1">{{ $message }}</div>@enderror
                            </div>

                            {{-- Reference --}}
                            @if(in_array($payment_method, ['vodafone_cash', 'instapay', 'bank_transfer', 'cheque']))
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">رقم العملية</label>
                                    <input type="text" wire:model="reference" class="form-control" placeholder="المرجع">
                                    @error('reference')
                                    <div class="text-danger small mt-1">{{ $message }}</div>@enderror
                                </div>
                            @endif

                            {{-- Date --}}
                            <div class="mb-3">
                                <label class="form-label fw-semibold">التاريخ <span class="text-danger">*</span></label>
                                <input type="date" wire:model="paid_at"
                                       class="form-control @error('paid_at') is-invalid @enderror">
                                @error('paid_at')
                                <div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            {{-- Notes --}}
                            <div class="mb-3">
                                <label class="form-label fw-semibold">ملاحظات</label>
                                <textarea wire:model="notes" rows="2" class="form-control"
                                          placeholder="اختياري"></textarea>
                            </div>

                            <button type="button"
                                    wire:click="recordPayment"
                                    wire:loading.attr="disabled"
                                    class="btn btn-success btn-lg w-100">
                                <span wire:loading.remove wire:target="recordPayment">
                                    <i class="ph-duotone ph-check-circle me-1"></i>
                                    تسجيل {{ number_format((float) $amount, 2) }} ج.م
                                </span>
                                <span wire:loading wire:target="recordPayment">
                                    <span class="spinner-border spinner-border-sm me-1"></span>
                                    جاري التسجيل...
                                </span>
                            </button>
                        @else
                            <div class="text-center py-5 text-muted">
                                <i class="ph-duotone ph-hand-pointing fs-1 d-block mb-2"></i>
                                <h6>اختر فاتورة</h6>
                                <small>اضغط على أي فاتورة لعرض فورم الدفع.</small>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

        </div>

        {{-- ============================
             Receipt Section
             ============================ --}}
        @if($activeReceipt)
            <div class="mt-4">
                <div class="card border shadow-sm">
                    <div class="card-header bg-body-tertiary d-flex justify-content-between align-items-center">
                        <h6 class="mb-0 fw-bold">
                            <i class="ph-duotone ph-receipt me-1"></i>
                            الإيصال — {{ $activeReceipt->receipt_number }}
                        </h6>
                        <div class="d-flex gap-2">
                            <a href="{{ route('teacher.payments.pdf', $activeReceipt->id) }}"
                               target="_blank"
                               class="btn btn-sm btn-outline-danger">
                                <i class="ph-duotone ph-file-pdf me-1"></i>PDF
                            </a>
                            <button type="button" onclick="window.print()" class="btn btn-sm btn-outline-primary">
                                <i class="ph-duotone ph-printer me-1"></i>طباعة
                            </button>
                            <button type="button" wire:click="closeReceipt" class="btn btn-sm btn-outline-secondary">
                                <i class="ph-duotone ph-x"></i>
                            </button>
                        </div>
                    </div>

                    <div class="card-body">
                        <div class="mx-auto bg-white text-dark p-4 rounded border" id="printable-receipt"
                             style="max-width: 400px;">

                            <div class="text-center border-bottom pb-3 mb-3">
                                <h4 class="fw-bold mb-1">{{ $activeReceipt->teacher?->name }}</h4>
                                @if($activeReceipt->teacher?->phone)
                                    <small class="text-muted d-block">
                                        <i class="ph-duotone ph-phone me-1"></i>{{ $activeReceipt->teacher->phone }}
                                    </small>
                                @endif
                                <h6 class="text-primary mt-2 mb-1">إيصال دفع</h6>
                                <small class="text-muted">{{ $activeReceipt->receipt_number }}</small>
                            </div>

                            <div class="mb-3">
                                <div class="d-flex justify-content-between mb-1 small">
                                    <span class="text-muted">الطالب:</span>
                                    <strong>{{ $activeReceipt->student?->name }}</strong>
                                </div>
                                <div class="d-flex justify-content-between mb-1 small">
                                    <span class="text-muted">الكود:</span>
                                    <strong>{{ $activeReceipt->student?->code }}</strong>
                                </div>
                            </div>

                            <div class="text-center py-2 border-top border-bottom my-3">
                                {!! DNS1D::getBarcodeHTML($activeReceipt->student?->barcode_digits ?? '0', 'C128', 1.2, 35) !!}
                                <small
                                    class="d-block mt-1 font-monospace">{{ $activeReceipt->student?->barcode_digits }}</small>
                            </div>

                            <div class="mb-3">
                                <div class="d-flex justify-content-between mb-1 small">
                                    <span class="text-muted">التاريخ:</span>
                                    <strong>{{ $activeReceipt->paid_at?->format('Y-m-d') }}</strong>
                                </div>
                                <div class="d-flex justify-content-between mb-1 small">
                                    <span class="text-muted">طريقة الدفع:</span>
                                    <strong>{{ $activeReceipt->payment_method_label }}</strong>
                                </div>
                                <div class="d-flex justify-content-between mb-1 small">
                                    <span class="text-muted">الشهر:</span>
                                    <strong>{{ $activeReceipt->invoice?->period_label }}</strong>
                                </div>
                            </div>

                            <div class="p-3 bg-body-tertiary rounded mb-3">
                                <div class="d-flex justify-content-between mb-1 small">
                                    <span class="text-muted">إجمالي الفاتورة:</span>
                                    <span>{{ number_format((float) $activeReceipt->invoice?->total, 2) }} ج.م</span>
                                </div>
                                <div class="d-flex justify-content-between border-top pt-2 mt-2">
                                    <strong>المدفوع:</strong>
                                    <strong
                                        class="text-success fs-5">{{ number_format((float) $activeReceipt->amount, 2) }}
                                        ج.م</strong>
                                </div>
                                @if($activeReceipt->invoice?->remaining_amount > 0)
                                    <div class="d-flex justify-content-between small mt-1">
                                        <span class="text-muted">المتبقي:</span>
                                        <span class="text-danger">{{ number_format((float) $activeReceipt->invoice->remaining_amount, 2) }} ج.م</span>
                                    </div>
                                @else
                                    <div
                                        class="text-center mt-2 p-2 bg-success-subtle text-success-emphasis rounded small fw-bold">
                                        ✓ مدفوع بالكامل
                                    </div>
                                @endif
                            </div>

                            <div class="text-center pt-3 border-top">
                                {!! DNS2D::getBarcodeHTML(route('teacher.payments.verify', $activeReceipt->receipt_number), 'QRCODE', 2.5, 2.5) !!}
                                <small class="text-muted d-block mt-1">امسح للتحقق</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

    @endif
</div>
