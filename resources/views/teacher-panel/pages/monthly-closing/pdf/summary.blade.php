<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'dejavusans', sans-serif; color: #1a1a1a; font-size: 11px; }
        .header { border-bottom: 2px solid #2c3e50; padding-bottom: 10px; margin-bottom: 16px; }
        .header .brand { font-size: 20px; font-weight: bold; color: #2c3e50; }
        .header h1 { font-size: 15px; color: #2c3e50; margin-top: 6px; }
        .header .meta { font-size: 10px; color: #666; margin-top: 4px; }
        .header .meta span { margin-left: 16px; }
        .stats { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .stats td { width: 33.33%; padding: 12px; border: 1px solid #ddd; text-align: center; }
        .stats .label { font-size: 11px; color: #666; margin-bottom: 6px; }
        .stats .value { font-size: 18px; font-weight: bold; }
        .stats .value.green { color: #27ae60; }
        .stats .value.red { color: #c0392b; }
        .stats .value.blue { color: #2c3e50; }
        table.list { width: 100%; border-collapse: collapse; font-size: 10px; }
        table.list th, table.list td { border: 1px solid #888; padding: 6px 8px; text-align: right; }
        table.list thead th { background: #2c3e50; color: #fff; text-align: center; }
        .footer { position: fixed; bottom: 6px; left: 0; right: 0; text-align: center; font-size: 8.5px; color: #999; border-top: 1px solid #e0e0e0; padding-top: 4px; }
    </style>
</head>
<body>
<div class="header">
    <h1>خلاصة تقفيل الشهر — {{ $closing->month }}</h1>
    <div class="meta">
        <span>رقم التقفيل: <strong>{{ $closing->reference }}</strong></span>
        @if($closing->branch)
            <span>الفرع: <strong>{{ $closing->branch->name }}</strong></span>
        @endif
        <span>التاريخ: {{ $closing->closed_at?->format('Y-m-d') }}</span>
    </div>
</div>

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

<h3 style="color:#2c3e50; margin-bottom:8px; font-size:12px;">بنود التقفيل</h3>
<table class="list">
    <thead>
    <tr>
        <th style="width:30px;">#</th>
        <th style="width:60px;">النوع</th>
        <th>الرقم</th>
        <th>المصدر</th>
        <th style="width:90px;">المبلغ</th>
        <th style="width:80px;">التاريخ</th>
    </tr>
    </thead>
    <tbody>
    @foreach($closing->items as $i => $item)
        @php
            $enum = $item->type === 'revenue'
                ? \App\Enums\Teacher\RevenueSourceEnum::tryFrom($item->source_type)
                : \App\Enums\Teacher\ExpenseSourceEnum::tryFrom($item->source_type);
        @endphp
        <tr>
            <td style="text-align:center;">{{ $i + 1 }}</td>
            <td style="text-align:center;">{{ $item->type === 'revenue' ? 'وارد' : 'صادر' }}</td>
            <td>{{ $item->reference }}</td>
            <td>{{ $enum?->label() ?? $item->source_type }}</td>
            <td style="text-align:left; direction:ltr;">{{ number_format($item->amount, 2) }} ج.م</td>
            <td style="text-align:center;">{{ $item->occurred_at?->format('Y-m-d') }}</td>
        </tr>
    @endforeach
    </tbody>
</table>
</body>
</html>
