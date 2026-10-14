<?php

use App\Enums\Teacher\TeacherPermissionEnum;
use App\Http\Controllers\TeacherPanel\AssignmentController;
use App\Http\Controllers\TeacherPanel\AttendanceCloseDayController;
use App\Http\Controllers\TeacherPanel\AttendanceController;
use App\Http\Controllers\TeacherPanel\BookingController;
use App\Http\Controllers\TeacherPanel\BranchController;
use App\Http\Controllers\TeacherPanel\CardRequestController;
use App\Http\Controllers\TeacherPanel\ExamController;
use App\Http\Controllers\TeacherPanel\ExpenseController;
use App\Http\Controllers\TeacherPanel\GroupController;
use App\Http\Controllers\TeacherPanel\InvoiceController;
use App\Http\Controllers\TeacherPanel\PaymentController;
use App\Http\Controllers\TeacherPanel\PayrollController;
use App\Http\Controllers\TeacherPanel\RevenueController;
use App\Http\Controllers\TeacherPanel\StaffController;
use App\Http\Controllers\TeacherPanel\StudentController;
use App\Http\Controllers\TeacherPanel\SubjectController;
use App\Http\Controllers\TeacherPanel\SupportTicketController;
use Illuminate\Support\Facades\Route;

// =============================================
// Teacher Authentication
// =============================================
Route::prefix('teacher')->name('teacher.')->group(function () {
    Route::view('login', 'teacher-panel.auth.login')->name('login');
});

// =============================================
// Public Routes
// =============================================
Route::get('receipts/verify/{receiptNumber}', [PaymentController::class, 'verify'])
    ->name('teacher.payments.verify');

// =============================================
// Teacher Panel
// =============================================
Route::middleware('teacher')->prefix('teacher')->name('teacher.')->group(function () {
    Route::view('/', 'teacher-panel.pages.dashboard.index')->name('dashboard');

    Route::view('profile', 'teacher-panel.pages.profile.index')->name('profile');

    Route::view('profile/subscription', 'teacher-panel.pages.profile.subscription')
        ->middleware('teacher.permission:'.TeacherPermissionEnum::MANAGE_SUBSCRIPTION->value)
        ->name('profile.subscription');

    // =============================================
    // Branches
    // =============================================
    Route::middleware('teacher.permission:'.TeacherPermissionEnum::BRANCH_CREATE->value)->group(function () {
        Route::get('branches/create', [BranchController::class, 'create'])->name('branches.create');
    });

    Route::middleware('teacher.permission:'.TeacherPermissionEnum::BRANCH_VIEW->value)->group(function () {
        Route::get('branches', [BranchController::class, 'index'])->name('branches.index');
    });

    Route::middleware('teacher.permission:'.TeacherPermissionEnum::BRANCH_UPDATE->value)->group(function () {
        Route::get('branches/{branch}/edit', [BranchController::class, 'edit'])->name('branches.edit');
    });

    // =============================================
    // Subjects
    // =============================================
    Route::middleware('teacher.permission:'.TeacherPermissionEnum::SUBJECT_CREATE->value)->group(function () {
        Route::get('subjects/create', [SubjectController::class, 'create'])->name('subjects.create');
    });

    Route::middleware('teacher.permission:'.TeacherPermissionEnum::SUBJECT_VIEW->value)->group(function () {
        Route::get('subjects', [SubjectController::class, 'index'])->name('subjects.index');
    });

    Route::middleware('teacher.permission:'.TeacherPermissionEnum::SUBJECT_UPDATE->value)->group(function () {
        Route::get('subjects/{subject}/edit', [SubjectController::class, 'edit'])->name('subjects.edit');
    });

    // =============================================
    // Groups
    // =============================================
    Route::middleware('teacher.permission:'.TeacherPermissionEnum::GROUP_CREATE->value)->group(function () {
        Route::get('groups/create', [GroupController::class, 'create'])->name('groups.create');
    });

    Route::middleware('teacher.permission:'.TeacherPermissionEnum::GROUP_VIEW->value)->group(function () {
        Route::get('groups', [GroupController::class, 'index'])->name('groups.index');
        Route::get('groups/{group}', [GroupController::class, 'show'])->name('groups.show');
    });

    Route::middleware('teacher.permission:'.TeacherPermissionEnum::GROUP_UPDATE->value)->group(function () {
        Route::get('groups/{group}/edit', [GroupController::class, 'edit'])->name('groups.edit');
    });

    // =============================================
    // Staff
    // =============================================
    Route::middleware('teacher.permission:'.TeacherPermissionEnum::STAFF_CREATE->value)->group(function () {
        Route::get('staff/create', [StaffController::class, 'create'])->name('staff.create');
    });

    Route::middleware('teacher.permission:'.TeacherPermissionEnum::STAFF_VIEW->value)->group(function () {
        Route::get('staff', [StaffController::class, 'index'])->name('staff.index');
        Route::get('staff/{staff}', [StaffController::class, 'show'])->name('staff.show');
    });

    Route::middleware('teacher.permission:'.TeacherPermissionEnum::STAFF_UPDATE->value)->group(function () {
        Route::get('staff/{staff}/edit', [StaffController::class, 'edit'])->name('staff.edit');
    });

    // =============================================
    // Students
    // =============================================
    Route::middleware('teacher.permission:'.TeacherPermissionEnum::STUDENT_CREATE->value)->group(function () {
        Route::get('students/create', [StudentController::class, 'create'])->name('students.create');
    });

    Route::middleware('teacher.permission:'.TeacherPermissionEnum::STUDENT_VIEW->value)->group(function () {
        Route::get('students', [StudentController::class, 'index'])->name('students.index');
        Route::get('students/{student}', [StudentController::class, 'show'])->name('students.show');
    });

    Route::middleware('teacher.permission:'.TeacherPermissionEnum::STUDENT_UPDATE->value)->group(function () {
        Route::get('students/{student}/edit', [StudentController::class, 'edit'])->name('students.edit');
    });

    // =============================================
    // Attendance
    // =============================================
    Route::middleware('teacher.permission:'.TeacherPermissionEnum::ATTENDANCE_MANAGE->value)->group(function () {
        Route::get('attendance', [AttendanceController::class, 'index'])->name('attendance.index');
    });

    Route::middleware('teacher.permission:'.TeacherPermissionEnum::ATTENDANCE_CLOSE_DAY->value)->group(function () {
        Route::get('attendance/close-day', [AttendanceCloseDayController::class, 'index'])->name('attendance.close-day.index');
        Route::get('attendance/close-day/{group}', [AttendanceCloseDayController::class, 'show'])->name('attendance.close-day.show');
    });

    Route::middleware('teacher.permission:'.TeacherPermissionEnum::ATTENDANCE_VIEW->value)->group(function () {
        Route::get('attendance/records', [AttendanceController::class, 'records'])->name('attendance.records');
        Route::get('attendance/{attendance}', [AttendanceController::class, 'show'])->name('attendance.show');
    });

    // =============================================
    // Payments & Invoices
    // =============================================
    Route::middleware('teacher.permission:'.TeacherPermissionEnum::PAYMENT_VIEW->value)->group(function () {
        Route::view('payments', 'teacher-panel.pages.payment.index')->name('payments.index');
        Route::get('invoices', [InvoiceController::class, 'index'])->name('invoices.index');
        Route::get('invoices/{invoice}', [InvoiceController::class, 'show'])->name('invoices.show');
    });

    // =============================================
    // Timetable
    // =============================================
    Route::middleware('teacher.permission:'.TeacherPermissionEnum::TIMETABLE_VIEW->value)->group(function () {
        Route::view('timetable', 'teacher-panel.pages.timetable.index')->name('timetable.index');
    });

    // =============================================
    // Exams
    // =============================================
    Route::middleware('teacher.permission:'.TeacherPermissionEnum::EXAM_CREATE->value)->group(function () {
        Route::get('exams/create', [ExamController::class, 'create'])->name('exams.create');
    });

    Route::middleware('teacher.permission:'.TeacherPermissionEnum::EXAM_VIEW->value)->group(function () {
        Route::get('exams', [ExamController::class, 'index'])->name('exams.index');
        Route::get('exams/{exam}', [ExamController::class, 'show'])->name('exams.show');
    });

    Route::middleware('teacher.permission:'.TeacherPermissionEnum::EXAM_UPDATE->value)->group(function () {
        Route::get('exams/{exam}/edit', [ExamController::class, 'edit'])->name('exams.edit');
    });

    // =============================================
    // Bookings
    // =============================================
    Route::middleware('teacher.permission:'.TeacherPermissionEnum::BOOKING_CREATE->value)->group(function () {
        Route::get('bookings/create', [BookingController::class, 'create'])->name('bookings.create');
    });

    Route::middleware('teacher.permission:'.TeacherPermissionEnum::BOOKING_VIEW->value)->group(function () {
        Route::get('bookings', [BookingController::class, 'index'])->name('bookings.index');
        Route::get('bookings/{booking}', [BookingController::class, 'show'])->name('bookings.show');
    });

    Route::middleware('teacher.permission:'.TeacherPermissionEnum::BOOKING_UPDATE->value)->group(function () {
        Route::get('bookings/{booking}/edit', [BookingController::class, 'edit'])->name('bookings.edit');
    });

    // =============================================
    // Assignments (المتابعة)
    // =============================================
    Route::middleware('teacher.permission:'.TeacherPermissionEnum::ASSIGNMENT_CREATE->value)->group(function () {
        Route::get('assignments/create', [AssignmentController::class, 'create'])->name('assignments.create');
    });

    Route::middleware('teacher.permission:'.TeacherPermissionEnum::ASSIGNMENT_VIEW->value)->group(function () {
        Route::get('assignments', [AssignmentController::class, 'index'])->name('assignments.index');
        Route::get('assignments/{assignment}', [AssignmentController::class, 'show'])->name('assignments.show');
    });

    Route::middleware('teacher.permission:'.TeacherPermissionEnum::ASSIGNMENT_UPDATE->value)->group(function () {
        Route::get('assignments/{assignment}/edit', [AssignmentController::class, 'edit'])->name('assignments.edit');
    });

    // =============================================
    // Card Requests
    // =============================================
    Route::middleware('teacher.permission:'.TeacherPermissionEnum::CARD_REQUEST_CREATE->value)->group(function () {
        Route::get('card-requests/create', [CardRequestController::class, 'create'])
            ->name('card-requests.create');
    });

    Route::middleware('teacher.permission:'.TeacherPermissionEnum::CARD_REQUEST_VIEW->value)->group(function () {
        Route::get('card-requests', [CardRequestController::class, 'index'])
            ->name('card-requests.index');
        Route::get('card-requests/{cardRequest}', [CardRequestController::class, 'show'])
            ->name('card-requests.show');
    });

    // =============================================
    // Support
    // =============================================
    Route::middleware('teacher.permission:'.TeacherPermissionEnum::SUPPORT_CREATE->value)->group(function () {
        Route::get('support/create', [SupportTicketController::class, 'create'])->name('support.create');
    });

    Route::middleware('teacher.permission:'.TeacherPermissionEnum::SUPPORT_VIEW->value)->group(function () {
        Route::get('support', [SupportTicketController::class, 'index'])->name('support.index');
        Route::get('support/{ticket}', [SupportTicketController::class, 'show'])->name('support.show');
    });

    // =============================================
    // Revenues
    // =============================================
    Route::middleware('teacher.permission:'.TeacherPermissionEnum::REVENUE_VIEW->value)->group(function () {
        Route::get('revenues', [RevenueController::class, 'index'])->name('revenues.index');
    });

    // =============================================
    // Payrolls (حسابات المدرسين)
    // =============================================

    // generate لازم تكون قبل {payroll}
    Route::middleware('teacher.permission:'.TeacherPermissionEnum::PAYROLL_GENERATE->value)->group(function () {
        Route::get('payrolls/generate', [PayrollController::class, 'generate'])->name('payrolls.generate');
    });

    Route::middleware('teacher.permission:'.TeacherPermissionEnum::PAYROLL_MANAGE->value)->group(function () {
        Route::get('payrolls/settings/{teacher}', [PayrollController::class, 'settings'])->name('payrolls.settings');
    });

    Route::middleware('teacher.permission:'.TeacherPermissionEnum::PAYROLL_VIEW->value)->group(function () {
        Route::get('payrolls', [PayrollController::class, 'index'])->name('payrolls.index');
        Route::get('payrolls/{payroll}', [PayrollController::class, 'show'])->name('payrolls.show');
    });

    // =============================================
    // Expenses (المصروفات)
    // =============================================
    Route::middleware('teacher.permission:'.TeacherPermissionEnum::EXPENSE_VIEW->value)->group(function () {
        Route::get('expenses', [ExpenseController::class, 'index'])->name('expenses.index');
    });

});
