<?php

use App\Http\Controllers\Api\Student\Parent\ParentChildrenController;
use Illuminate\Support\Facades\Route;

Route::prefix('parent')->name('parent.')->group(function () {
    Route::get('children', [ParentChildrenController::class, 'index'])->name('children.index');

    Route::get('children/{studentId}', [ParentChildrenController::class, 'show'])->name('children.show');

    Route::prefix('children/{studentId}')->name('children.')->group(function () {


        Route::get('attendance', [ParentChildrenController::class, 'attendance'])->name('attendance');
        Route::get('attendance/stats', [ParentChildrenController::class, 'attendanceStats'])->name('attendance.stats');

        Route::get('invoices', [ParentChildrenController::class, 'invoices'])->name('invoices');
        Route::get('invoices/stats', [ParentChildrenController::class, 'invoiceStats'])->name('invoices.stats');

        Route::get('assignments', [ParentChildrenController::class, 'assignments'])->name('assignments');
        Route::get('assignments/stats', [ParentChildrenController::class, 'assignmentStats'])->name('assignments.stats');

        Route::get('schedules', [ParentChildrenController::class, 'schedules'])->name('schedules');

        Route::get('groups', [ParentChildrenController::class, 'groups'])->name('groups');
    });
});
