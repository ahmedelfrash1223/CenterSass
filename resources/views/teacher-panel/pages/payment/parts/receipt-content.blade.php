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
        <div class="receipt-info-row"><span>الفرع:</span><strong>{{ $payment->student->branch->name }}</strong></div>
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
    <div class="receipt-info-row"><span>التاريخ:</span><strong>{{ $payment->paid_at?->format('Y-m-d') }}</strong></div>
    <div class="receipt-info-row">
        <span>الوقت:</span><strong>{{ $payment->paid_time?->format('H:i') ?? $payment->created_at->format('H:i') }}</strong>
    </div>
    <div class="receipt-info-row"><span>طريقة الدفع:</span><strong>{{ $payment->payment_method_label }}</strong></div>
    @if($payment->reference)
        <div class="receipt-info-row"><span>المرجع:</span><strong>{{ $payment->reference }}</strong></div>
    @endif
    <div class="receipt-info-row"><span>الشهر:</span><strong>{{ $payment->invoice?->period_label }}</strong></div>
</div>

<div class="receipt-divider-dashed"></div>

@if(!empty($breakdown))
    <div class="receipt-section">
        <div class="receipt-info-row" style="font-weight: bold; border-bottom: 1px solid #ccc; padding-bottom: 4px; margin-bottom: 4px;">
            <span>المجموعة</span>
            <span>المبلغ</span>
        </div>
        @foreach($breakdown as $entry)
            <div class="receipt-info-row">
                <span>{{ $entry['description'] }}</span>
                <strong>{{ number_format($entry['amount'], 2) }} ج.م</strong>
            </div>
            @if(!empty($entry['discount']) && $entry['discount'] > 0)
                <div class="receipt-info-row" style="color: #198754; font-size: 9px;">
                    <span>← خصم مطبّق على البند</span>
                    <span>- {{ number_format($entry['discount'], 2) }} ج.م</span>
                </div>
            @endif
        @endforeach
    </div>
    <div class="receipt-divider-dashed"></div>
@endif
<div class="receipt-amount-section">
    <div class="receipt-info-row">
        <span>الإجمالي قبل الخصم:</span>
        <span>{{ number_format((float) $payment->invoice?->subtotal, 2) }} ج.م</span>
    </div>

    @if($payment->invoice?->has_discount)
        <div class="receipt-info-row">
            <span>
                الخصم
                @if($payment->invoice->discount_type === 'percentage')
                    ({{ (float) $payment->invoice->discount_value }}%)
                @endif:
            </span>
            <span style="color: #198754;">
                - {{ number_format((float) $payment->invoice->discount, 2) }} ج.م
            </span>
        </div>
        @if($payment->invoice->discount_reason)
            <div class="receipt-info-row">
                <span>سبب الخصم:</span>
                <span style="font-size: 9px;">{{ $payment->invoice->discount_reason }}</span>
            </div>
        @endif
        <div class="receipt-info-row" style="font-weight: bold;">
            <span>الإجمالي بعد الخصم:</span>
            <span>{{ number_format((float) $payment->invoice->total, 2) }} ج.م</span>
        </div>
    @endif

    <div class="receipt-info-row mt-2">
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
            <span class="text-danger">{{ number_format((float) $payment->invoice->remaining_amount, 2) }} ج.م</span>
        </div>
    @else
        <div class="receipt-paid-stamp mt-2">✓ مدفوع بالكامل</div>
    @endif
</div>

<div class="receipt-qr">
    {!! DNS2D::getBarcodeSVG(route('teacher.payments.verify', $payment->receipt_number), 'QRCODE', 3, 3) !!}
    <p class="qr-caption">امسح للتحقق</p>
</div>

<div class="receipt-divider-dashed"></div>

<div class="receipt-footer">
    <p>تم التسجيل بواسطة: {{ $payment->creator?->name ?? '—' }}</p>
    <p>{{ now()->format('Y-m-d H:i A') }}</p>
</div>
