<div>

    {{-- أزرار الأكشن --}}
    @livewire('teacher-panel.pages.invoice.parts.invoice-actions', ['invoice' => $invoice], key('actions-' . $invoice->id))

    <div class="row g-4">

        {{-- Invoice Details --}}
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-body">
                    <h6 class="mb-0 fw-bold">بيانات الفاتورة</h6>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-sm-6">
                            <small class="text-body-secondary d-block mb-1">رقم الفاتورة</small>
                            <span class="fw-semibold small">{{ $invoice->invoice_number }}</span>
                        </div>
                        <div class="col-sm-6">
                            <small class="text-body-secondary d-block mb-1">الحالة</small>
                            <span class="badge bg-{{ $invoice->status_color }}-subtle text-{{ $invoice->status_color }}-emphasis">
                                {{ $invoice->status_label }}
                            </span>
                        </div>
                        <div class="col-sm-6">
                            <small class="text-body-secondary d-block mb-1">الشهر</small>
                            <span class="fw-semibold small">{{ $invoice->period_label }}</span>
                        </div>
                        <div class="col-sm-6">
                            <small class="text-body-secondary d-block mb-1">الفترة</small>
                            <span class="small">
                                {{ $invoice->period_start?->format('Y-m-d') }}
                                →
                                {{ $invoice->period_end?->format('Y-m-d') }}
                            </span>
                        </div>
                        <div class="col-sm-6">
                            <small class="text-body-secondary d-block mb-1">تاريخ الإنشاء</small>
                            <span class="small">{{ $invoice->created_at?->format('Y-m-d h:i A') }}</span>
                        </div>
                        <div class="col-sm-6">
                            <small class="text-body-secondary d-block mb-1">أنشأها</small>
                            <span class="small">{{ $invoice->creator?->name ?? '—' }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Amounts --}}
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-body">
                    <h6 class="mb-0 fw-bold">المبالغ</h6>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-body-secondary">الإجمالي قبل الخصم:</span>
                        <strong>{{ number_format((float) $invoice->subtotal, 2) }} ج.م</strong>
                    </div>

                    @if($invoice->has_discount)
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-body-secondary">
                                <i class="ph-duotone ph-tag me-1 text-success"></i>
                                الخصم
                                <small>
                                    ({{ $invoice->discount_type_label }}
                                    @if($invoice->discount_type === 'percentage')
                                        {{ (float) $invoice->discount_value }}%
                                    @endif
                                </small>:
                            </span>
                            <strong class="text-success">
                                - {{ number_format((float) $invoice->discount, 2) }} ج.م
                            </strong>
                        </div>
                        @if($invoice->discount_reason)
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-body-secondary small">
                                    <i class="ph-duotone ph-chat-text me-1"></i>
                                    سبب الخصم:
                                </span>
                                <span class="small">{{ $invoice->discount_reason }}</span>
                            </div>
                        @endif
                    @endif

                    <div class="d-flex justify-content-between mb-2 pt-2 border-top">
                        <span class="text-body-secondary fw-semibold">الإجمالي النهائي:</span>
                        <strong>{{ number_format((float) $invoice->total, 2) }} ج.م</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-body-secondary">المدفوع:</span>
                        <strong class="text-success">{{ number_format((float) $invoice->paid_amount, 2) }} ج.م</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-3">
                        <span class="text-body-secondary">المتبقي:</span>
                        <strong class="text-danger">{{ number_format((float) $invoice->remaining_amount, 2) }} ج.م</strong>
                    </div>

                    @php
                        $percentage = $invoice->total > 0 ? ($invoice->paid_amount / $invoice->total) * 100 : 0;
                    @endphp
                    <div class="progress" style="height: 8px;">
                        <div class="progress-bar bg-{{ $invoice->status_color }}"
                             style="width: {{ min(100, $percentage) }}%"></div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Student Info --}}
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-body">
                    <h6 class="mb-0 fw-bold">الطالب</h6>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div class="rounded-circle text-white d-flex align-items-center justify-content-center fw-bold flex-shrink-0"
                             style="width: 56px; height: 56px; background: var(--bs-primary);">
                            {{ mb_substr($invoice->student?->name ?? '?', 0, 1) }}
                        </div>
                        <div>
                            <h6 class="mb-1 fw-bold">{{ $invoice->student?->name }}</h6>
                            <small class="text-body-secondary d-block">{{ $invoice->student?->code }}</small>
                        </div>
                    </div>

                    @if($invoice->student?->email)
                        <div class="small mb-2">
                            <i class="ph-duotone ph-envelope me-1 text-body-secondary"></i>
                            {{ $invoice->student->email }}
                        </div>
                    @endif

                    @if($invoice->student?->branch)
                        <div class="small">
                            <i class="ph-duotone ph-git-branch me-1 text-body-secondary"></i>
                            {{ $invoice->student->branch->name }}
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Invoice Items --}}
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-body d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-bold">بنود الفاتورة</h6>
                    <span class="badge bg-primary-subtle text-primary-emphasis">
                        {{ $invoice->items->count() }} بند
                    </span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                            <tr>
                                <th class="ps-4">#</th>
                                <th>البيان</th>
                                <th>النوع</th>
                                <th class="text-center">الكمية</th>
                                <th class="text-center">سعر الوحدة</th>
                                <th class="text-center">الإجمالي</th>
                                @if($invoice->has_discount)
                                    <th class="text-center">الخصم</th>
                                    <th class="text-center">الصافي</th>
                                @endif
                                <th class="text-center">المدفوع</th>
                                <th class="text-center">المتبقي</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($invoice->items as $index => $item)
                                <tr>
                                    <td class="ps-4">{{ $index + 1 }}</td>
                                    <td>
                                        <span class="fw-semibold small">{{ $item->description }}</span>
                                        @if($item->group)
                                            <small class="text-body-secondary d-block">{{ $item->group->name }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary-subtle text-secondary-emphasis">
                                            {{ $item->pricing_type_label }}
                                        </span>
                                    </td>
                                    <td class="text-center">{{ (int) $item->quantity }}</td>
                                    <td class="text-center">{{ number_format((float) $item->unit_price, 2) }}</td>
                                    <td class="text-center fw-semibold">{{ number_format((float) $item->total, 2) }}</td>
                                    @if($invoice->has_discount)
                                        <td class="text-center text-success">
                                            - {{ number_format((float) $item->discount_amount, 2) }}
                                        </td>
                                        <td class="text-center fw-bold">{{ number_format($item->net_total, 2) }}</td>
                                    @endif
                                    <td class="text-center text-success">
                                        {{ number_format((float) $item->paid_amount, 2) }}
                                    </td>
                                    <td class="text-center">
                                        @if((float) $item->remaining_amount > 0)
                                            <span class="text-danger fw-semibold">
                                                {{ number_format((float) $item->remaining_amount, 2) }}
                                            </span>
                                        @else
                                            <span class="text-body-secondary">—</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="10" class="text-center text-body-secondary py-4">لا توجد بنود.</td>
                                </tr>
                            @endforelse
                            </tbody>
                            <tfoot class="table-light">
                            <tr>
                                <td colspan="{{ $invoice->has_discount ? 8 : 6 }}" class="text-end fw-bold ps-4">الإجمالي النهائي:</td>
                                <td colspan="2" class="text-center fw-bold">
                                    {{ number_format((float) $invoice->total, 2) }} ج.م
                                </td>
                            </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- Payments History --}}
        @if($invoice->payments->count() > 0)
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-body d-flex justify-content-between align-items-center">
                        <h6 class="mb-0 fw-bold">سجل الدفعات</h6>
                        <span class="badge bg-success-subtle text-success-emphasis">
                            {{ $invoice->payments->count() }} دفعة
                        </span>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                <tr>
                                    <th class="ps-4">#</th>
                                    <th>رقم الإيصال</th>
                                    <th>التاريخ</th>
                                    <th>الطريقة</th>
                                    <th class="text-center">المبلغ</th>
                                    <th class="text-center">إجراءات</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($invoice->payments as $index => $payment)
                                    <tr wire:key="pay-{{ $payment->id }}">
                                        <td class="ps-4">{{ $index + 1 }}</td>
                                        <td><span class="fw-semibold small">{{ $payment->receipt_number }}</span></td>
                                        <td><small class="text-body-secondary">{{ $payment->paid_at?->format('Y-m-d') }}</small></td>
                                        <td>
                                            <span class="badge bg-primary-subtle text-primary-emphasis">
                                                <i class="ph-duotone {{ $payment->payment_method_icon }} me-1"></i>
                                                {{ $payment->payment_method_label }}
                                            </span>
                                        </td>
                                        <td class="text-center fw-bold text-success">
                                            {{ number_format((float) $payment->amount, 2) }} ج.م
                                        </td>
                                        <td class="text-center">
                                            <button type="button"
                                                    wire:click="showReceipt({{ $payment->id }})"
                                                    class="btn btn-sm btn-outline-primary">
                                                <i class="ph-duotone ph-receipt me-1"></i>الإيصال
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        {{-- Inline Receipt --}}
        @if($activePayment)
            <div class="col-12 mt-3" wire:key="inline-receipt-{{ $activePayment->id }}">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-body d-flex justify-content-between align-items-center">
                        <h6 class="mb-0 fw-bold">
                            <i class="ph-duotone ph-receipt me-1"></i>
                            الإيصال — {{ $activePayment->receipt_number }}
                        </h6>
                        <div class="d-flex gap-2">
                            <button type="button" onclick="printInlineReceipt()" class="btn btn-primary btn-sm">
                                <i class="ph-duotone ph-printer me-1"></i>طباعة
                            </button>
                            <button type="button" wire:click="closeReceipt" class="btn btn-outline-secondary btn-sm">
                                <i class="ph-duotone ph-x"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body bg-body-tertiary">
                        <div class="bg-white text-dark rounded-3 p-4 mx-auto border"
                             id="printable-inline-receipt" style="max-width: 400px;">
                            @include('teacher-panel.pages.payment.parts.receipt-content', [
                                'payment'   => $activePayment,
                                'breakdown' => $activeBreakdown,
                            ])
                        </div>
                    </div>
                </div>
            </div>

            @script
            <script>
                window.printInlineReceipt = function () {
                    const content = document.getElementById('printable-inline-receipt');
                    if (!content) return;
                    const printWindow = window.open('', '_blank', 'height=800,width=600');
                    printWindow.document.write(`
                        <!DOCTYPE html>
                        <html dir="rtl"><head><meta charset="UTF-8"><title>طباعة</title>
                        <style>
                            * { margin:0; padding:0; box-sizing:border-box; }
                            body { font-family: 'Cairo', sans-serif; padding: 8mm; direction: rtl; font-size: 11px; }
                            .receipt-header { text-align: center; padding-bottom: 10px; border-bottom: 2px solid #212529; margin-bottom: 12px; }
                            .receipt-shop-name { font-size: 16px; font-weight: bold; }
                            .receipt-title { font-size: 13px; color: #0d6efd; }
                            .receipt-divider { border-top: 2px solid #212529; margin: 10px 0; }
                            .receipt-divider-dashed { border-top: 1px dashed #999; margin: 10px 0; }
                            .receipt-info-row { display: flex; justify-content: space-between; padding: 3px 0; font-size: 10px; }
                            .receipt-info-row span:first-child { color: #6c757d; }
                            .receipt-barcode, .receipt-qr { text-align: center; margin: 10px 0; }
                            .receipt-barcode svg, .receipt-qr svg { max-width: 100%; }
                            .barcode-value, .qr-caption { font-family: monospace; font-size: 10px; }
                            .receipt-amount-section { background: #f5f5f5; padding: 10px; border-radius: 6px; margin: 10px 0; }
                            .receipt-amount-total { display: flex; justify-content: space-between; padding-top: 8px; margin-top: 8px; border-top: 2px solid #ccc; font-size: 13px; font-weight: bold; color: #198754; }
                            .receipt-paid-stamp { text-align: center; padding: 8px; background: #e7f5eb; color: #198754; border-radius: 6px; font-weight: bold; }
                            .receipt-footer { text-align: center; font-size: 9px; color: #6c757d; margin-top: 14px; padding-top: 8px; border-top: 1px dashed #ccc; }
                            @page { margin: 5mm; size: 80mm auto; }
                        </style>
                        </head><body>${content.innerHTML}</body></html>
                    `);
                    printWindow.document.close();
                    setTimeout(() => { printWindow.focus(); printWindow.print(); printWindow.close(); }, 300);
                };
            </script>
            @endscript
        @endif

    </div>
</div>
