<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="utf-8">
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'dejavusans', sans-serif;
            color: #1a1a1a;
            font-size: 10.5px;
        }

        /* ═════════ Header ═════════ */
        .page-header {
            border-bottom: 2px solid #2c3e50;
            padding-bottom: 10px;
            margin-bottom: 16px;
        }

        .page-header .brand {
            font-size: 20px;
            font-weight: bold;
            color: #2c3e50;
            letter-spacing: 1px;
        }

        .page-header h1 {
            font-size: 15px;
            color: #2c3e50;
            margin-top: 6px;
            margin-bottom: 4px;
        }

        .page-header .meta-row {
            font-size: 10px;
            color: #666;
            margin-top: 4px;
        }

        .page-header .meta-row span {
            margin-left: 16px;
        }

        /* ═════════ Table (Word-style) ═════════ */
        table.list {
            width: 100%;
            border-collapse: collapse;
            font-size: 10px;
        }

        table.list th,
        table.list td {
            border: 1px solid #888;
            padding: 7px 8px;
            text-align: right;
            vertical-align: middle;
            word-wrap: break-word;
        }

        /* رأس الجدول — داكن ونص أبيض */
        table.list thead th {
            background: #2c3e50;
            color: #ffffff;
            font-weight: bold;
            font-size: 10px;
            text-align: center;
            letter-spacing: 0.3px;
            border-color: #2c3e50;
        }

        /* أعمدة ثابتة بأسلوب Word */
        .col-num {
            width: 30px;
            text-align: center;
        }

        .col-group {
            width: 110px;
        }

        .col-name {
            width: auto;
        }

        .col-code {
            width: 100px;
        }

        .col-check {
            width: 65px;
            text-align: center;
        }

        /* صفوف البيانات */
        table.list tbody tr:nth-child(even) td {
            background: #f8f9fa;
        }

        /* صف المجموعة */
        .group-row td {
            background: #e8eef3 !important;
            font-weight: bold;
            color: #2c3e50;
            font-size: 10.5px;
            padding: 8px;
            border-top: 1.5px solid #2c3e50;
        }

        /* خانات المراجعة */
        .check-cell {
            text-align: center;
            vertical-align: middle;
        }

        .box {
            display: inline-block;
            width: 14px;
            height: 14px;
            border: 1.2px solid #555;
            background: #ffffff;
            vertical-align: middle;
        }

        /* ═════════ Footer ═════════ */
        .page-footer {
            position: fixed;
            bottom: 6px;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 8.5px;
            color: #999;
            border-top: 1px solid #e0e0e0;
            padding-top: 4px;
        }
    </style>
</head>

<body>

    {{-- Header --}}
    <div class="page-header">
        <h1>قائمة متابعة الطلاب — {{ $cardRequest->teacher?->name ?? '' }}</h1>
        <div class="meta-row">
            <span>طلب رقم: <strong>{{ $cardRequest->reference }}</strong></span>
            <span>عدد الطلاب: <strong>{{ $items->count() }}</strong></span>
            <span>التاريخ: {{ now()->format('Y-m-d') }}</span>
        </div>
    </div>

    @if($items->isEmpty())
        <p style="text-align:center; margin-top:40px; color:#666;">لا توجد عناصر في هذا الطلب.</p>
    @else
        <table class="list">
            <thead>
                <tr>
                    <th class="col-num">#</th>
                    <th class="col-group">المجموعة</th>
                    <th class="col-name">اسم الطالب</th>
                    <th class="col-code">الكود</th>
                    <th class="col-check">مراجعة</th>
                    <th class="col-check">طباعة</th>
                    <th class="col-check">تسليم</th>
                </tr>
            </thead>
            <tbody>
                @php $rowNumber = 1; @endphp
                @foreach($items->groupBy(fn($i) => $i->group?->name ?? 'بدون مجموعة') as $groupName => $groupItems)
                    <tr class="group-row">
                        <td colspan="7">
                            المجموعة: {{ $groupName }}
                            <span style="font-weight: normal; color: #555;">
                                — {{ $groupItems->count() }} طالب
                            </span>
                        </td>
                    </tr>
                    @foreach($groupItems as $item)
                        <tr>
                            <td class="col-num">{{ $rowNumber++ }}</td>
                            <td class="col-group">{{ $groupName }}</td>
                            <td class="col-name">{{ $item->student_name ?? '—' }}</td>
                            <td class="col-code" style="direction:ltr; text-align:left;">
                                {{ $item->student_code ?? '—' }}
                            </td>
                            <td class="check-cell"><span class="box"></span></td>
                            <td class="check-cell"><span class="box"></span></td>
                            <td class="check-cell"><span class="box"></span></td>
                        </tr>
                    @endforeach
                @endforeach
            </tbody>
        </table>
    @endif

</body>

</html>
