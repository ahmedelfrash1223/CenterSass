<?php

use Illuminate\Support\Facades\Route;


// ============ Auth Routes (Public) ============
Route::prefix('auth')->name('auth.')->group(function () {
    require __DIR__ . '/auth/auth.php';
});


// Protected Routes  ( Student Only )
Route::middleware(['auth:sanctum', 'student.active', 'student.verified', 'student.only'])
    ->prefix('student')
    ->name('student.')
    ->group(function () {

        // Link Accounts Routes
        require __DIR__ . '/student/links.php';

        // Profile Routes
        require __DIR__ . '/student/profile.php';
        require __DIR__ . '/student/guardians.php';

        // Groups Routes
        require __DIR__ . '/student/groups.php';

        // Schedules Routes
        require __DIR__ . '/student/schedules.php';

        // Attendance Routes
        require __DIR__ . '/student/attendance.php';

        // Assignments Routes
        require __DIR__ . '/student/assignments.php';

        // Invoices Routes
        require __DIR__ . '/student/invoices.php';

        // Home Routes
        require __DIR__ . '/student/home.php';
    });

// Protected Routes  ( Parent Only )
Route::middleware(['auth:sanctum', 'student.active', 'student.verified', 'parent.only'])
    ->prefix('parent')
    ->name('parent.')
    ->group(function () {

        require __DIR__ . '/parent/children.php';

    });


