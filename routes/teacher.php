<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TeacherPanel\BranchController;
use App\Http\Controllers\TeacherPanel\SubjectController;
use App\Http\Controllers\TeacherPanel\GroupController;
use App\Http\Controllers\TeacherPanel\StaffController;
use App\Enums\Teacher\TeacherPermissionEnum;
use App\Http\Controllers\TeacherPanel\StudentController;

#=================================== AUTH ROUTES ================================================>
Route::prefix('teacher')->name('teacher.')->group(function () {

    Route::view('login', 'teacher-panel.auth.login')->name('login');

});

#=================================== MAIN ROUTES ================================================>
Route::middleware('teacher')->prefix('teacher')->name('teacher.')->group(function () {

    // 📌 Dashboard
    Route::view('/', 'teacher-panel.pages.dashboard.index')->name('dashboard');

    // 📌 Profile
    Route::view('profile', 'teacher-panel.pages.profile.index')->name('profile');

    // 📌 Subscription
    Route::view('profile/subscription', 'teacher-panel.pages.profile.subscription')
        ->middleware('teacher.permission:' . TeacherPermissionEnum::MANAGE_SUBSCRIPTION->value)
        ->name('profile.subscription');

    // 📌 Branches
    Route::middleware('teacher.permission:' . TeacherPermissionEnum::BRANCH_CREATE->value)->group(function () {
        Route::get('branches/create', [BranchController::class, 'create'])->name('branches.create');
    });

    Route::middleware('teacher.permission:' . TeacherPermissionEnum::BRANCH_VIEW->value)->group(function () {
        Route::get('branches', [BranchController::class, 'index'])->name('branches.index');
    });

    Route::middleware('teacher.permission:' . TeacherPermissionEnum::BRANCH_UPDATE->value)->group(function () {
        Route::get('branches/{branch}/edit', [BranchController::class, 'edit'])->name('branches.edit');
    });

    // 📌 Subjects
    Route::middleware('teacher.permission:' . TeacherPermissionEnum::SUBJECT_CREATE->value)->group(function () {
        Route::get('subjects/create', [SubjectController::class, 'create'])->name('subjects.create');
    });

    Route::middleware('teacher.permission:' . TeacherPermissionEnum::SUBJECT_VIEW->value)->group(function () {
        Route::get('subjects', [SubjectController::class, 'index'])->name('subjects.index');
    });

    Route::middleware('teacher.permission:' . TeacherPermissionEnum::SUBJECT_UPDATE->value)->group(function () {
        Route::get('subjects/{subject}/edit', [SubjectController::class, 'edit'])->name('subjects.edit');
    });

    // 📌 Groups
    Route::middleware('teacher.permission:' . TeacherPermissionEnum::GROUP_CREATE->value)->group(function () {
        Route::get('groups/create', [GroupController::class, 'create'])->name('groups.create');
    });

    Route::middleware('teacher.permission:' . TeacherPermissionEnum::GROUP_VIEW->value)->group(function () {
        Route::get('groups', [GroupController::class, 'index'])->name('groups.index');
        Route::get('groups/{group}', [GroupController::class, 'show'])->name('groups.show');
    });

    Route::middleware('teacher.permission:' . TeacherPermissionEnum::GROUP_UPDATE->value)->group(function () {
        Route::get('groups/{group}/edit', [GroupController::class, 'edit'])->name('groups.edit');
    });

    // 📌 Staff
    Route::middleware('teacher.permission:' . TeacherPermissionEnum::STAFF_CREATE->value)->group(function () {
        Route::get('staff/create', [StaffController::class, 'create'])->name('staff.create');
    });

    Route::middleware('teacher.permission:' . TeacherPermissionEnum::STAFF_VIEW->value)->group(function () {
        Route::get('staff', [StaffController::class, 'index'])->name('staff.index');
        Route::get('staff/{staff}', [StaffController::class, 'show'])->name('staff.show');
    });

    Route::middleware('teacher.permission:' . TeacherPermissionEnum::STAFF_UPDATE->value)->group(function () {
        Route::get('staff/{staff}/edit', [StaffController::class, 'edit'])->name('staff.edit');
    });

    // 📌 Students
    Route::middleware('teacher.permission:' . TeacherPermissionEnum::STUDENT_CREATE->value)->group(function () {
        Route::get('students/create', [StudentController::class, 'create'])->name('students.create');
    });

    Route::middleware('teacher.permission:' . TeacherPermissionEnum::STUDENT_VIEW->value)->group(function () {
        Route::get('students', [StudentController::class, 'index'])->name('students.index');
        Route::get('students/{student}', [StudentController::class, 'show'])->name('students.show');
    });

    Route::middleware('teacher.permission:' . TeacherPermissionEnum::STUDENT_UPDATE->value)->group(function () {
        Route::get('students/{student}/edit', [StudentController::class, 'edit'])->name('students.edit');
    });

});
