<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>كود التحقق - نبذة</title>
    <!-- استدعاء خط Cairo -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Cairo', Arial, sans-serif;
            background-color: #f4f6f4; /* خلفية رمادية مائلة للأخضر الفاتح جداً */
            margin: 0;
            padding: 0;
            direction: rtl;
            -webkit-font-smoothing: antialiased;
        }
        .email-wrapper {
            width: 100%;
            background-color: #f4f6f4;
            padding: 40px 0;
        }
        .container {
            max-width: 500px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 16px;
            overflow: hidden;
            border: 1px solid #e5eae5;
            box-shadow: 0 10px 30px rgba(38, 59, 43, 0.05); /* ظل خفيف جداً بلون اللوجو */
        }
        .header {
            background-color: #ffffff;
            padding: 35px 30px 20px;
            text-align: center;
            border-bottom: 1px solid #f0f4f0;
        }
        .header h1 {
            margin: 0;
            font-size: 28px;
            font-weight: 700;
            color: #263B2B; /* الأخضر الغامق جداً (شبه الأسود) */
            letter-spacing: 1px;
        }
        .body-content {
            padding: 35px 30px;
            text-align: center;
            color: #263B2B;
        }
        .greeting {
            font-size: 18px;
            font-weight: 700;
            margin-bottom: 12px;
            color: #263B2B;
        }
        .text {
            font-size: 14px;
            color: #6B7D6E;
            line-height: 1.8;
            margin-bottom: 30px;
        }

        /* تصميم مربع الكود */
        .otp-container {
            background-color: #F4F7F4; /* أخضر فاتح جداً */
            border: 1px solid #D5E0D5;
            border-radius: 12px;
            padding: 25px;
            margin: 0 auto 30px;
            text-align: center;
        }
        .otp-label {
            font-size: 13px;
            color: #6B7D6E;
            margin-bottom: 10px;
            font-weight: 600;
        }
        .otp-code {
            font-size: 38px;
            font-weight: 700;
            color: #3C673E; /* الأخضر الزيتوني الأساسي */
            letter-spacing: 12px;
            font-family: 'Courier New', Courier, monospace; /* خطوط عريضة للأرقام */
            direction: ltr;
            margin-bottom: 10px;
        }
        .expiry {
            font-size: 12px;
            color: #9BAF9D;
        }

        /* تصميم التنبيه */
        .warning {
            background-color: #FFF9F2;
            border-right: 3px solid #D99A4E; /* لون برتقالي هادي */
            padding: 12px 15px;
            text-align: right;
            font-size: 13px;
            color: #8C6B41;
            margin-bottom: 25px;
            border-radius: 4px;
            line-height: 1.6;
        }

        .footer {
            background-color: #FAFCFA;
            padding: 25px 30px;
            text-align: center;
            font-size: 12px;
            color: #9BAF9D;
            border-top: 1px solid #F0F4F0;
        }
        .footer strong {
            color: #3C673E;
        }
    </style>
</head>
<body>
<div class="email-wrapper">
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1>نبذة</h1>
        </div>

        <!-- Body -->
        <div class="body-content">
            <p class="greeting">مرحباً بك 👋</p>

            <p class="text">
                لقد طلبت كود تحقق {{ $purposeLabel }}.
                <br>
                استخدم الكود التالي لإتمام العملية:
            </p>

            <!-- OTP Box -->
            <div class="otp-container">
                <div class="otp-label">كود التحقق الخاص بك</div>
                <div class="otp-code">{{ $code }}</div>
                <div class="expiry">
                    ينتهي هذا الكود خلال {{ $expiresInMinutes }} دقائق
                </div>
            </div>

            <!-- Warning -->
            <div class="warning">
                <strong>ملاحظة أمنية:</strong><br>
                لا تشارك هذا الكود مع أي شخص. فريق نبذة لن يطلب منك هذا الكود أبداً.
            </div>

            <p class="text" style="margin-bottom: 0; font-size: 12px;">
                إذا لم تكن أنت من طلب هذا الكود، يمكنك تجاهل هذه الرسالة بأمان.
            </p>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p style="margin: 0;">
                رسالة آلية من <strong>نبذة</strong> — نظام إدارة المراكز التعليمية
            </p>
            <p style="margin: 6px 0 0;">
                © {{ date('Y') }} جميع الحقوق محفوظة
            </p>
        </div>
    </div>
</div>
</body>
</html>
