<?php

use App\Http\Controllers\Api\Student\Profile\StudentProfileController;
use App\Http\Controllers\Api\Student\Profile\StudentIdCardController;
use Illuminate\Support\Facades\Route;

Route::prefix('profiles')->name('profiles.')->group(function () {
    Route::get('/', [StudentProfileController::class, 'index'])->name('index');
    Route::get('{studentId}', [StudentProfileController::class, 'show'])
        ->whereNumber('studentId')
        ->name('show');

    Route::get('id-card', [StudentIdCardController::class,'index'])->name('id-card');

});
