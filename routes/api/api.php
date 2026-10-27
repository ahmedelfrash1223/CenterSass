<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes Master
|--------------------------------------------------------------------------
|
*/

Route::prefix('student')->name('student.')->group(function () {

    // Auth Routes
    require __DIR__ . '/student/auth.php';

    // Protected Routes
    Route::middleware(['auth:sanctum', 'student.active'])->group(function () {

        // Link Accounts Routes
        require __DIR__ . '/student/links.php';
    });

});

