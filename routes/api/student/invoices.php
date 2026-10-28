<?php

use App\Http\Controllers\Api\Student\Invoice\StudentInvoiceController;
use Illuminate\Support\Facades\Route;

Route::prefix('invoices')->name('invoices.')->group(function () {
    Route::get('/', [StudentInvoiceController::class, 'index'])->name('index');
    Route::get('stats', [StudentInvoiceController::class, 'stats'])->name('stats');
});
