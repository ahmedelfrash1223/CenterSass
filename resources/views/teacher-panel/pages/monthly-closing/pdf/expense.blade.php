<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'dejavusans', sans-serif; color: #1a1a1a; font-size: 10.5px; }
        .header { border-bottom: 2px solid #2c3e50; padding-bottom: 10px; margin-bottom: 16px; }
        .header .brand { font-size: 20px; font-weight: bold; color: #2c3e50; }
        .header h1 { font-size: 15px; color: #2c3e50; margin-top: 6px; }
        .header .meta { font-size: 10px; color: #666; margin-top: 4px; }
        .header .meta span { margin-left: 16px; }
        table.list { width: 100%; border-collapse: collapse; font-size: 10px; }
        table.list th, table.list td { border: 1px solid #888; padding: 7px 8px; text-align: right; }
        table.list thead th { background: #2c3e50; color: #fff; font-weight: bold; text-align: center; }
        table.list tbody tr:nth-child(even) td { background: #f8f9fa; }
        .total-row td { background: #e8eef3 !important; font-weight: bold; font-size: 11px; border-top: 1.5px solid #2c3e50; }
        .footer { position: fixed; bottom: 6px; left: 0; right: 0; text-align: center; font-size: 8.5px; color: #999; border-top: 1px solid #e0e0e0; padding-top: 4px; }
    </style>
</head>
<body>
<div class="header">
    <h1>كشف الصادر — {{ $closing->month }}</h1>
    <div class="meta">
        <span>رقم التقفيل: <strong>{{ $closing->reference }}</strong></span>
        @if($closing->branch)
            <span>الفرع: <strong>{{ $closing->branch->name }}</strong></span>
        @endif
        <span>المدرس: {{ $closing->teacher?->name }}</span>
        <span>التاريخ: {{ $closing->closed_at?->format('Y-m-d') }}</span>
    </div>
</div>

<table class="list">
    <thead>
    <tr>
        <th style="width:30px;">#</th>
        <th>الرقم</th>
        <th>المصدر</th>
        <th>الوصف / السبب</th>
        <th style="width:90px;">المبلغ</th>
        <th style="width:90px;">التاريخ</th>
    </tr>
    </thead>
    <tbody>
    @forelse($items as $i => $item)
        @php
            $enum = \App\Enums\Teacher\ExpenseSourceEnum::tryFrom($item->source_type);
        @endphp
        <tr>
            <td style="text-align:center;">{{ $i + 1 }}</td>
            <td>{{ $item->reference }}</td>
            <td>{{ $enum?->label() ?? $item->source_type }}</td>
            <td>{{ $item->description ?: '—' }}</td>
            <td style="text-align:left; direction:ltr;">{{ number_format($item->amount, 2) }} ج.م</td>
            <td style="text-align:center;">{{ $item->occurred_at?->format('Y-m-d') }}</td>
        </tr>
    @empty
        <tr><td colspan="6" style="text-align:center; padding:20px;">لا توجد بنود</td></tr>
    @endforelse
    <tr class="total-row">
        <td colspan="4" style="text-align:right;">الإجمالي</td>
        <td style="text-align:left; direction:ltr;">{{ number_format($closing->total_expense, 2) }} ج.م</td>
        <td></td>
    </tr>
    </tbody>
</table>
</body>
</html>
