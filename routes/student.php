<?php

use Illuminate\Support\Facades\Route;

// =============================================
// Student Authentication
// =============================================
Route::prefix('student')->name('student.')->group(function () {
    Route::view('login', 'student-panel.auth.login')->name('login');
});

// =============================================
// Student Panel
// =============================================
Route::middleware('student')->prefix('student')->name('student.')->group(function () {
    Route::view('/', 'student-panel.pages.dashboard.index')->name('dashboard');
    Route::view('profile', 'student-panel.pages.profile.index')->name('profile');
});
