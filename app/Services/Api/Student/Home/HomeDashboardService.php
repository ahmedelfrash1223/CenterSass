<?php

namespace App\Services\Api\Student\Home;

use App\Models\GroupSchedule;
use App\Models\StudentAccount;
use App\Services\Api\Student\Schedule\StudentScheduleService;
use App\Services\Api\Student\Attendance\StudentAttendanceService;
use App\Services\Api\Student\Invoice\StudentInvoiceService;
use App\Services\Api\Student\Assignment\StudentAssignmentService;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class HomeDashboardService
{
    public function __construct(
        private readonly StudentScheduleService $scheduleService,
        private readonly StudentAttendanceService $attendanceService,
        private readonly StudentInvoiceService $invoiceService,
        private readonly StudentAssignmentService $assignmentService,
    ) {}


    public function getDashboardData(StudentAccount $account): array
    {
        $currentMonth = now()->format('Y-m');

        $nextClass = $this->getNextUpcomingClass($account);

        $attendanceStats = $this->attendanceService->statsForAccount($account, $currentMonth);

        $invoiceStats = $this->invoiceService->statsForAccount($account, $currentMonth);

        $assignmentStats = $this->assignmentService->statsForAccount($account, $currentMonth);

        $teachersCount = $account->students()->distinct('teacher_id')->count('teacher_id');

        return [
            'student' => [
                'name' => $account->name,
                'email' => $account->email,
                'avatar' => $account->avatar,
                'subscribed_teachers_count' => $teachersCount,
            ],
            'next_class' => $nextClass,
            'quick_stats' => [
                'attendance' => [
                    'percentage' => $attendanceStats['attendance_rate'],
                    'status' => $attendanceStats['attendance_rate'] >= 75 ? 'good' : 'warning',
                ],
                'invoices' => [
                    'total_remaining' => $invoiceStats['total_remaining'],
                    'currency' => 'ج.م',
                    'has_dues' => $invoiceStats['total_remaining'] > 0,
                ],
                'assignments' => [
                    'average_percentage' => $assignmentStats['average_percentage'],
                    'graded_count' => $assignmentStats['graded_count'],
                ],
            ],
        ];
    }


    private function getNextUpcomingClass(StudentAccount $account): ?array
    {
        $students = $account->students;
        $studentIds = $students->pluck('id');

        $daysOrder = [
            'Saturday' => 1,
            'Sunday' => 2,
            'Monday' => 3,
            'Tuesday' => 4,
            'Wednesday' => 5,
            'Thursday' => 6,
            'Friday' => 7,
        ];

        $today = now()->format('l');
        $currentTime = now()->format('H:i');
        $todayOrder = $daysOrder[$today] ?? 7;

        $allSchedules = GroupSchedule::query()
            ->whereHas('group.students', function ($q) use ($studentIds) {
                $q->whereIn('students.id', $studentIds);
            })
            ->with(['group', 'subject', 'teacher'])
            ->where('is_cancelled', false)
            ->get();

        if ($allSchedules->isEmpty()) {
            return null;
        }
        $sortedSchedules = $allSchedules->sortBy(function ($schedule) use ($daysOrder) {
            return sprintf('%d-%s', $daysOrder[$schedule->day] ?? 7, $schedule->start_time->format('H:i'));
        });

        $todayUpcoming = $sortedSchedules->first(function ($schedule) use ($today, $currentTime) {
            return $schedule->day === $today && $schedule->start_time->format('H:i') >= $currentTime;
        });

        if ($todayUpcoming) {
            return $this->formatNextClass($todayUpcoming, 'upcoming', 'لم تبدأ');
        }

        $nextDaySchedules = $sortedSchedules->first(function ($schedule) use ($todayOrder, $daysOrder) {
            return ($daysOrder[$schedule->day] ?? 7) > $todayOrder;
        });

        if ($nextDaySchedules) {
            return $this->formatNextClass($nextDaySchedules, 'upcoming', 'لم تبدأ');
        }

        $firstSchedule = $sortedSchedules->first();
        return $firstSchedule ? $this->formatNextClass($firstSchedule, 'upcoming', 'لم تبدأ') : null;
    }


    private function formatNextClass($schedule, string $status, string $statusLabel): array
    {
        $subjectValue = $schedule->subject?->subject;
        $subjectEnum = $subjectValue ? \App\Enums\Teacher\SubjectEnum::tryFrom($subjectValue) : null;

        return [
            'schedule_id' => $schedule->id,
            'group_name' => $schedule->group->name ?? '—',
            'subject_name' => $subjectEnum?->label() ?? $subjectValue ?? '—',
            'teacher_name' => $schedule->teacher?->name ?? '—',
            'day' => $schedule->day,
            'day_label' => $this->getDayLabel($schedule->day),
            'start_time' => $schedule->start_time->format('H:i'),
            'end_time' => $schedule->end_time->format('H:i'),
            'time_status' => $status,
            'time_status_label' => $statusLabel,
            'show_scan_button' => true,
        ];
    }


    private function getDayLabel(string $day): string
    {
        return match ($day) {
            'Saturday' => 'السبت',
            'Sunday' => 'الأحد',
            'Monday' => 'الإثنين',
            'Tuesday' => 'الثلاثاء',
            'Wednesday' => 'الأربعاء',
            'Thursday' => 'الخميس',
            'Friday' => 'الجمعة',
            default => $day,
        };
    }
}
