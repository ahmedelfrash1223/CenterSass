@section('title','تسجيل الدخول')
@extends('admin-panel.auth.master')
@section('main')
    <div class="auth-main v1">
        <div class="auth-wrapper">
            <div class="auth-form">
                <div class="card border-0 shadow-sm my-5" style="border-radius:20px;">
                    <div class="card-body p-4 p-md-5">

                        <div class="text-center mb-4">
                            <img
                                src="{{ asset('assets/adminPanel/images/logo.png') }}"
                                alt="VSoft"
                                class="img-fluid mb-3"
                                style="max-height:120px;"
                            >

                            <h4 class="fw-bold text-dark mb-1">
                                سجل الدخول إلى حسابك
                            </h4>

                        </div>
                        @livewire('admin-panel.auth.login-form')
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
