<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>التحقق من الإيصال - {{ $payment->receipt_number }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/teacherPanel') }}/css/verify.css" id="main-style-link">
    <link rel="icon" href="{{ asset('assets/teacherPanel/images/favicon.png') }}" type="image/x-icon"/>
</head>
<body>

<div class="page-wrap">


    {{-- Success Card --}}
    <div class="success-card">

        {{-- Animated Check --}}
        <div class="check-wrap">
            <div class="check-ring"></div>
            <div class="check-ring check-ring--2"></div>
            <div class="check-ring check-ring--3"></div>

            <div class="check-circle">
                <svg class="check-svg" viewBox="0 0 52 52">
                    <path d="M14 27 L22 35 L38 19"/>
                </svg>
            </div>
        </div>

        {{-- Text --}}
        <h1 class="success-title">عملية موثوقة</h1>
        <p class="success-subtitle">
            تم التحقق من الإيصال بنجاح. اضغط تحت لعرض التفاصيل.
        </p>

        {{-- Show Receipt Button --}}
        <button type="button" id="show-receipt-btn" class="btn btn--primary">
            <svg class="btn__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"
                 stroke-linecap="round" stroke-linejoin="round">
                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                <polyline points="14 2 14 8 20 8"></polyline>
                <line x1="9" y1="13" x2="15" y2="13"></line>
                <line x1="9" y1="17" x2="15" y2="17"></line>
            </svg>
            عرض الإيصال
        </button>
    </div>

    {{-- Receipt (hidden by default) --}}
    <div class="receipt-container" id="receipt-container">

        <div class="receipt-paper">

            <div class="receipt-header">
                <h3 class="receipt-shop-name">{{ $payment->teacher?->name ?? 'مركز تعليمي' }}</h3>
                @if($payment->teacher?->phone)
                    <p class="receipt-shop-phone">{{ $payment->teacher->phone }}</p>
                @endif
                <div class="receipt-divider"></div>
                <h4 class="receipt-title">إيصال دفع</h4>
                <p class="receipt-number">رقم الإيصال: <strong>{{ $payment->receipt_number }}</strong></p>
            </div>

            <div class="receipt-section">
                <div class="receipt-info-row"><span>الطالب:</span><strong>{{ $payment->student?->name }}</strong></div>
                <div class="receipt-info-row"><span>الكود:</span><strong>{{ $payment->student?->code }}</strong></div>
                @if($payment->student?->branch)
                    <div class="receipt-info-row">
                        <span>الفرع:</span><strong>{{ $payment->student->branch->name }}</strong></div>
                @endif
            </div>

            @if($payment->student?->barcode_digits)
                <div class="receipt-barcode">
                    {!! DNS1D::getBarcodeSVG($payment->student->barcode_digits, 'C128', 1.5, 40) !!}
                    <div class="barcode-value">{{ $payment->student->barcode_digits }}</div>
                </div>
            @endif

            <div class="receipt-divider-dashed"></div>

            <div class="receipt-section">
                <div class="receipt-info-row">
                    <span>التاريخ:</span><strong>{{ $payment->paid_at?->format('Y-m-d') }}</strong></div>
                <div class="receipt-info-row">
                    <span>الوقت:</span><strong>{{ $payment->paid_time?->format('H:i') ?? $payment->created_at->format('H:i') }}</strong>
                </div>
                <div class="receipt-info-row">
                    <span>طريقة الدفع:</span><strong>{{ $payment->payment_method_label }}</strong></div>
                @if($payment->reference)
                    <div class="receipt-info-row"><span>المرجع:</span><strong>{{ $payment->reference }}</strong></div>
                @endif
                <div class="receipt-info-row"><span>الشهر:</span><strong>{{ $payment->invoice?->period_label }}</strong>
                </div>
            </div>

            <div class="receipt-divider-dashed"></div>

            {{-- تفاصيل البنود --}}
            @php
                $breakdown = [];
                foreach ($payment->allocations as $alloc) {
                    $item = $alloc->invoiceItem;
                    $breakdown[$alloc->invoice_item_id] = [
                        'description'     => $item?->description ?? '—',
                        'amount'          => (float) $alloc->amount,
                        'discount_amount' => (float) ($item?->discount_amount ?? 0),
                        'discount_type'   => $item?->discount_type,
                        'discount_value'  => (float) ($item?->discount_value ?? 0),
                        'discount_reason' => $item?->discount_reason,
                    ];
                }
            @endphp

            @if(!empty($breakdown))
                <div class="receipt-section">
                    <div class="receipt-info-row"
                         style="font-weight: bold; border-bottom: 1px solid #cbd5e1; padding-bottom: 6px; margin-bottom: 6px;">
                        <span>المجموعة</span>
                        <span>المبلغ</span>
                    </div>

                    @foreach($breakdown as $entry)
                        <div class="receipt-info-row">
                            <span>{{ $entry['description'] }}</span>
                            <strong>{{ number_format($entry['amount'], 2) }} ج.م</strong>
                        </div>

                        @if(!empty($entry['discount_amount']) && $entry['discount_amount'] > 0)
                            <div class="receipt-info-row" style="color: #10b981; font-size: 11px; padding-right: 8px;">
                                <span>
                                    خصم
                                    @if(($entry['discount_type'] ?? '') === 'percentage')
                                        ({{ number_format($entry['discount_value'], 2) }}%)
                                    @else
                                        (ثابت)
                                    @endif
                                    :
                                </span>
                                <span>- {{ number_format($entry['discount_amount'], 2) }} ج.م</span>
                            </div>

                            @if(!empty($entry['discount_reason']))
                                <div class="receipt-info-row"
                                     style="color: #64748b; font-size: 11px; padding-right: 8px;">
                                    <span>سبب الخصم:</span>
                                    <span style="font-style: italic;">{{ $entry['discount_reason'] }}</span>
                                </div>
                            @endif
                        @endif
                    @endforeach
                </div>
                <div class="receipt-divider-dashed"></div>
            @endif

            {{-- الإجماليات --}}
            <div class="receipt-amount-section">
                <div class="receipt-info-row">
                    <span>إجمالي الفاتورة:</span>
                    <span>{{ number_format((float) $payment->invoice?->total, 2) }} ج.م</span>
                </div>

                @if($payment->invoice?->has_discount)
                    <div class="receipt-info-row" style="color: #10b981;">
                        <span>إجمالي الخصم:</span>
                        <span>- {{ number_format((float) $payment->invoice->discount, 2) }} ج.م</span>
                    </div>
                @endif

                <div class="receipt-info-row">
                    <span>المدفوع سابقاً:</span>
                    <span>{{ number_format((float) $payment->invoice?->paid_amount - $payment->amount, 2) }} ج.م</span>
                </div>

                <div class="receipt-amount-total">
                    <span>المبلغ المدفوع الآن:</span>
                    <strong>{{ number_format((float) $payment->amount, 2) }} ج.م</strong>
                </div>

                @if($payment->invoice?->remaining_amount > 0)
                    <div class="receipt-info-row mt-2">
                        <span>المتبقي:</span>
                        <span style="color: #ef4444;">{{ number_format((float) $payment->invoice->remaining_amount, 2) }} ج.م</span>
                    </div>
                @else
                    <div class="receipt-paid-stamp">✓ مدفوع بالكامل</div>
                @endif
            </div>

            {{-- QR Code --}}
            <div class="receipt-qr">
                {!! DNS2D::getBarcodeSVG(route('teacher.payments.verify', $payment->receipt_number), 'QRCODE', 3, 3) !!}
                <p class="qr-caption">امسح للتحقق</p>
            </div>

            <div class="receipt-divider-dashed"></div>

            <div class="receipt-footer">
                <p>تم التسجيل بواسطة: {{ $payment->creator?->name ?? '—' }}</p>
                <p>{{ now()->format('Y-m-d H:i A') }}</p>
            </div>

        </div>

    </div>

</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const showBtn = document.getElementById('show-receipt-btn');
        const container = document.getElementById('receipt-container');

        showBtn.addEventListener('click', function () {
            container.classList.add('is-open');

            // اخفي الزرار الأول
            showBtn.style.display = 'none';

            // scroll للـ receipt
            setTimeout(function () {
                container.scrollIntoView({behavior: 'smooth', block: 'start'});
            }, 300);
        });
    });
</script>

</body>
</html>
