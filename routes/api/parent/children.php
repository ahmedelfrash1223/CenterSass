<?php

use App\Http\Controllers\Api\Parent\ParentChildrenController;
use Illuminate\Support\Facades\Route;

Route::prefix('children')->name('children.')->group(function () {
    Route::get('/', [ParentChildrenController::class, 'index'])->name('index');

    Route::get('{studentId}', [ParentChildrenController::class, 'show'])->name('show');

    Route::prefix('{studentId}')->name('child.')->group(function () {
        Route::get('schedules', [ParentChildrenController::class, 'schedules'])->name('schedules');
        Route::get('attendance', [ParentChildrenController::class, 'attendance'])->name('attendance');
        Route::get('attendance/stats', [ParentChildrenController::class, 'attendanceStats'])->name('attendance.stats');
        Route::get('invoices', [ParentChildrenController::class, 'invoices'])->name('invoices');
        Route::get('invoices/stats', [ParentChildrenController::class, 'invoiceStats'])->name('invoices.stats');
        Route::get('assignments', [ParentChildrenController::class, 'assignments'])->name('assignments');
        Route::get('assignments/stats', [ParentChildrenController::class, 'assignmentStats'])->name('assignments.stats');
        Route::get('groups', [ParentChildrenController::class, 'groups'])->name('groups');
    });
});
