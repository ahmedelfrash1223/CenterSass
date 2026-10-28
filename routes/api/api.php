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
    Route::middleware(['auth:sanctum', 'student.active'])->group(function () {

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
    });

});

