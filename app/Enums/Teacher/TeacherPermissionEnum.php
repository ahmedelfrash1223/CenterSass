<?php

namespace App\Enums\Teacher;

enum TeacherPermissionEnum: string
{
    // =============================================
    // Subscription
    // =============================================
    case MANAGE_SUBSCRIPTION = 'manage_subscription';

    // =============================================
    // Staff
    // =============================================
    case STAFF_VIEW = 'staff.view';
    case STAFF_CREATE = 'staff.create';
    case STAFF_UPDATE = 'staff.update';
    case STAFF_DELETE = 'staff.delete';

    // =============================================
    // Branches
    // =============================================
    case BRANCH_VIEW = 'branches.view';
    case BRANCH_CREATE = 'branches.create';
    case BRANCH_UPDATE = 'branches.update';
    case BRANCH_DELETE = 'branches.delete';

    // =============================================
    // Subjects
    // =============================================
    case SUBJECT_VIEW = 'subjects.view';
    case SUBJECT_CREATE = 'subjects.create';
    case SUBJECT_UPDATE = 'subjects.update';
    case SUBJECT_DELETE = 'subjects.delete';

    // =============================================
    // Groups
    // =============================================
    case GROUP_VIEW = 'groups.view';
    case GROUP_CREATE = 'groups.create';
    case GROUP_UPDATE = 'groups.update';
    case GROUP_DELETE = 'groups.delete';

    // =============================================
    // Students
    // =============================================
    case STUDENT_VIEW = 'students.view';
    case STUDENT_CREATE = 'students.create';
    case STUDENT_UPDATE = 'students.update';
    case STUDENT_DELETE = 'students.delete';

    // =============================================
    // Attendance
    // =============================================
    case ATTENDANCE_VIEW = 'attendance.view';
    case ATTENDANCE_MANAGE = 'attendance.manage';
    case ATTENDANCE_CLOSE_DAY = 'attendance.close_day';

    // =============================================
    // Timetable
    // =============================================
    case TIMETABLE_VIEW = 'timetable.view';

    // =============================================
    // Reports
    // =============================================
    case VIEW_REPORTS = 'reports.view';

    // =============================================
    // Settings
    // =============================================
    case MANAGE_SETTINGS = 'settings.manage';

    // =============================================
    // Payments & Invoices
    // =============================================
    case PAYMENT_VIEW = 'payments.view';
    case PAYMENT_MANAGE = 'payments.manage';
    case PAYMENT_RECEIPT = 'payments.receipt';
    case PAYMENT_DELETE = 'payments.delete';

    // =============================================
    // Exams
    // =============================================
    case EXAM_VIEW = 'exams.view';
    case EXAM_CREATE = 'exams.create';
    case EXAM_UPDATE = 'exams.update';
    case EXAM_DELETE = 'exams.delete';
    case EXAM_PUBLISH = 'exams.publish';
    case EXAM_MANAGE_QUESTIONS = 'exams.manage_questions';

    // =============================================
    // Bookings
    // =============================================
    case BOOKING_VIEW = 'bookings.view';
    case BOOKING_CREATE = 'bookings.create';
    case BOOKING_UPDATE = 'bookings.update';
    case BOOKING_DELETE = 'bookings.delete';
    case BOOKING_CONFIRM = 'bookings.confirm';

    // =============================================
    // Assignments
    // =============================================
    case ASSIGNMENT_VIEW = 'assignments.view';
    case ASSIGNMENT_CREATE = 'assignments.create';
    case ASSIGNMENT_UPDATE = 'assignments.update';
    case ASSIGNMENT_DELETE = 'assignments.delete';
    case ASSIGNMENT_GRADE = 'assignments.grade';

    // =============================================
    // Card Requests
    // =============================================
    case CARD_REQUEST_VIEW = 'card_requests.view';
    case CARD_REQUEST_CREATE = 'card_requests.create';
    case CARD_REQUEST_DELETE = 'card_requests.delete';

    // =============================================
    // Support Tickets
    // =============================================
    case SUPPORT_VIEW = 'support.view';
    case SUPPORT_CREATE = 'support.create';
    case SUPPORT_REPLY = 'support.reply';
    case SUPPORT_CLOSE = 'support.close';

    // =============================================
    // Labels
    // =============================================
    public function label(): string
    {
        return match ($this) {
            self::MANAGE_SUBSCRIPTION => 'إدارة الاشتراك',

            self::STAFF_VIEW => 'عرض الفريق',
            self::STAFF_CREATE => 'إضافة عضو',
            self::STAFF_UPDATE => 'تعديل عضو',
            self::STAFF_DELETE => 'حذف عضو',

            self::BRANCH_VIEW => 'عرض الفروع',
            self::BRANCH_CREATE => 'إضافة فرع',
            self::BRANCH_UPDATE => 'تعديل فرع',
            self::BRANCH_DELETE => 'حذف فرع',

            self::SUBJECT_VIEW => 'عرض المواد',
            self::SUBJECT_CREATE => 'إضافة مادة',
            self::SUBJECT_UPDATE => 'تعديل مادة',
            self::SUBJECT_DELETE => 'حذف مادة',

            self::GROUP_VIEW => 'عرض المجموعات',
            self::GROUP_CREATE => 'إضافة مجموعة',
            self::GROUP_UPDATE => 'تعديل مجموعة',
            self::GROUP_DELETE => 'حذف مجموعة',

            self::STUDENT_VIEW => 'عرض الطلاب',
            self::STUDENT_CREATE => 'إضافة طالب',
            self::STUDENT_UPDATE => 'تعديل طالب',
            self::STUDENT_DELETE => 'حذف طالب',

            self::ATTENDANCE_VIEW => 'عرض الحضور',
            self::ATTENDANCE_MANAGE => 'تسجيل وتعديل الحضور',
            self::ATTENDANCE_CLOSE_DAY => 'تقفيل اليوم',

            self::TIMETABLE_VIEW => 'عرض جدول الأسبوع',

            self::VIEW_REPORTS => 'عرض التقارير',
            self::MANAGE_SETTINGS => 'إدارة الإعدادات',

            self::PAYMENT_VIEW => 'عرض المدفوعات',
            self::PAYMENT_MANAGE => 'تسجيل وتعديل المدفوعات',
            self::PAYMENT_RECEIPT => 'طباعة الإيصالات',
            self::PAYMENT_DELETE => 'حذف وتعديل الفواتير',

            self::EXAM_VIEW => 'عرض الامتحانات',
            self::EXAM_CREATE => 'إضافة امتحان',
            self::EXAM_UPDATE => 'تعديل امتحان',
            self::EXAM_DELETE => 'حذف امتحان',
            self::EXAM_PUBLISH => 'نشر الامتحانات',
            self::EXAM_MANAGE_QUESTIONS => 'إدارة أسئلة الامتحانات',

            self::BOOKING_VIEW => 'عرض الحجوزات',
            self::BOOKING_CREATE => 'إضافة حجز',
            self::BOOKING_UPDATE => 'تعديل حجز',
            self::BOOKING_DELETE => 'حذف حجز',
            self::BOOKING_CONFIRM => 'تأكيد الحجز',

            self::ASSIGNMENT_VIEW => 'عرض المتابعة',
            self::ASSIGNMENT_CREATE => 'إضافة متابعة',
            self::ASSIGNMENT_UPDATE => 'تعديل متابعة',
            self::ASSIGNMENT_DELETE => 'حذف متابعة',
            self::ASSIGNMENT_GRADE => 'تقييم الطلاب',

            self::CARD_REQUEST_VIEW => 'عرض طلبات الكروت',
            self::CARD_REQUEST_CREATE => 'طلب كروت',
            self::CARD_REQUEST_DELETE => 'إلغاء طلب كروت',

            self::SUPPORT_VIEW => 'عرض تذاكر الدعم',
            self::SUPPORT_CREATE => 'إنشاء تذكرة دعم',
            self::SUPPORT_REPLY => 'الرد على تذاكر الدعم',
            self::SUPPORT_CLOSE => 'إغلاق تذاكر الدعم',
        };
    }

    // =============================================
    // Permission Groups
    // =============================================
    public function group(): string
    {
        return match ($this) {
            self::MANAGE_SUBSCRIPTION => 'الاشتراك',

            self::STAFF_VIEW,
            self::STAFF_CREATE,
            self::STAFF_UPDATE,
            self::STAFF_DELETE => 'الفريق',

            self::BRANCH_VIEW,
            self::BRANCH_CREATE,
            self::BRANCH_UPDATE,
            self::BRANCH_DELETE => 'الفروع',

            self::SUBJECT_VIEW,
            self::SUBJECT_CREATE,
            self::SUBJECT_UPDATE,
            self::SUBJECT_DELETE => 'المواد',

            self::GROUP_VIEW,
            self::GROUP_CREATE,
            self::GROUP_UPDATE,
            self::GROUP_DELETE => 'المجموعات',

            self::STUDENT_VIEW,
            self::STUDENT_CREATE,
            self::STUDENT_UPDATE,
            self::STUDENT_DELETE => 'الطلاب',

            self::ATTENDANCE_VIEW,
            self::ATTENDANCE_MANAGE,
            self::ATTENDANCE_CLOSE_DAY => 'الحضور',

            self::TIMETABLE_VIEW => 'جدول الأسبوع',

            self::VIEW_REPORTS => 'التقارير',

            self::MANAGE_SETTINGS => 'الإعدادات',

            self::PAYMENT_VIEW,
            self::PAYMENT_MANAGE,
            self::PAYMENT_RECEIPT,
            self::PAYMENT_DELETE => 'المالية',

            self::EXAM_VIEW,
            self::EXAM_CREATE,
            self::EXAM_UPDATE,
            self::EXAM_DELETE,
            self::EXAM_PUBLISH,
            self::EXAM_MANAGE_QUESTIONS => 'الامتحانات',

            self::BOOKING_VIEW,
            self::BOOKING_CREATE,
            self::BOOKING_UPDATE,
            self::BOOKING_DELETE,
            self::BOOKING_CONFIRM => 'الحجوزات',

            self::ASSIGNMENT_VIEW,
            self::ASSIGNMENT_CREATE,
            self::ASSIGNMENT_UPDATE,
            self::ASSIGNMENT_DELETE,
            self::ASSIGNMENT_GRADE => 'المتابعة',

            self::CARD_REQUEST_VIEW,
            self::CARD_REQUEST_CREATE,
            self::CARD_REQUEST_DELETE => 'طلبات الكروت',

            self::SUPPORT_VIEW,
            self::SUPPORT_CREATE,
            self::SUPPORT_REPLY,
            self::SUPPORT_CLOSE => 'الدعم الفني',
        };
    }

    // =============================================
    // Icons
    // =============================================
    public function icon(): string
    {
        return match ($this) {
            self::MANAGE_SUBSCRIPTION => 'ph-crown',

            self::STAFF_VIEW,
            self::BRANCH_VIEW,
            self::SUBJECT_VIEW,
            self::GROUP_VIEW,
            self::STUDENT_VIEW,
            self::ATTENDANCE_VIEW,
            self::EXAM_VIEW => 'ph-eye',

            self::STAFF_CREATE,
            self::BRANCH_CREATE,
            self::SUBJECT_CREATE,
            self::GROUP_CREATE,
            self::STUDENT_CREATE,
            self::EXAM_CREATE => 'ph-plus-circle',

            self::STAFF_UPDATE,
            self::BRANCH_UPDATE,
            self::SUBJECT_UPDATE,
            self::GROUP_UPDATE,
            self::STUDENT_UPDATE,
            self::EXAM_UPDATE => 'ph-pencil-simple',

            self::STAFF_DELETE,
            self::BRANCH_DELETE,
            self::SUBJECT_DELETE,
            self::GROUP_DELETE,
            self::STUDENT_DELETE,
            self::EXAM_DELETE,
            self::PAYMENT_DELETE => 'ph-trash',

            self::ATTENDANCE_MANAGE => 'ph-scan',
            self::ATTENDANCE_CLOSE_DAY => 'ph-lock',
            self::TIMETABLE_VIEW => 'ph-calendar-week',
            self::VIEW_REPORTS => 'ph-chart-bar',
            self::MANAGE_SETTINGS => 'ph-gear',

            self::PAYMENT_VIEW => 'ph-eye',
            self::PAYMENT_MANAGE => 'ph-currency-circle-dollar',
            self::PAYMENT_RECEIPT => 'ph-receipt',

            self::EXAM_PUBLISH => 'ph-paper-plane-tilt',
            self::EXAM_MANAGE_QUESTIONS => 'ph-list-checks',

            self::BOOKING_VIEW => 'ph-eye',
            self::BOOKING_CREATE => 'ph-plus-circle',
            self::BOOKING_UPDATE => 'ph-pencil-simple',
            self::BOOKING_DELETE => 'ph-trash',
            self::BOOKING_CONFIRM => 'ph-check-circle',

            self::ASSIGNMENT_VIEW => 'ph-eye',
            self::ASSIGNMENT_CREATE => 'ph-plus-circle',
            self::ASSIGNMENT_UPDATE => 'ph-pencil-simple',
            self::ASSIGNMENT_DELETE => 'ph-trash',
            self::ASSIGNMENT_GRADE => 'ph-check-circle',

            self::CARD_REQUEST_VIEW => 'ph-eye',
            self::CARD_REQUEST_CREATE => 'ph-identification-card',
            self::CARD_REQUEST_DELETE => 'ph-trash',

            self::SUPPORT_VIEW => 'ph-eye',
            self::SUPPORT_CREATE => 'ph-plus-circle',
            self::SUPPORT_REPLY => 'ph-chat-circle',
            self::SUPPORT_CLOSE => 'ph-lock',
        };
    }

    // =============================================
    // Options
    // =============================================
    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn (self $case) => [$case->value => $case->label()])
            ->toArray();
    }

    public static function grouped(): array
    {
        $result = [];

        foreach (self::cases() as $case) {
            $result[$case->group()][] = $case;
        }

        return $result;
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    // =============================================
    // Default Permissions
    // =============================================
    public static function forOwner(): array
    {
        return self::values();
    }

    public static function forStaff(): array
    {
        return [
            self::BRANCH_VIEW->value,
            self::BRANCH_CREATE->value,
            self::BRANCH_UPDATE->value,

            self::SUBJECT_VIEW->value,
            self::SUBJECT_CREATE->value,
            self::SUBJECT_UPDATE->value,

            self::GROUP_VIEW->value,
            self::GROUP_CREATE->value,
            self::GROUP_UPDATE->value,

            self::STUDENT_VIEW->value,
            self::STUDENT_CREATE->value,
            self::STUDENT_UPDATE->value,

            self::ATTENDANCE_VIEW->value,
            self::ATTENDANCE_MANAGE->value,
            self::ATTENDANCE_CLOSE_DAY->value,

            self::TIMETABLE_VIEW->value,
            self::VIEW_REPORTS->value,

            self::PAYMENT_VIEW->value,
            self::PAYMENT_MANAGE->value,
            self::PAYMENT_RECEIPT->value,
            self::PAYMENT_DELETE->value,

            self::EXAM_VIEW->value,
            self::EXAM_CREATE->value,
            self::EXAM_UPDATE->value,
            self::EXAM_MANAGE_QUESTIONS->value,

            self::BOOKING_VIEW->value,
            self::BOOKING_CREATE->value,
            self::BOOKING_UPDATE->value,
            self::BOOKING_CONFIRM->value,

            self::ASSIGNMENT_VIEW->value,
            self::ASSIGNMENT_CREATE->value,
            self::ASSIGNMENT_UPDATE->value,
            self::ASSIGNMENT_GRADE->value,

            self::CARD_REQUEST_VIEW->value,

            self::SUPPORT_VIEW->value,
            self::SUPPORT_CREATE->value,
            self::SUPPORT_REPLY->value,
        ];
    }

    public static function forAssistant(): array
    {
        return [
            self::BRANCH_VIEW->value,
            self::SUBJECT_VIEW->value,
            self::GROUP_VIEW->value,
            self::STUDENT_VIEW->value,

            self::ATTENDANCE_VIEW->value,
            self::ATTENDANCE_CLOSE_DAY->value,

            self::TIMETABLE_VIEW->value,
            self::VIEW_REPORTS->value,
            self::PAYMENT_VIEW->value,

            self::EXAM_VIEW->value,

            self::BOOKING_VIEW->value,
            self::BOOKING_CREATE->value,
            self::ASSIGNMENT_VIEW->value,
        ];
    }

    public static function forRole(TeacherRoleEnum $role): array
    {
        return match ($role) {
            TeacherRoleEnum::OWNER => self::forOwner(),
            TeacherRoleEnum::STAFF => self::forStaff(),
            TeacherRoleEnum::ASSISTANT => self::forAssistant(),
        };
    }
}
