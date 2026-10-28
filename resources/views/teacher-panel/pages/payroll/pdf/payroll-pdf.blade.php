<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
    <meta charset="UTF-8">
    <title>كشف حساب {{ $payroll->reference }}</title>
    <style>
        @page {
            margin: 12mm;
        }

        body {
            font-family: dejavusans, sans-serif;
            font-size: 10px;
            color: #222;
            direction: rtl;
            line-height: 1.5;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        .print-info {
            margin-bottom: 12px;
        }

        .print-info td {
            border: none;
            padding: 2px 0;
            text-align: right;
            font-size: 10px;
        }

        .print-info .left {
            text-align: left;
        }

        .document-title {
            text-align: center;
            border-top: 1px solid #444;
            border-bottom: 2px solid #444;
            padding: 8px 0;
            margin-bottom: 12px;
        }

        .document-title h1 {
            font-size: 19px;
            margin: 0 0 4px;
            color: #222;
        }

        .document-title p {
            font-size: 10px;
            margin: 0;
            color: #555;
        }

        .details {
            border: 1px solid #888;
            padding: 5px;
            margin-bottom: 12px;
        }

        .details td {
            border: none;
            padding: 4px 6px;
            text-align: right;
            width: 50%;
        }

        .section-title {
            font-size: 11px;
            font-weight: bold;
            background-color: #eee;
            border: 1px solid #888;
            padding: 5px 7px;
            margin: 12px 0 5px;
        }

        .data-table {
            margin-bottom: 10px;
        }

        .data-table th,
        .data-table td {
            border: 1px solid #999;
            padding: 5px 6px;
            text-align: center;
            vertical-align: middle;
        }

        .data-table th {
            background-color: #eee;
            color: #222;
            font-weight: bold;
        }

        .data-table thead {
            display: table-header-group;
        }

        .data-table tr {
            page-break-inside: avoid;
        }

        .data-table .text-right {
            text-align: right;
        }

        .total-row td {
            font-weight: bold;
            background-color: #eee;
            border-top: 2px solid #555;
            font-size: 11px;
        }

        .amount {
            direction: ltr;
            white-space: nowrap;
        }

        .badge-success,
        .badge-danger {
            background: transparent;
            color: #222;
            padding: 0;
            border-radius: 0;
            font-size: 10px;
        }

        .signatures {
            margin-top: 25px;
        }

        .signatures td {
            border: none;
            width: 50%;
            text-align: center;
            padding: 5px;
        }

        .signature-line {
            border-top: 1px dotted #777;
            width: 65%;
            margin: 28px auto 0;
        }

        .document-end {
            border-top: 1px solid #999;
            text-align: center;
            padding-top: 6px;
            margin-top: 15px;
            font-size: 9px;
            color: #555;
        }
    </style>
</head>

<body>

{{-- Print Information --}}
<table class="print-info">
    <tr>
        <td>
            <strong>المدرس:</strong>
            {{ $payroll->teacher?->name ?? '—' }}
        </td>
        <td class="left">
            <strong>تاريخ الطباعة:</strong>
            {{ now()->format('Y/m/d h:i A') }}
        </td>
    </tr>
    <tr>
        <td>
            <strong>الفرع:</strong>
            {{ $payroll->branch?->name ?? 'كل الفروع' }}
        </td>
        <td class="left">
            <strong>رقم الكشف:</strong>
            {{ $payroll->reference }}
        </td>
    </tr>
</table>

{{-- Document Title --}}
<div class="document-title">
    <h1>كشف حساب مستحقات المدرس</h1>
    <p>بيان تفصيلي بالمستحقات والخصومات والدفعات</p>
</div>

{{-- Payroll Details --}}
<div class="details">
    <table>
        <tr>
            <td>
                <strong>الفترة:</strong>
                {{ $payroll->period_label }}
            </td>
            <td>
                <strong>نوع الأجر:</strong>
                {{ $payroll->payment_type->label() }}
            </td>
        </tr>
        <tr>
            <td>
                <strong>الحالة:</strong>
                {{ $payroll->status->label() }}
            </td>
            <td>
                <strong>العملة:</strong>
                جنيه مصري
            </td>
        </tr>
    </table>
</div>

{{-- Sessions Statistics --}}
<div class="section-title">أولاً: تفاصيل الحصص</div>

<table class="data-table">
    <thead>
    <tr>
        <th>إجمالي الحصص</th>
        <th>مكتملة</th>
        <th>ملغية</th>
        <th>لم تُدرّس</th>
        <th>إجمالي الساعات</th>
    </tr>
    </thead>
    <tbody>
    <tr>
        <td>{{ $stats['total'] }}</td>
        <td>{{ $stats['completed'] }}</td>
        <td>{{ $stats['cancelled'] }}</td>
        <td>{{ $stats['upcoming'] }}</td>
        <td>{{ number_format((float) $stats['hours_total'], 1) }} س</td>
    </tr>
    </tbody>
</table>

{{-- Amounts --}}
<div class="section-title">ثانياً: تفاصيل المبالغ</div>

<table class="data-table">
    <thead>
    <tr>
        <th style="width:65%;">البند</th>
        <th style="width:35%;">المبلغ (ج.م)</th>
    </tr>
    </thead>
    <tbody>
    <tr>
        <td class="text-right">المبلغ الكامل</td>
        <td class="amount">{{ number_format((float) $amounts['full'], 2) }}</td>
    </tr>

    @if($amounts['cancellation_deduction'] > 0)
        <tr>
            <td class="text-right">خصم الحصص الملغية</td>
            <td class="amount">- {{ number_format((float) $amounts['cancellation_deduction'], 2) }}</td>
        </tr>
    @endif

    <tr>
        <td class="text-right">البونص</td>
        <td class="amount">+ {{ number_format((float) $amounts['bonus'], 2) }}</td>
    </tr>
    <tr>
        <td class="text-right">الخصومات</td>
        <td class="amount">- {{ number_format((float) $amounts['deductions'], 2) }}</td>
    </tr>
    <tr class="total-row">
        <td class="text-right">الصافي المستحق</td>
        <td class="amount">{{ number_format((float) $amounts['net'], 2) }}</td>
    </tr>
    <tr>
        <td class="text-right">المدفوع</td>
        <td class="amount">{{ number_format((float) $amounts['paid'], 2) }}</td>
    </tr>
    <tr>
        <td class="text-right">المتبقي</td>
        <td class="amount">{{ number_format((float) $amounts['remaining'], 2) }}</td>
    </tr>
    </tbody>
</table>

{{-- Bonuses and Deductions --}}
@if($payroll->bonuses->count() > 0 || $payroll->deductionsItems->count() > 0)
    <div class="section-title">ثالثاً: البنود الإضافية</div>

    <table class="data-table">
        <thead>
        <tr>
            <th>النوع</th>
            <th>السبب</th>
            <th>المبلغ</th>
            <th>التاريخ</th>
        </tr>
        </thead>
        <tbody>
        @foreach($payroll->bonuses as $bonus)
            <tr>
                <td>بونص</td>
                <td class="text-right">{{ $bonus->reason }}</td>
                <td class="amount">+ {{ number_format((float) $bonus->amount, 2) }}</td>
                <td>{{ $bonus->created_at?->format('Y/m/d') ?? '—' }}</td>
            </tr>
        @endforeach

        @foreach($payroll->deductionsItems as $deduction)
            <tr>
                <td>خصم</td>
                <td class="text-right">{{ $deduction->reason }}</td>
                <td class="amount">- {{ number_format((float) $deduction->amount, 2) }}</td>
                <td>{{ $deduction->created_at?->format('Y/m/d') ?? '—' }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
@endif

{{-- Payments --}}
@if($payroll->payments->count() > 0)
    <div class="section-title">رابعاً: سجل الدفعات</div>

    <table class="data-table">
        <thead>
        <tr>
            <th>#</th>
            <th>الرقم</th>
            <th>الطريقة</th>
            <th>المبلغ</th>
            <th>التاريخ</th>
        </tr>
        </thead>
        <tbody>
        @foreach($payroll->payments as $i => $payment)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ $payment->reference }}</td>
                <td>{{ $payment->payment_method }}</td>
                <td class="amount">{{ number_format((float) $payment->amount, 2) }}</td>
                <td>{{ $payment->paid_at?->format('Y/m/d H:i') ?? '—' }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
@endif

{{-- Signatures --}}
<table class="signatures">
    <tr>
        <td>
            <strong>توقيع المدرس</strong>
            <div class="signature-line"></div>
        </td>
        <td>
            <strong>اعتماد الإدارة</strong>
            <div class="signature-line"></div>
        </td>
    </tr>
</table>

<div class="document-end">
    نهاية كشف الحساب — تاريخ الإصدار: {{ now()->format('Y/m/d') }}
</div>

</body>
</html>
