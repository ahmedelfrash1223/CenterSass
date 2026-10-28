<?php

use App\Http\Controllers\Api\Student\Assignment\StudentAssignmentController;
use Illuminate\Support\Facades\Route;

Route::prefix('assignments')->name('assignments.')->group(function () {
    Route::get('/', [StudentAssignmentController::class, 'index'])->name('index');
    Route::get('stats', [StudentAssignmentController::class, 'stats'])->name('stats');
});
