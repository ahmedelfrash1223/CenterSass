<?php

use App\Http\Controllers\Api\Student\Guardian\StudentGuardianController;
use Illuminate\Support\Facades\Route;

Route::prefix('guardians')->name('guardians.')->group(function () {
    Route::get('/', [StudentGuardianController::class, 'index'])->name('index');
    Route::get('{studentId}', [StudentGuardianController::class, 'show'])
        ->whereNumber('studentId')
        ->name('show');
});
