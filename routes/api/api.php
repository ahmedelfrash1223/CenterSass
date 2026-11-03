<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes Master
|--------------------------------------------------------------------------
|
*/

Route::prefix('student')->name('student.')->group(function () {

    // Auth Routes
    require __DIR__ . '/student/auth.php';

    // Protected Routes
    Route::middleware(['auth:sanctum', 'student.active', 'student.verified'])->group(function () {

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

    Route::middleware(['auth:sanctum', 'student.active', 'student.verified', 'parent.only'])->group(function () {

        require __DIR__ . '/student/parent/children.php';

    });

});

