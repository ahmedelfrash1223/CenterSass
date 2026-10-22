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
            font-size: 11px;
        }

        /* ═════════ Header ═════════ */
        .header {
            border-bottom: 2px solid #2c3e50;
            padding-bottom: 10px;
            margin-bottom: 16px;
        }

        .header .brand {
            font-size: 20px;
            font-weight: bold;
            color: #2c3e50;
        }

        .header .subtitle {
            font-size: 11px;
            color: #555;
            margin-top: 4px;
        }

        .header .meta {
            margin-top: 6px;
            font-size: 10px;
            color: #666;
        }

        .header .meta span {
            margin-left: 16px;
        }

        /* ═════════ Cards Table ═════════ */
        table.cards {
            width: 100%;
            border-collapse: collapse;
        }

        table.cards>tbody>tr>td {
            width: 50%;
            vertical-align: top;
            padding: 8px 10px;
        }

        /* ═════════ Card ═════════ */
        .card-table {
            width: 100%;
            border-collapse: collapse;
            border: 1.5px solid #2c3e50;
            background: #ffffff;
        }

        .card-table td {
            padding: 10px;
            vertical-align: middle;
        }

        /* الباركود (يمين) */
        .card-barcode-cell {
            width: 55%;
            text-align: center;
            border-left: 1px dashed #999;
        }

        .card-barcode-cell img {
            display: block;
            margin: 0 auto;
            height: 60px;
            max-width: 100%;
        }

        .card-barcode-cell .no-code {
            color: #999;
            font-size: 9px;
            height: 60px;
            line-height: 60px;
        }

        /* بيانات الطالب (شمال) */
        .card-info-cell {
            width: 45%;
            text-align: right;
            padding-right: 14px;
        }

        .card-info-cell .student-name {
            font-size: 12px;
            font-weight: bold;
            color: #1a1a1a;
            margin-bottom: 3px;
            line-height: 1.3;
        }

        /* ✅ سطر اسم المجموعة */
        .card-info-cell .student-group {
            font-size: 9.5px;
            color: #666;
            margin-bottom: 6px;
            line-height: 1.2;
        }

        .card-info-cell .student-group .label {
            color: #999;
            font-size: 9px;
        }

        .card-info-cell .student-code {
            font-size: 10px;
            color: #2c3e50;
            direction: ltr;
            text-align: right;
            font-family: 'monospace', 'dejavusans', sans-serif;
            letter-spacing: 0.5px;
            font-weight: bold;
        }

        /* ═════════ Footer ═════════ */
        .footer {
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
    <div class="header">
        <div class="subtitle">بطاقات الباركود — {{ $cardRequest->teacher?->name ?? '' }}</div>
        <div class="meta">
            <span>طلب رقم: <strong>{{ $cardRequest->reference }}</strong></span>
            <span>عدد الكروت: <strong>{{ $items->count() }}</strong></span>
            <span>التاريخ: {{ now()->format('Y-m-d') }}</span>
        </div>
    </div>

    @if($items->isEmpty())
        <p style="text-align:center; margin-top:40px; color:#666;">لا توجد عناصر في هذا الطلب.</p>
    @else
        {{-- بطاقتان في كل صف، كل بطاقة أفقية --}}
        <table class="cards">
            @foreach($items->chunk(2) as $chunk)
                <tr>
                    @foreach($chunk as $item)
                        @php
                            $code = trim((string) ($item->student_code ?? $item->student_id ?? ''));
                            $groupName = $item->group?->name ?? '—';
                            $barcodeImg = '';
                            if ($code !== '') {
                                try {
                                    $barcodeImg = DNS1D::getBarcodePNG($code, 'C128', 1.5, 50, [0, 0, 0], false);
                                } catch (\Throwable $e) {
                                    $barcodeImg = '';
                                }
                            }
                        @endphp
                        <td>
                            <table class="card-table">
                                <tr>
                                    {{-- الباركود على اليمين --}}
                                    <td class="card-barcode-cell">
                                        @if($barcodeImg !== '')
                                            <img src="data:image/png;base64,{{ $barcodeImg }}" alt="{{ $code }}">
                                        @else
                                            <div class="no-code">— بدون كود —</div>
                                        @endif
                                    </td>
                                    {{-- الاسم + المجموعة + الكود على الشمال --}}
                                    <td class="card-info-cell">
                                        <div class="student-name">{{ $item->student_name ?? '—' }}</div>
                                        <div class="student-group">
                                            <span class="label">المجموعة:</span>
                                            {{ $groupName }}
                                        </div>
                                        <div class="student-code">{{ $code !== '' ? $code : '—' }}</div>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    @endforeach
                    @if($chunk->count() < 2)
                        <td>&nbsp;</td>
                    @endif
                </tr>
            @endforeach
        </table>
    @endif


</body>

</html>
