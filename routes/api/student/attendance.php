<?php

use App\Http\Controllers\Api\Student\Attendance\StudentAttendanceController;
use Illuminate\Support\Facades\Route;

Route::prefix('attendance')->name('attendance.')->group(function () {
    Route::get('/', [StudentAttendanceController::class, 'index'])->name('index');
    Route::get('stats', [StudentAttendanceController::class, 'stats'])->name('stats');
});
