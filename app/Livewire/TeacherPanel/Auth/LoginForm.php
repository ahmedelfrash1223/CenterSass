<?php

namespace App\Livewire\TeacherPanel\Auth;

use App\Http\Requests\TeacherPanel\Auth\LoginRequest;
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

        if (filter_var($this->login, FILTER_VALIDATE_EMAIL)) {
            $field = 'email';
        } elseif (preg_match('/^[0-9]{10,15}$/', $this->login)) {
            $field = 'phone';
        } else {
            $field = 'username';
        }

        if (!Auth::guard('teacher')->attempt([
            $field => $this->login,
            'password' => $this->password,
            'is_active' => 1,
        ], $this->remember)) {
            throw ValidationException::withMessages([
                'login' => 'بيانات تسجيل الدخول غير صحيحة.',
            ]);
        }

        session()->regenerate();

        return to_route('teacher.dashboard');
    }


    #---------------------------------------------------------RENDER
    public function render()
    {
        return view('teacher-panel.auth.login-form');
    }
}
