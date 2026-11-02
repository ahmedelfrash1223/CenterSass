<?php

use App\Http\Controllers\Api\Student\Schedule\StudentScheduleController;
use Illuminate\Support\Facades\Route;

Route::prefix('schedules')->name('schedules.')->group(function () {
    Route::get('/', [StudentScheduleController::class, 'index'])->name('index');
});
