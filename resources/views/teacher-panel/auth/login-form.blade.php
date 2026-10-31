@php
    // LoginForm reports wrong credentials as a "login" error while the field is filled;
    // when the field is empty it's the "required" validation message instead.
    $hasCredentialsError = $errors->has('login') && filled($login);
@endphp

<form class="nb-form" wire:submit.prevent="submit" data-nb-login-form>

    <!------- General error (wrong credentials) ------->
    @if ($hasCredentialsError)
        <div class="nb-alert" role="alert">
            <svg class="nb-alert__icon" viewBox="0 0 24 24" fill="none" aria-hidden="true" focusable="false">
                <circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="1.8"/>
                <path d="M12 7.5v5.5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                <circle cx="12" cy="16.4" r="1.1" fill="currentColor"/>
            </svg>
            <p class="nb-alert__text">{{ $errors->first('login') }}</p>
        </div>
    @endif

    <!------- Phone / Email / Username ------->
    <div class="nb-field @if ($errors->has('login')) nb-field--invalid @endif">
        <label class="nb-field__label" for="nb-login-identifier">
            رقم الموبايل أو البريد الإلكتروني أو اسم المستخدم
        </label>

        <div class="nb-field__control">
            <span class="nb-field__icon" aria-hidden="true">
                <svg viewBox="0 0 24 24" fill="none" focusable="false">
                    <circle cx="12" cy="8.5" r="3.75" stroke="currentColor" stroke-width="1.7"/>
                    <path d="M4.75 19.25c1.3-3.1 4-4.75 7.25-4.75s5.95 1.65 7.25 4.75" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/>
                </svg>
            </span>

            <input
                id="nb-login-identifier"
                type="text"
                name="login"
                class="nb-input"
                wire:model="login"
                autocomplete="username"
                autocapitalize="none"
                spellcheck="false"
                @if ($errors->has('login'))
                    aria-invalid="true"
                    @unless ($hasCredentialsError) aria-describedby="nb-login-identifier-error" @endunless
                @endif
            >
        </div>

        @error('login')
            @unless ($hasCredentialsError)
                <p class="nb-field__error" id="nb-login-identifier-error">{{ $message }}</p>
            @endunless
        @enderror
    </div>

    <!------- Password ------->
    <div class="nb-field @error('password') nb-field--invalid @enderror">
        <div class="nb-field__label-row">
            <label class="nb-field__label" for="nb-login-password">كلمة المرور</label>

            @if (\Illuminate\Support\Facades\Route::has('teacher.password.request'))
                <a class="nb-field__link" href="{{ route('teacher.password.request') }}">نسيت كلمة المرور؟</a>
            @endif
        </div>

        <div class="nb-field__control">
            <span class="nb-field__icon" aria-hidden="true">
                <svg viewBox="0 0 24 24" fill="none" focusable="false">
                    <rect x="5" y="10.5" width="14" height="9.5" rx="2.5" stroke="currentColor" stroke-width="1.7"/>
                    <path d="M8.25 10.5V8a3.75 3.75 0 0 1 7.5 0v2.5" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/>
                </svg>
            </span>

            <input
                id="nb-login-password"
                type="password"
                name="password"
                class="nb-input nb-input--with-action"
                wire:model="password"
                autocomplete="current-password"
                data-nb-password
                @error('password')
                    aria-invalid="true"
                    aria-describedby="nb-login-password-error"
                @enderror
            >

            <button
                type="button"
                class="nb-field__action"
                aria-label="إظهار كلمة المرور"
                aria-controls="nb-login-password"
                aria-pressed="false"
                data-nb-password-toggle
                wire:ignore
            >
                <svg class="nb-icon-eye" viewBox="0 0 24 24" fill="none" aria-hidden="true" focusable="false">
                    <path d="M2.75 12S6 5.75 12 5.75 21.25 12 21.25 12 18 18.25 12 18.25 2.75 12 2.75 12Z" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/>
                    <circle cx="12" cy="12" r="3" stroke="currentColor" stroke-width="1.7"/>
                </svg>
                <svg class="nb-icon-eye-off" viewBox="0 0 24 24" fill="none" aria-hidden="true" focusable="false">
                    <path d="M3.5 3.5l17 17" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/>
                    <path d="M10.6 6.9c.45-.1.92-.15 1.4-.15 6 0 9.25 5.25 9.25 5.25s-.9 1.5-2.65 3M6.6 7.9C4 9.7 2.75 12 2.75 12S6 18.25 12 18.25c1.55 0 2.9-.4 4.05-1" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M9.9 9.9a3 3 0 0 0 4.2 4.2" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/>
                </svg>
            </button>
        </div>

        <p class="nb-field__hint" id="nb-login-capslock" data-nb-capslock hidden wire:ignore>
            <svg viewBox="0 0 24 24" fill="none" aria-hidden="true" focusable="false">
                <path d="M12 4 4 12h4.5v4h7v-4H20L12 4Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
                <path d="M8.5 19.5h7" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
            </svg>
            زرار Caps Lock شغال
        </p>

        @error('password')
            <p class="nb-field__error" id="nb-login-password-error">{{ $message }}</p>
        @enderror
    </div>

    <!------- Remember Me ------->
    <label class="nb-check" for="rememberMe">
        <input
            class="nb-check__input"
            type="checkbox"
            id="rememberMe"
            name="remember"
            wire:model="remember"
        >
        <span class="nb-check__box" aria-hidden="true">
            <svg viewBox="0 0 24 24" fill="none" focusable="false">
                <path d="M5.5 12.5 10 17 18.5 7.5" stroke="currentColor" stroke-width="2.8" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </span>
        <span class="nb-check__label">افتكرني على الجهاز ده</span>
    </label>

    <!------- Submit ------->
    <button
        type="submit"
        class="nb-btn nb-btn--primary nb-btn--block"
        wire:loading.attr="disabled"
        wire:target="submit"
    >
        <span wire:loading.remove wire:target="submit">تسجيل الدخول</span>

        <span class="nb-btn__loading" wire:loading.flex wire:target="submit">
            <span class="nb-spinner" aria-hidden="true"></span>
            جاري تسجيل الدخول...
        </span>
    </button>

</form>
