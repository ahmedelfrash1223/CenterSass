<?php

namespace App\Livewire\AdminPanel\Auth;

use App\Http\Requests\AdminPanel\Auth\LoginRequest;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class LoginForm extends Component
{
    public $login;
    public $password;
    public $remember;


    #---------------------------------------------------------VALIDATION RULES
    public function rules()
    {
        return (new LoginRequest())->rules();
    }

    public function messages()
    {
        return (new LoginRequest())->messages();
    }


    #---------------------------------------------------------SUBMIT
    public function submit()
    {
        $this->validate();

        $field = filter_var($this->login, FILTER_VALIDATE_EMAIL)
            ? 'email'
            : 'username';

        if (!Auth::guard('admin')->attempt([
            $field => $this->login,
            'password' => $this->password,
            'is_active' => 1,
        ], $this->remember)) {
            throw ValidationException::withMessages([
                'login' => 'بيانات تسجيل الدخول غير صحيحة.',
            ]);
        }

        session()->regenerate();

        return to_route('admin.dashboard');
    }


    #---------------------------------------------------------RENDER
    public function render()
    {
        return view('admin-panel.auth.login-form');
    }
}
