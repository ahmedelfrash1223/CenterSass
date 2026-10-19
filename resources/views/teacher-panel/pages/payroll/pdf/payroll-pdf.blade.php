<!DOCTYPE html>
<html dir="rtl" lang="ar">

<head>
    <meta charset="UTF-8">
    <title>كشف حساب {{ $payroll->reference }}</title>
    <style>
        body {
            font-family: 'dejavusans', sans-serif;
            font-size: 11px;
            color: #333;
            direction: rtl;
        }

        .pdf-header {
            text-align: center;
            padding-bottom: 12px;
            margin-bottom: 12px;
            border-bottom: 3px solid #1e293b;
        }

        .pdf-header h1 {
            color: #1e293b;
            margin: 0 0 4px;
            font-size: 24px;
            font-weight: bold;
            letter-spacing: 1px;
        }

        .pdf-header h2 {
            margin: 4px 0;
            font-size: 14px;
            color: #64748b;
            font-weight: normal;
        }

        .pdf-details {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            padding: 10px 14px;
            margin-bottom: 15px;
            border-radius: 4px;
        }

        .pdf-details table {
            width: 100%;
            border: none;
            margin: 0;
        }

        .pdf-details td {
            border: none;
            padding: 4px 0;
            text-align: right;
            font-size: 11px;
        }

        .pdf-details td strong {
            color: #1e293b;
        }

        .pdf-separator {
            border-top: 2px solid #1e293b;
            margin: 15px 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }

        th,
        td {
            border: 1px solid #dee2e6;
            padding: 6px 8px;
            text-align: center;
        }

        th {
            background: #e9ecef;
            font-weight: bold;
            color: #495057;
        }

        .total-row {
            background: #d4edda;
            font-weight: bold;
        }

        .total-row td {
            font-size: 13px;
        }

        .section-title {
            background: #1e293b;
            color: #fff;
            padding: 6px 10px;
            margin-top: 15px;
            margin-bottom: 8px;
            font-size: 12px;
            font-weight: bold;
            border-radius: 4px;
        }

        .pdf-footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 10px;
            border-top: 2px solid #1e293b;
            font-size: 9px;
            color: #64748b;
        }

        .pdf-footer strong {
            color: #1e293b;
            font-size: 11px;
        }

        .badge-success {
            background: #d4edda;
            color: #155724;
            padding: 2px 6px;
            border-radius: 10px;
            font-size: 10px;
        }

        .badge-danger {
            background: #f8d7da;
            color: #721c24;
            padding: 2px 6px;
            border-radius: 10px;
            font-size: 10px;
        }
    </style>
</head>

<body>



    {{-- Details --}}
    <div class="pdf-details">
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
                <td><strong>نوع الأجر:</strong> {{ $payroll->payment_type->label() }}</td>
                <td><strong>الحالة:</strong> {{ $payroll->status->label() }}</td>
            </tr>
        </table>
    </div>

    <div class="pdf-separator"></div>

    {{-- Sessions Stats --}}
    <div class="section-title">تفاصيل الحصص</div>
    <table>
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
                <td class="badge-success">{{ $stats['completed'] }}</td>
                <td class="badge-danger">{{ $stats['cancelled'] }}</td>
                <td>{{ $stats['upcoming'] }}</td>
                <td>{{ number_format((float) $stats['hours_total'], 1) }} س</td>
            </tr>
        </tbody>
    </table>

    {{-- Amounts --}}
    <div class="section-title">تفاصيل المبالغ</div>
    <table>
        <thead>
            <tr>
                <th>البند</th>
                <th>المبلغ (ج.م)</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>المبلغ الكامل</td>
                <td>{{ number_format((float) $amounts['full'], 2) }}</td>
            </tr>
            @if($amounts['cancellation_deduction'] > 0)
                <tr>
                    <td>خصم الحصص الملغية</td>
                    <td style="color: #721c24;">- {{ number_format((float) $amounts['cancellation_deduction'], 2) }}</td>
                </tr>
            @endif
            <tr>
                <td>البونص</td>
                <td style="color: #155724;">+ {{ number_format((float) $amounts['bonus'], 2) }}</td>
            </tr>
            <tr>
                <td>الخصومات</td>
                <td style="color: #721c24;">- {{ number_format((float) $amounts['deductions'], 2) }}</td>
            </tr>
            <tr class="total-row">
                <td>الصافي</td>
                <td>{{ number_format((float) $amounts['net'], 2) }}</td>
            </tr>
            <tr>
                <td>المدفوع</td>
                <td style="color: #0c5460;">{{ number_format((float) $amounts['paid'], 2) }}</td>
            </tr>
            <tr>
                <td>المتبقي</td>
                <td style="color: #721c24;">{{ number_format((float) $amounts['remaining'], 2) }}</td>
            </tr>
        </tbody>
    </table>

    {{-- Bonus & Deductions Details --}}
    @if($payroll->bonuses->count() > 0 || $payroll->deductionsItems->count() > 0)
        <div class="section-title">البنود الإضافية</div>
        <table>
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
                        <td><span class="badge-success">بونص</span></td>
                        <td>{{ $bonus->reason }}</td>
                        <td style="color: #155724;">+ {{ number_format((float) $bonus->amount, 2) }}</td>
                        <td>{{ $bonus->created_at?->format('Y-m-d') }}</td>
                    </tr>
                @endforeach
                @foreach($payroll->deductionsItems as $deduction)
                    <tr>
                        <td><span class="badge-danger">خصم</span></td>
                        <td>{{ $deduction->reason }}</td>
                        <td style="color: #721c24;">- {{ number_format((float) $deduction->amount, 2) }}</td>
                        <td>{{ $deduction->created_at?->format('Y-m-d') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    {{-- Payments --}}
    @if($payroll->payments->count() > 0)
        <div class="section-title">سجل الدفعات</div>
        <table>
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
                        <td>{{ number_format((float) $payment->amount, 2) }}</td>
                        <td>{{ $payment->paid_at?->format('Y-m-d H:i') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif



</body>

</html>
