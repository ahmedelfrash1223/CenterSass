<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes Master
|--------------------------------------------------------------------------
|
*/

Route::prefix('student')
    ->name('student.')
    ->group(function () {
        require __DIR__ . '/student/auth.php';
    });
