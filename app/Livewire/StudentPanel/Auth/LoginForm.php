<?php

namespace App\Livewire\StudentPanel\Auth;

use App\Http\Requests\StudentPanel\Auth\LoginRequest;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Livewire\Component;

class LoginForm extends Component
{
    public string $login = '';

    public string $password = '';

    public function mount(): void
    {
        if (Auth::guard('student')->check()) {
            if (Auth::guard('student')->user()->is_active) {
                $this->redirectRoute('student.dashboard');
            } else {
                Auth::guard('student')->logout();
            }
        }
    }

    /** @return array<string, array<int, string>> */
    public function rules(): array
    {
        return (new LoginRequest)->rules();
    }

    /** @return array<string, string> */
    public function messages(): array
    {
        return (new LoginRequest)->messages();
    }

    public function submit(): RedirectResponse
    {
        $this->login = trim($this->login);
        $this->validate();

        $throttleKey = 'student-login:'.Str::lower($this->login).'|'.request()->ip();

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            throw ValidationException::withMessages([
                'login' => 'محاولات كثيرة. يرجى المحاولة بعد '.RateLimiter::availableIn($throttleKey).' ثانية.',
            ]);
        }

        if (filter_var($this->login, FILTER_VALIDATE_EMAIL)) {
            $field = 'email';
        } elseif (preg_match('/^[0-9]{10,15}$/', $this->login)) {
            $field = 'phone';
        } else {
            $field = 'username';
        }

        if (! Auth::guard('student')->attempt([
            $field => $this->login,
            'password' => $this->password,
            'is_active' => 1,
        ])) {
            RateLimiter::hit($throttleKey, 60);
            $this->reset('password');

            throw ValidationException::withMessages([
                'login' => 'بيانات تسجيل الدخول غير صحيحة.',
            ]);
        }

        RateLimiter::clear($throttleKey);
        session()->regenerate();

        return to_route('student.dashboard');
    }

    public function render(): View
    {
        return view('student-panel.auth.login-form');
    }
}
