<?php

use Illuminate\Support\Facades\Route;


#=================================== AUTH ROUTES ================================================>
Route::prefix('teacher')->name('teacher.')->group(function () {

    Route::view('login', 'teacher-panel.auth.login')->name('login');

});



#=================================== MAIN ROUTES ================================================>
Route::middleware('teacher')->prefix('teacher')->name('teacher.')->group(function () {

    Route::view('/', 'teacher-panel.pages.dashboard.index')->name('dashboard');
    Route::view('profile', 'teacher-panel.pages.profile.index')->name('profile');
    Route::view('profile/subscription', 'teacher-panel.pages.profile.subscription')->name('profile.subscription');


});
