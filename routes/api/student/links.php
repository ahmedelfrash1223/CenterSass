<?php

use App\Http\Controllers\Api\Student\Link\StudentLinkController;
use Illuminate\Support\Facades\Route;

Route::prefix('links')->name('links.')->group(function () {
    Route::post('scan', [StudentLinkController::class, 'scan'])->name('scan');
    Route::get('/', [StudentLinkController::class, 'index'])->name('index');
    Route::delete('/', [StudentLinkController::class, 'unlink'])->name('unlink');
});
