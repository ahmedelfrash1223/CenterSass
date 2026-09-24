<?php

namespace App\Enums\Teacher;

enum TeacherPermissionEnum: string
{
    // =============================================
    // 📌 Subscription
    // =============================================
    case MANAGE_SUBSCRIPTION = 'manage_subscription';

    // =============================================
    // 📌 Staff
    // =============================================
    case STAFF_VIEW = 'staff.view';
    case STAFF_CREATE = 'staff.create';
    case STAFF_UPDATE = 'staff.update';
    case STAFF_DELETE = 'staff.delete';

    // =============================================
    // 📌 Branches
    // =============================================
    case BRANCH_VIEW = 'branches.view';
    case BRANCH_CREATE = 'branches.create';
    case BRANCH_UPDATE = 'branches.update';
    case BRANCH_DELETE = 'branches.delete';

    // =============================================
    // 📌 Subjects
    // =============================================
    case SUBJECT_VIEW = 'subjects.view';
    case SUBJECT_CREATE = 'subjects.create';
    case SUBJECT_UPDATE = 'subjects.update';
    case SUBJECT_DELETE = 'subjects.delete';

    // =============================================
    // 📌 Groups
    // =============================================
    case GROUP_VIEW = 'groups.view';
    case GROUP_CREATE = 'groups.create';
    case GROUP_UPDATE = 'groups.update';
    case GROUP_DELETE = 'groups.delete';

    // =============================================
    // 📌 Students
    // =============================================
    case STUDENT_VIEW = 'students.view';
    case STUDENT_CREATE = 'students.create';
    case STUDENT_UPDATE = 'students.update';
    case STUDENT_DELETE = 'students.delete';

    // =============================================
    // 📌 Attendance
    // =============================================
    case ATTENDANCE_VIEW = 'attendance.view';
    case ATTENDANCE_MANAGE = 'attendance.manage';
    case ATTENDANCE_CLOSE_DAY = 'attendance.close_day';

    // =============================================
    // 📌 Timetable
    // =============================================
    case TIMETABLE_VIEW = 'timetable.view';

    // =============================================
    // 📌 Reports
    // =============================================
    case VIEW_REPORTS = 'reports.view';

    // =============================================
    // 📌 Settings
    // =============================================
    case MANAGE_SETTINGS = 'settings.manage';

    // =============================================
    // 📌 Payments & Invoices
    // =============================================
    case PAYMENT_VIEW     = 'payments.view';
    case PAYMENT_MANAGE   = 'payments.manage';
    case PAYMENT_RECEIPT  = 'payments.receipt';
    case PAYMENT_DELETE   = 'payments.delete';

    // =============================================
    // 📌 Helpers
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

            self::PAYMENT_VIEW    => 'عرض المدفوعات',
            self::PAYMENT_MANAGE  => 'تسجيل وتعديل المدفوعات',
            self::PAYMENT_RECEIPT => 'طباعة الإيصالات',
            self::PAYMENT_DELETE  => 'حذف وتعديل الفواتير',
        };
    }

    public function group(): string
    {
        return match ($this) {
            self::MANAGE_SUBSCRIPTION => 'الاشتراك',
            self::STAFF_VIEW, self::STAFF_CREATE, self::STAFF_UPDATE, self::STAFF_DELETE => 'الفريق',
            self::BRANCH_VIEW, self::BRANCH_CREATE, self::BRANCH_UPDATE, self::BRANCH_DELETE => 'الفروع',
            self::SUBJECT_VIEW, self::SUBJECT_CREATE, self::SUBJECT_UPDATE, self::SUBJECT_DELETE => 'المواد',
            self::GROUP_VIEW, self::GROUP_CREATE, self::GROUP_UPDATE, self::GROUP_DELETE => 'المجموعات',
            self::STUDENT_VIEW, self::STUDENT_CREATE, self::STUDENT_UPDATE, self::STUDENT_DELETE => 'الطلاب',
            self::ATTENDANCE_VIEW, self::ATTENDANCE_MANAGE, self::ATTENDANCE_CLOSE_DAY => 'الحضور',
            self::TIMETABLE_VIEW => 'جدول الأسبوع',
            self::VIEW_REPORTS => 'التقارير',
            self::MANAGE_SETTINGS => 'الإعدادات',

            self::PAYMENT_VIEW,
            self::PAYMENT_MANAGE,
            self::PAYMENT_RECEIPT,
            self::PAYMENT_DELETE => 'المالية',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::MANAGE_SUBSCRIPTION => 'ph-crown',
            self::STAFF_VIEW => 'ph-eye',
            self::STAFF_CREATE => 'ph-user-plus',
            self::STAFF_UPDATE => 'ph-pencil-simple',
            self::STAFF_DELETE => 'ph-trash',
            self::BRANCH_VIEW => 'ph-eye',
            self::BRANCH_CREATE => 'ph-plus-circle',
            self::BRANCH_UPDATE => 'ph-pencil-simple',
            self::BRANCH_DELETE => 'ph-trash',
            self::SUBJECT_VIEW => 'ph-eye',
            self::SUBJECT_CREATE => 'ph-plus-circle',
            self::SUBJECT_UPDATE => 'ph-pencil-simple',
            self::SUBJECT_DELETE => 'ph-trash',
            self::GROUP_VIEW => 'ph-eye',
            self::GROUP_CREATE => 'ph-plus-circle',
            self::GROUP_UPDATE => 'ph-pencil-simple',
            self::GROUP_DELETE => 'ph-trash',
            self::STUDENT_VIEW => 'ph-eye',
            self::STUDENT_CREATE => 'ph-plus-circle',
            self::STUDENT_UPDATE => 'ph-pencil-simple',
            self::STUDENT_DELETE => 'ph-trash',
            self::ATTENDANCE_VIEW => 'ph-eye',
            self::ATTENDANCE_MANAGE => 'ph-scan',
            self::ATTENDANCE_CLOSE_DAY => 'ph-lock',
            self::TIMETABLE_VIEW => 'ph-calendar-week',
            self::VIEW_REPORTS => 'ph-chart-bar',
            self::MANAGE_SETTINGS => 'ph-gear',

            self::PAYMENT_VIEW    => 'ph-eye',
            self::PAYMENT_MANAGE  => 'ph-currency-circle-dollar',
            self::PAYMENT_RECEIPT => 'ph-receipt',
            self::PAYMENT_DELETE  => 'ph-trash',
        };
    }

    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn(self $case) => [$case->value => $case->label()])
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
    // 📌 Default Permissions for each Role
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
