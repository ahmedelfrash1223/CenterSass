<?php

use Illuminate\Support\Facades\Route;

Route::name('landing.')->group(function () {

    Route::view('/', 'landing.pages.index.index')->name('index');
    Route::view('/features', 'landing.pages.features.index')->name('features');
    Route::view('/pricing', 'landing.pages.pricing.index')->name('pricing');
    Route::view('/student-portal', 'landing.pages.student-portal.index')->name('student-portal');

});
