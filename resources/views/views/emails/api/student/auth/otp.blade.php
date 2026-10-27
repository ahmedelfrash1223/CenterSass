<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>كود التحقق - Classio</title>
    <style>
        body {
            font-family: 'Cairo', 'DejaVu Sans', Arial, sans-serif;
            background: #f5f7fa;
            margin: 0;
            padding: 0;
            direction: rtl;
        }
        .container {
            max-width: 600px;
            margin: 40px auto;
            background: #ffffff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.06);
        }
        .header {
            background: linear-gradient(135deg, #4680ff 0%, #6f9cff 100%);
            color: #ffffff;
            padding: 40px 30px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: 700;
        }
        .header p {
            margin: 8px 0 0;
            opacity: 0.9;
            font-size: 14px;
        }
        .body {
            padding: 40px 30px;
            text-align: center;
        }
        .greeting {
            font-size: 18px;
            color: #2c3e50;
            margin-bottom: 12px;
        }
        .text {
            font-size: 14px;
            color: #64748b;
            line-height: 1.8;
            margin-bottom: 24px;
        }
        .otp-box {
            background: #f8faff;
            border: 2px dashed #4680ff;
            border-radius: 12px;
            padding: 24px;
            margin: 24px 0;
        }
        .otp-label {
            font-size: 13px;
            color: #64748b;
            margin-bottom: 8px;
        }
        .otp-code {
            font-size: 42px;
            font-weight: 700;
            color: #4680ff;
            letter-spacing: 10px;
            font-family: 'Courier New', monospace;
            direction: ltr;
        }
        .expiry {
            font-size: 13px;
            color: #94a3b8;
            margin-top: 12px;
        }
        .warning {
            background: #fff7ed;
            border-right: 4px solid #f59e0b;
            padding: 12px 16px;
            text-align: right;
            font-size: 13px;
            color: #92400e;
            margin: 24px 0;
            border-radius: 4px;
        }
        .footer {
            background: #f8fafc;
            padding: 24px 30px;
            text-align: center;
            font-size: 12px;
            color: #94a3b8;
            border-top: 1px solid #e2e8f0;
        }
        .footer strong {
            color: #4680ff;
        }
    </style>
</head>
<body>
<div class="container">
    {{-- Header --}}
    <div class="header">
        <h1>Classio</h1>
        <p>كود التحقق الخاص بك</p>
    </div>

    {{-- Body --}}
    <div class="body">
        <p class="greeting">مرحبًا 👋</p>

        <p class="text">
            طلبت كود تحقق {{ $purposeLabel }}.
            <br>
            استخدم الكود التالي لإتمام العملية:
        </p>

        {{-- OTP Box --}}
        <div class="otp-box">
            <div class="otp-label">كود التحقق</div>
            <div class="otp-code">{{ $code }}</div>
            <div class="expiry">
                ⏱ ينتهي بعد {{ $expiresInMinutes }} دقائق
            </div>
        </div>

        {{-- Warning --}}
        <div class="warning">
            <strong>⚠️ تنبيه:</strong>
            لا تشارك هذا الكود مع أي شخص.
            فريق Classio لن يطلب منك الكود أبدًا.
        </div>

        <p class="text" style="margin-bottom: 0;">
            لو مش إنت اللي طلبت الكود ده، تقدر تتجاهل الرسالة بأمان.
        </p>
    </div>

    {{-- Footer --}}
    <div class="footer">
        <p style="margin: 0;">
            هذا البريد أُرسل تلقائيًا من <strong>Classio</strong>
        </p>
        <p style="margin: 6px 0 0;">
            © {{ date('Y') }} Classio — نظام إدارة المراكز التعليمية
        </p>
    </div>
</div>
</body>
</html>
