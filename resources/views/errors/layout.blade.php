<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'حدث خطأ') | نبذة</title>

    <style>
        :root {
            --blue: #008cff;
            --green: #00dfa2;
            --dark: #202b3c;
            --muted: #8993a2;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            min-height: 100vh;
            background: #fff;
            color: var(--dark);
            font-family: "Cairo", "Segoe UI", Tahoma, Arial, sans-serif;
        }

        .error-page {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 35px 20px;
            text-align: center;
        }

        .error-logo {
            display: inline-block;
            margin-bottom: 65px;
        }

        .error-logo img {
            display: block;
            width: 190px;
            max-width: 65vw;
            height: auto;
        }

        .error-content {
            width: 100%;
            max-width: 520px;
        }

        .error-label {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 20px;
            color: #657386;
            font-size: 13px;
            font-weight: 600;
        }

        .error-label::before {
            content: "";
            width: 7px;
            height: 7px;
            border-radius: 50%;
            background: var(--green);
        }

        .error-code {
            margin: 0 0 22px;
            color: var(--blue);
            font-size: clamp(90px, 17vw, 145px);
            font-weight: 800;
            line-height: .95;
            letter-spacing: -5px;
        }

        .error-title {
            margin: 0 0 14px;
            font-size: clamp(23px, 4vw, 30px);
            font-weight: 700;
            line-height: 1.5;
        }

        .error-message {
            max-width: 420px;
            margin: 0 auto;
            color: var(--muted);
            font-size: 15px;
            line-height: 2;
        }

        .error-divider {
            width: 44px;
            height: 3px;
            margin: 28px auto;
            border-radius: 5px;
            background: var(--green);
        }

        .error-actions {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 19px;
        }

        .home-button {
            min-width: 185px;
            min-height: 48px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0 24px;
            border-radius: 8px;
            background: var(--blue);
            color: #fff;
            text-decoration: none;
            font-size: 14px;
            font-weight: 700;
            transition: background .2s ease;
        }

        .home-button:hover {
            background: #0878d8;
        }

        .back-link {
            color: #7d8999;
            font-size: 13px;
            text-decoration: none;
        }

        .back-link:hover {
            color: var(--blue);
        }

        .error-footer {
            margin-top: 65px;
            color: #a0a9b5;
            font-size: 12px;
        }

        .error-footer span {
            color: var(--blue);
            font-weight: 700;
        }

        @media (max-width: 480px) {
            .error-logo {
                margin-bottom: 48px;
            }

            .error-logo img {
                width: 160px;
            }

            .error-code {
                letter-spacing: -3px;
            }

            .error-footer {
                margin-top: 48px;
            }
        }
    </style>
</head>

<body>
    <main class="error-page">
        <a class="error-logo" href="{{ url('/') }}" aria-label="نبذة">
            <img src="{{ asset('assets/brand.svg') }}" alt="نبذة">
        </a>

        <section class="error-content">
            <div class="error-label">
                @yield('eyebrow', 'حدث خطأ')
            </div>

            <h2 class="error-code">@yield('code', '404')</h2>

            <h1 class="error-title">
                @yield('heading', 'عذرًا، حدث خطأ')
            </h1>

            <p class="error-message">
                @yield('message', 'لم نتمكن من العثور على ما تبحث عنه. يمكنك العودة للرئيسية والمحاولة مرة أخرى.')
            </p>

            <div class="error-divider"></div>

            <div class="error-actions">
                <a wire:navigate class="back-link" href="javascript:history.back()">
                    الرجوع إلى الصفحة السابقة
                </a>
            </div>
        </section>

    </main>
</body>

</html>
