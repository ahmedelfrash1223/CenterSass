<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <title>كشف الصادر - {{ $closing->month }}</title>
    <style>
        @page {
            margin: 12mm;
        }

        * {
            box-sizing: border-box;
        }

        body {
            font-family: dejavusans, sans-serif;
            color: #222;
            font-size: 10px;
            line-height: 1.5;
            direction: rtl;
        }

        .header {
            border-bottom: 2px solid #444;
            padding-bottom: 10px;
            margin-bottom: 14px;
        }

        .header h1 {
            font-size: 19px;
            color: #222;
            margin: 0 0 8px;
            text-align: center;
        }

        .header .meta {
            width: 100%;
            border-collapse: collapse;
        }

        .header .meta td {
            border: none;
            padding: 3px 0;
            text-align: right;
            font-size: 10px;
            width: 50%;
        }

        .header .meta td.left {
            text-align: left;
        }

        .details {
            border: 1px solid #999;
            padding: 7px 9px;
            margin-bottom: 12px;
        }

        .details table {
            width: 100%;
            border-collapse: collapse;
        }

        .details td {
            border: none;
            padding: 3px;
            text-align: right;
        }

        table.list {
            width: 100%;
            border-collapse: collapse;
            font-size: 9.5px;
            margin-bottom: 12px;
        }

        table.list thead {
            display: table-header-group;
        }

        table.list th,
        table.list td {
            border: 1px solid #999;
            padding: 6px 5px;
            text-align: right;
            vertical-align: middle;
        }

        table.list thead th {
            background: #eee;
            color: #222;
            font-weight: bold;
            text-align: center;
        }

        table.list tbody tr {
            page-break-inside: avoid;
        }

        .total-row td {
            background: #eee;
            font-weight: bold;
            font-size: 10.5px;
            border-top: 2px solid #555;
        }

        .amount {
            text-align: left !important;
            direction: ltr;
            white-space: nowrap;
        }

        .center {
            text-align: center !important;
        }

        .footer {
            position: fixed;
            bottom: -5mm;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 8px;
            color: #666;
            border-top: 1px solid #999;
            padding-top: 4px;
        }
    </style>
</head>
<body>

{{-- Document Header --}}
<div class="header">
    <h1>كشف الصادر</h1>

    <table class="meta">
        <tr>
            <td>
                <strong>رقم التقفيل:</strong>
                {{ $closing->reference }}
            </td>
            <td class="left">
                <strong>تاريخ الطباعة:</strong>
                {{ now()->format('Y/m/d h:i A') }}
            </td>
        </tr>
        <tr>
            <td>
                <strong>الشهر:</strong>
                {{ $closing->month }}
            </td>
            <td class="left">
                <strong>تاريخ التقفيل:</strong>
                {{ $closing->closed_at?->format('Y/m/d') ?? '—' }}
            </td>
        </tr>
    </table>
</div>

{{-- Closing Details --}}
<div class="details">
    <table>
        <tr>
            <td>
                <strong>المدرس:</strong>
                {{ $closing->teacher?->name ?? '—' }}
            </td>
            <td>
                <strong>الفرع:</strong>
                {{ $closing->branch?->name ?? 'كل الفروع' }}
            </td>
        </tr>
    </table>
</div>

{{-- Expenses List --}}
<table class="list">
    <thead>
    <tr>
        <th style="width:30px;">#</th>
        <th>الرقم</th>
        <th>المصدر</th>
        <th>الوصف / السبب</th>
        <th style="width:90px;">المبلغ</th>
        <th style="width:80px;">التاريخ</th>
    </tr>
    </thead>
    <tbody>
    @forelse($items as $i => $item)
        @php
            $enum = \App\Enums\Teacher\ExpenseSourceEnum::tryFrom($item->source_type);
        @endphp
        <tr>
            <td class="center">{{ $i + 1 }}</td>
            <td>{{ $item->reference }}</td>
            <td>{{ $enum?->label() ?? $item->source_type }}</td>
            <td>{{ $item->description ?: '—' }}</td>
            <td class="amount">{{ number_format($item->amount, 2) }} ج.م</td>
            <td class="center">{{ $item->occurred_at?->format('Y/m/d') ?? '—' }}</td>
        </tr>
    @empty
        <tr>
            <td colspan="6" class="center" style="padding:15px;">
                لا توجد بنود
            </td>
        </tr>
    @endforelse

    <tr class="total-row">
        <td colspan="4" style="text-align:right;">الإجمالي</td>
        <td class="amount">{{ number_format($closing->total_expense, 2) }} ج.م</td>
        <td></td>
    </tr>
    </tbody>
</table>

{{-- Footer --}}
<div class="footer">
    كشف صادر رسمي — تم إنشاء المستند إلكترونيًا بتاريخ {{ now()->format('Y/m/d') }}
</div>

</body>
</html>
