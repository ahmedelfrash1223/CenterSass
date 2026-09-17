<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
    <meta charset="UTF-8">
    <title>إيصال دفع - {{ $payment->receipt_number }}</title>
    <style>
        @page {
            margin: 10mm;
            size: 80mm auto;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'DejaVu Sans', sans-serif;
            direction: rtl;
            font-size: 11px;
            color: #212529;
            background: #ffffff;
            padding: 8mm;
        }

        .receipt-header {
            text-align: center;
            padding-bottom: 10px;
            border-bottom: 2px solid #212529;
            margin-bottom: 12px;
        }

        .shop-name {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 4px;
            color: #212529;
        }

        .shop-phone {
            font-size: 10px;
            color: #6c757d;
        }

        .receipt-title {
            font-size: 13px;
            font-weight: bold;
            color: #0d6efd;
            margin-top: 8px;
        }

        .receipt-number {
            font-size: 10px;
            color: #495057;
            margin-top: 4px;
        }

        .divider {
            border-top: 1px dashed #999;
            margin: 10px 0;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            padding: 3px 0;
            font-size: 10px;
        }

        .info-row span:first-child {
            color: #6c757d;
        }

        .info-row strong,
        .info-row span:last-child {
            font-weight: bold;
            color: #212529;
        }

        .barcode-block {
            text-align: center;
            padding: 10px 0;
            background: #ffffff;
        }

        .barcode-block svg {
            max-width: 100%;
            height: auto;
            background: #ffffff;
        }

        .barcode-value {
            font-family: monospace;
            font-size: 10px;
            letter-spacing: 1.5px;
            margin-top: 4px;
            color: #212529;
        }

        .amount-section {
            background: #f5f5f5;
            padding: 10px;
            border-radius: 6px;
            margin: 10px 0;
        }

        .amount-total {
            display: flex;
            justify-content: space-between;
            padding-top: 8px;
            margin-top: 8px;
            border-top: 2px solid #ccc;
            font-size: 13px;
            font-weight: bold;
            color: #198754;
        }

        .paid-stamp {
            text-align: center;
            padding: 8px;
            background: #e7f5eb;
            color: #198754;
            border-radius: 6px;
            font-weight: bold;
            font-size: 11px;
            margin-top: 8px;
        }

        .qr-block {
            text-align: center;
            margin: 12px 0;
            background: #ffffff;
        }

        .qr-block svg {
            max-width: 100px;
            height: auto;
            background: #ffffff;
        }

        .qr-caption {
            font-size: 9px;
            color: #6c757d;
            margin-top: 4px;
        }

        .footer {
            text-align: center;
            font-size: 9px;
            color: #6c757d;
            margin-top: 14px;
            padding-top: 8px;
            border-top: 1px dashed #ccc;
        }
    </style>
</head>
<body>

<div class="receipt-header">
    <div class="shop-name">{{ $payment->teacher?->name ?? 'مركز تعليمي' }}</div>
    @if($payment->teacher?->phone)
        <div class="shop-phone">هاتف: {{ $payment->teacher->phone }}</div>
    @endif
    <div class="receipt-title">إيصال دفع</div>
    <div class="receipt-number">رقم الإيصال: {{ $payment->receipt_number }}</div>
</div>

<div class="info-row">
    <span>الطالب:</span>
    <strong>{{ $payment->student?->name }}</strong>
</div>
<div class="info-row">
    <span>الكود:</span>
    <strong>{{ $payment->student?->code }}</strong>
</div>
@if($payment->student?->branch)
    <div class="info-row">
        <span>الفرع:</span>
        <strong>{{ $payment->student->branch->name }}</strong>
    </div>
@endif

<div class="barcode-block">
    {!! DNS1D::getBarcodeHTML($payment->student?->barcode_digits ?? '0', 'C128', 1.2, 35) !!}
    <div class="barcode-value">{{ $payment->student?->barcode_digits }}</div>
</div>

<div class="divider"></div>

<div class="info-row">
    <span>التاريخ:</span>
    <strong>{{ $payment->paid_at?->format('Y-m-d') }}</strong>
</div>
<div class="info-row">
    <span>الوقت:</span>
    <strong>{{ $payment->paid_time?->format('H:i') ?? $payment->created_at->format('H:i') }}</strong>
</div>
<div class="info-row">
    <span>طريقة الدفع:</span>
    <strong>{{ $payment->payment_method_label }}</strong>
</div>
@if($payment->reference)
    <div class="info-row">
        <span>المرجع:</span>
        <strong>{{ $payment->reference }}</strong>
    </div>
@endif
<div class="info-row">
    <span>الشهر:</span>
    <strong>{{ $payment->invoice?->period_label }}</strong>
</div>

<div class="divider"></div>

<div class="amount-section">
    <div class="info-row">
        <span>إجمالي الفاتورة:</span>
        <span>{{ number_format((float) $payment->invoice?->total, 2) }} ج.م</span>
    </div>
    <div class="info-row">
        <span>المدفوع سابقاً:</span>
        <span>{{ number_format((float) $payment->invoice?->paid_amount - $payment->amount, 2) }} ج.م</span>
    </div>
    <div class="amount-total">
        <span>المبلغ المدفوع:</span>
        <span>{{ number_format((float) $payment->amount, 2) }} ج.م</span>
    </div>
    @if($payment->invoice?->remaining_amount > 0)
        <div class="info-row" style="margin-top: 6px;">
            <span>المتبقي:</span>
            <span style="color: #dc3545;">{{ number_format((float) $payment->invoice->remaining_amount, 2) }} ج.م</span>
        </div>
    @else
        <div class="paid-stamp">✓ مدفوع بالكامل</div>
    @endif
</div>

<div class="qr-block">
    {!! DNS2D::getBarcodeHTML(route('teacher.payments.verify', $payment->receipt_number), 'QRCODE', 2.5, 2.5) !!}
    <div class="qr-caption">امسح للتحقق</div>
</div>

<div class="footer">
    تم التسجيل بواسطة: {{ $payment->creator?->name ?? '—' }}
    <br>
    {{ now()->format('Y-m-d H:i A') }}
</div>

</body>
</html>
