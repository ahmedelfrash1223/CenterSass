<?php

use App\Http\Controllers\Api\Student\Auth\GoogleController;
use App\Http\Controllers\Api\Student\Auth\LoginController;
use App\Http\Controllers\Api\Student\Auth\LogoutController;
use App\Http\Controllers\Api\Student\Auth\OtpController;
use App\Http\Controllers\Api\Student\Auth\RegisterController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Student Auth Routes
|--------------------------------------------------------------------------
*/

// ─── Public ────────────────────────────────────────────
Route::post('register', [RegisterController::class, 'register'])
    ->name('auth.register');

Route::post('login', [LoginController::class, 'login'])
    ->name('auth.login');

Route::post('google', [GoogleController::class, 'callback'])
    ->name('auth.google');

Route::post('otp/verify', [OtpController::class, 'verify'])
    ->name('auth.otp.verify');

Route::post('otp/resend', [OtpController::class, 'resend'])
    ->name('auth.otp.resend');

// ─── Protected ─────────────────────────────────────────
Route::middleware(['auth:sanctum', 'student.active', 'student.verified'])->group(function () {
    Route::get('me', [LoginController::class, 'me'])->name('me');
    Route::post('logout', [LogoutController::class, 'logout'])->name('auth.logout');
});
