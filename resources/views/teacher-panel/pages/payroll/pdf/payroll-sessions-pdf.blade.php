<!DOCTYPE html>
<html dir="rtl" lang="ar">

<head>
    <meta charset="UTF-8">
    <title>حصص {{ $payroll->teacher?->name }}</title>
    <style>
        body {
            font-family: 'dejavusans', sans-serif;
            font-size: 10px;
            color: #333;
            direction: rtl;
        }

        /* Header */
        .header {
            text-align: center;
            padding-bottom: 10px;
            border-bottom: 3px solid #1e293b;
            margin-bottom: 15px;
        }

        .header h1 {
            color: #1e293b;
            margin: 0 0 4px;
            font-size: 22px;
            font-weight: bold;
            letter-spacing: 1px;
        }

        .header h2 {
            margin: 4px 0;
            font-size: 12px;
            color: #64748b;
            font-weight: normal;
        }

        /* Info */
        .info {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            padding: 8px 12px;
            margin-bottom: 15px;
            border-radius: 4px;
        }

        .info table {
            width: 100%;
            border: none;
            margin: 0;
        }

        .info td {
            border: none;
            padding: 3px 0;
            text-align: right;
            font-size: 10px;
        }

        .info td strong {
            color: #1e293b;
        }

        /* Main table */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }

        th,
        td {
            border: 1px solid #dee2e6;
            padding: 5px 7px;
            text-align: center;
            font-size: 10px;
        }

        th {
            background: #e9ecef;
            font-weight: bold;
            color: #495057;
        }

        /* Row colors */
        .bg-completed {
            background: #d4edda;
        }

        .bg-cancelled {
            background: #f8d7da;
        }

        .bg-upcoming {
            background: #e9ecef;
        }

        /* Summary */
        .summary {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            padding: 8px 12px;
            border-radius: 4px;
            margin-top: 10px;
        }

        .summary table {
            border: none;
            margin: 0;
        }

        .summary td {
            border: none;
            text-align: right;
            padding: 3px 0;
        }

        .summary td:last-child {
            text-align: left;
            font-weight: bold;
        }

        /* Footer */
        .footer {
            text-align: center;
            margin-top: 20px;
            padding-top: 8px;
            border-top: 2px solid #1e293b;
            font-size: 9px;
            color: #64748b;
        }

        .footer strong {
            color: #1e293b;
            font-size: 11px;
        }
    </style>
</head>

<body>


    {{-- Info --}}
    <div class="info">
        <table>
            <tr>
                <td><strong>المدرس:</strong> {{ $payroll->teacher?->name ?? '—' }}</td>
                <td><strong>الفرع:</strong> {{ $payroll->branch?->name ?? 'كل الفروع' }}</td>
            </tr>
            <tr>
                <td><strong>الفترة:</strong> {{ $payroll->period_label }}</td>
                <td><strong>الرقم:</strong> {{ $payroll->reference }}</td>
            </tr>
            <tr>
                <td><strong>إجمالي الحصص:</strong> {{ $sessions->count() }}</td>
                <td><strong>إجمالي الساعات:</strong> {{ number_format((float) $sessions->sum('hours'), 1) }} ساعة</td>
            </tr>
        </table>
    </div>

    {{-- Sessions Table --}}
    <table>
        <thead>
            <tr>
                <th style="width: 30px;">#</th>
                <th>التاريخ</th>
                <th>الوقت</th>
                <th>المجموعة</th>
                <th>الفرع</th>
                <th>المادة</th>
                <th style="width: 50px;">الساعات</th>
                <th style="width: 60px;">الحالة</th>
            </tr>
        </thead>
        <tbody>
            @forelse($sessions as $i => $s)
                @php
                    $rowClass = match ($s['status']) {
                        'cancelled' => 'bg-cancelled',
                        'completed' => 'bg-completed',
                        default => 'bg-upcoming',
                    };
                    $label = match ($s['status']) {
                        'cancelled' => 'ملغية',
                        'completed' => 'مكتملة',
                        default => 'لم تُدرّس',
                    };
                @endphp
                <tr class="{{ $rowClass }}">
                    <td>{{ $i + 1 }}</td>
                    <td>{{ $s['date']->format('Y-m-d') }}</td>
                    <td>{{ $s['start_time'] }} - {{ $s['end_time'] }}</td>
                    <td>{{ $s['group'] ?? '—' }}</td>
                    <td>{{ $s['branch'] ?? '—' }}</td>
                    <td>{{ $s['subject'] ?? '—' }}</td>
                    <td>{{ number_format($s['hours'], 1) }}</td>
                    <td>{{ $label }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="8">لا توجد حصص في هذه الفترة</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{-- Summary --}}
    <div class="summary">
        <table>
            <tr>
                <td>مكتملة:</td>
                <td>{{ $sessions->where('status', 'completed')->count() }} حصة</td>
            </tr>
            <tr>
                <td>ملغية:</td>
                <td>{{ $sessions->where('status', 'cancelled')->count() }} حصة</td>
            </tr>
            <tr>
                <td>لم تُدرّس:</td>
                <td>{{ $sessions->where('status', 'upcoming')->count() }} حصة</td>
            </tr>
        </table>
    </div>
</body>

</html>
