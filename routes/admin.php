<?php

use App\Http\Controllers\AdminPanel\AdminController;
use App\Http\Controllers\AdminPanel\CardRequestController;
use App\Http\Controllers\AdminPanel\CardRequestExportController;
use App\Http\Controllers\AdminPanel\PackageController;
use App\Http\Controllers\AdminPanel\SubscriptionController;
use App\Http\Controllers\AdminPanel\SupportTicketController;
use App\Http\Controllers\AdminPanel\TeacherController;
use Illuminate\Support\Facades\Route;

// =================================== AUTH ROUTES ================================================>
Route::prefix('admin')->name('admin.')->group(function () {

    // Route::resource('login',AdminAuthController::class);
    Route::view('login', 'admin-panel.auth.login')->name('login');

});

// =================================== MAIN ROUTES ================================================>
Route::middleware('admin')->prefix('admin')->name('admin.')->group(function () {

    Route::view('/', 'admin-panel.pages.dashboard.index')->name('dashboard');
    Route::view('profile', 'admin-panel.pages.profile.index')->name('profile');
    Route::resource('/package', PackageController::class);
    Route::resource('/teacher', TeacherController::class);
    Route::resource('/admins', AdminController::class)->parameters(['admins' => 'admin']);
    Route::resource('/subscriptions', SubscriptionController::class)->parameters(['subscriptions' => 'subscription']);
    Route::view('subscription-requests', 'admin-panel.pages.subscription-request.index')->name('subscription-request');

    Route::get('card-requests', [CardRequestController::class, 'index'])->name('card-requests.index');
    Route::get('card-requests/{cardRequest}', [CardRequestController::class, 'show'])->name('card-requests.show');

    Route::get('support', [SupportTicketController::class, 'index'])->name('support.index');
    Route::get('support/{ticket}', [SupportTicketController::class, 'show'])->name('support.show');

    Route::get('card-requests/{cardRequest}/export-zip', [CardRequestExportController::class, 'downloadZip'])
        ->name('card-requests.export-zip');
});
