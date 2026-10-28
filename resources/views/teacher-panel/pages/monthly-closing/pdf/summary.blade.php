<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <style>
        @page {
            margin: 12mm;
        }

        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'dejavusans', sans-serif;
            color: #222;
            font-size: 10px;
            line-height: 1.6;
            margin: 0;
        }

        .header {
            text-align: center;
            padding-bottom: 10px;
            margin-bottom: 12px;
            border-bottom: 1px solid #888;
        }

        .header h1 {
            font-size: 17px;
            font-weight: bold;
            margin: 0 0 3px;
            color: #222;
        }

        .header .subtitle {
            font-size: 10px;
            color: #666;
        }

        .info {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
        }

        .info td {
            width: 50%;
            padding: 4px 6px;
            border: 1px solid #ccc;
            font-size: 9.5px;
        }

        .label {
            color: #555;
        }

        .value {
            font-weight: bold;
        }

        .stats {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 16px;
        }

        .stats td {
            width: 33.33%;
            padding: 8px 5px;
            border: 1px solid #bbb;
            text-align: center;
        }

        .stats .label {
            font-size: 9.5px;
            color: #555;
            margin-bottom: 3px;
        }

        .stats .value {
            font-size: 13px;
            font-weight: bold;
        }

        .green {
            color: #287747;
        }

        .red {
            color: #a93226;
        }

        .blue {
            color: #333;
        }

        .section-title {
            font-size: 12px;
            font-weight: bold;
            margin: 0 0 7px;
            padding-bottom: 4px;
            border-bottom: 1px solid #ccc;
        }

        table.list {
            width: 100%;
            border-collapse: collapse;
            font-size: 9.5px;
        }

        table.list th, table.list td {
            border: 1px solid #bbb;
            padding: 5px 6px;
            text-align: right;
        }

        table.list thead {
            display: table-header-group;
        }

        table.list thead th {
            background: #eee;
            color: #222;
            font-weight: bold;
            text-align: center;
        }

        table.list tr {
            page-break-inside: avoid;
        }

        .center {
            text-align: center !important;
        }

        .amount {
            text-align: left !important;
            direction: ltr;
            white-space: nowrap;
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
<div class="header">
    <h1> تقفيل الشهر</h1>
    <div class="subtitle">الشهر: {{ $closing->month }}</div>
</div>

<table class="info">
    <tr>
        <td><span class="label">رقم التقفيل:</span> <span class="value">{{ $closing->reference }}</span></td>
        <td><span class="label">تاريخ الطباعة:</span> <span class="value">{{ now()->format('Y-m-d H:i') }}</span></td>
    </tr>
    <tr>
        <td><span class="label">تاريخ التقفيل:</span> <span
                class="value">{{ $closing->closed_at?->format('Y-m-d') ?? '—' }}</span></td>
        <td>
            <span class="label">الفرع:</span>
            <span class="value">{{ $closing->branch?->name ?? 'كل الفروع' }}</span>
        </td>
    </tr>
</table>

<table class="stats">
    <tr>
        <td>
            <div class="label">إجمالي الوارد</div>
            <div class="value green">{{ number_format($closing->total_revenue, 2) }} ج.م</div>
        </td>
        <td>
            <div class="label">إجمالي الصادر</div>
            <div class="value red">{{ number_format($closing->total_expense, 2) }} ج.م</div>
        </td>
        <td>
            <div class="label">الصافي</div>
            <div class="value {{ $closing->net_amount >= 0 ? 'green' : 'red' }}">
                {{ number_format($closing->net_amount, 2) }} ج.م
            </div>
        </td>
    </tr>
</table>

<h3 class="section-title">بنود التقفيل</h3>

<table class="list">
    <thead>
    <tr>
        <th style="width: 30px;">#</th>
        <th style="width: 55px;">النوع</th>
        <th>الرقم</th>
        <th>المصدر</th>
        <th style="width: 85px;">المبلغ</th>
        <th style="width: 80px;">التاريخ</th>
    </tr>
    </thead>
    <tbody>
    @forelse($closing->items as $i => $item)
        @php
            $enum = $item->type === 'revenue'
                ? \App\Enums\Teacher\RevenueSourceEnum::tryFrom($item->source_type)
                : \App\Enums\Teacher\ExpenseSourceEnum::tryFrom($item->source_type);
        @endphp
        <tr>
            <td class="center">{{ $i + 1 }}</td>
            <td class="center">{{ $item->type === 'revenue' ? 'وارد' : 'صادر' }}</td>
            <td>{{ $item->reference }}</td>
            <td>{{ $enum?->label() ?? $item->source_type }}</td>
            <td class="amount">{{ number_format($item->amount, 2) }} ج.م</td>
            <td class="center">{{ $item->occurred_at?->format('Y-m-d') ?? '—' }}</td>
        </tr>
    @empty
        <tr>
            <td colspan="6" class="center">لا توجد بنود في التقفيل</td>
        </tr>
    @endforelse
    </tbody>
</table>
{{-- Footer --}}
<div class="footer">
    كشف صادر رسمي — تم إنشاء المستند إلكترونيًا بتاريخ {{ now()->format('Y/m/d') }}
</div>
</body>
</html>
