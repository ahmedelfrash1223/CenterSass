<?php

use App\Http\Controllers\Api\Student\Group\StudentGroupController;
use Illuminate\Support\Facades\Route;

Route::prefix('groups')->name('groups.')->group(function () {
    Route::get('/', [StudentGroupController::class, 'index'])->name('index');
    Route::get('{groupId}', [StudentGroupController::class, 'show'])
        ->whereNumber('groupId')
        ->name('show');
});
