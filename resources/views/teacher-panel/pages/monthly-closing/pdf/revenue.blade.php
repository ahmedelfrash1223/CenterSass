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

        .total-row td {
            background: #f0f0f0;
            font-weight: bold;
            border-top: 1.5px solid #777;
        }

        .empty {
            text-align: center !important;
            padding: 12px !important;
            color: #666;
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
    <h1>كشف الوارد</h1>
    <div class="subtitle">الفترة: {{ $closing->month }}</div>
</div>

<table class="info">
    <tr>
        <td><span class="label">رقم التقفيل:</span> <span class="value">{{ $closing->reference }}</span></td>
        <td><span class="label">تاريخ الطباعة:</span> <span class="value">{{ now()->format('Y-m-d H:i') }}</span></td>
    </tr>
    <tr>
        <td><span class="label">المدرس:</span> <span class="value">{{ $closing->teacher?->name ?? '—' }}</span></td>
        <td><span class="label">تاريخ التقفيل:</span> <span
                class="value">{{ $closing->closed_at?->format('Y-m-d') ?? '—' }}</span></td>
    </tr>
    @if($closing->branch)
        <tr>
            <td colspan="2"><span class="label">الفرع:</span> <span class="value">{{ $closing->branch->name }}</span>
            </td>
        </tr>
    @endif
</table>

<table class="list">
    <thead>
    <tr>
        <th style="width: 30px;">#</th>
        <th>الرقم</th>
        <th>المصدر</th>
        <th>الوصف</th>
        <th style="width: 85px;">المبلغ</th>
        <th style="width: 80px;">التاريخ</th>
    </tr>
    </thead>
    <tbody>
    @forelse($items as $i => $item)
        @php
            $enum = \App\Enums\Teacher\RevenueSourceEnum::tryFrom($item->source_type);
        @endphp
        <tr>
            <td class="center">{{ $i + 1 }}</td>
            <td>{{ $item->reference }}</td>
            <td>{{ $enum?->label() ?? $item->source_type }}</td>
            <td>{{ $item->description ?: '—' }}</td>
            <td class="amount">{{ number_format($item->amount, 2) }} ج.م</td>
            <td class="center">{{ $item->occurred_at?->format('Y-m-d') ?? '—' }}</td>
        </tr>
    @empty
        <tr>
            <td colspan="6" class="empty">لا توجد بنود</td>
        </tr>
    @endforelse
    <tr class="total-row">
        <td colspan="4">الإجمالي</td>
        <td class="amount">{{ number_format($closing->total_revenue, 2) }} ج.م</td>
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
