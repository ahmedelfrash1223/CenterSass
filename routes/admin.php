<?php

use App\Http\Controllers\AdminPanel\PackageController;
use App\Http\Controllers\AdminPanel\TeacherController;
use Illuminate\Support\Facades\Route;
#=================================== AUTH ROUTES ================================================>
Route::prefix('admin')->name('admin.')->group(function () {

    //Route::resource('login',AdminAuthController::class);
    Route::view('login', 'admin-panel.auth.login')->name('login');

});

#=================================== MAIN ROUTES ================================================>
Route::middleware('admin')->prefix('admin')->name('admin.')->group(function () {

    Route::view('/', 'admin-panel.pages.dashboard.index')->name('dashboard');
    Route::view('profile', 'admin-panel.pages.profile.index')->name('profile');
    Route::resource('/package', PackageController::class);
    Route::resource('/teacher', TeacherController::class);
});

