<?php

namespace App\Services\Teacher;

use App\Models\Attendance;
use App\Models\Group;
use App\Models\GroupSchedule;
use App\Models\Teacher;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AttendanceService
{
    /**
     * Close attendance for one group on a specific date.
     *
     * For each completed schedule of that day:
     *   - Mark all active students without an attendance record as "absent"
     *   - Leave existing records as-is
     *
     * Returns a summary array.
     */
    public function closeGroup(Group $group, Carbon $date, Teacher $user): array
    {
        $today = $date->format('l');  // Monday, Tuesday, ...
        $now = $date->isToday()
            ? Carbon::now()->format('H:i')
            : '23:59';  // For past days, treat all schedules as completed

        // Get today's completed schedules for this group
        $schedules = GroupSchedule::where('group_id', $group->id)
            ->where('day', $today)
            ->when($date->isToday(), fn($q) => $q->where('end_time', '<', $now))
            ->orderBy('start_time')
            ->get();

        if ($schedules->isEmpty()) {
            return [
                'success' => false,
                'message' => 'لا توجد حصص مكتملة لهذه المجموعة في هذا اليوم.',
                'total' => 0,
                'created' => 0,
                'closed_schedules' => 0,
            ];
        }

        // Active students in this group
        $studentIds = $group->students()
            ->wherePivot('status', 'active')
            ->pluck('students.id')
            ->toArray();

        if (empty($studentIds)) {
            return [
                'success' => false,
                'message' => 'لا يوجد طلاب نشطون في هذه المجموعة.',
                'total' => 0,
                'created' => 0,
                'closed_schedules' => $schedules->count(),
            ];
        }

        $totalCreated = 0;
        $closedSchedules = 0;

        DB::transaction(function () use (
            $schedules, $studentIds, $group, $user, $date, &$totalCreated, &$closedSchedules
        ) {
            foreach ($schedules as $schedule) {
                // Existing attendance records for this schedule + date
                $existingStudentIds = Attendance::where('schedule_id', $schedule->id)
                    ->where('attendance_date', $date->toDateString())
                    ->pluck('student_id')
                    ->toArray();

                // Students missing from this schedule
                $missingIds = array_diff($studentIds, $existingStudentIds);

                foreach ($missingIds as $studentId) {
                    Attendance::create([
                        'student_id' => $studentId,
                        'group_id' => $group->id,
                        'schedule_id' => $schedule->id,
                        'branch_id' => $group->branch_id,
                        'teacher_id' => $user->getOwnerId(),
                        'created_by' => $user->id,
                        'attendance_date' => $date->toDateString(),
                        'check_in_time' => null,
                        'status' => 'absent',
                        'is_auto_closed' => true,
                        'closed_at' => now(),
                    ]);

                    $totalCreated++;
                }

                $closedSchedules++;
            }
        });

        return [
            'success' => true,
            'message' => "تم تقفيل {$closedSchedules} حصة وإنشاء {$totalCreated} سجل غياب.",
            'total' => count($studentIds),
            'created' => $totalCreated,
            'closed_schedules' => $closedSchedules,
        ];
    }

    /**
     * Get attendance statistics for a group on a given date.
     * Returns counts + list of schedules with their stats.
     */
    public function getGroupStats(Group $group, Carbon $date): array
    {
        $today = $date->format('l');

        $totalStudents = $group->students()
            ->wherePivot('status', 'active')
            ->count();

        // Schedules of that day
        $schedules = GroupSchedule::where('group_id', $group->id)
            ->where('day', $today)
            ->orderBy('start_time')
            ->get();

        $scheduleStats = [];
        $overallPresent = [];
        $overallLate = [];
        $overallAbsent = [];

        foreach ($schedules as $schedule) {
            $attendances = Attendance::where('schedule_id', $schedule->id)
                ->where('attendance_date', $date->toDateString())
                ->get();

            $present = $attendances->where('status', 'present')->count();
            $late = $attendances->where('status', 'late')->count();
            $absent = $attendances->where('status', 'absent')->count();
            $excused = $attendances->where('status', 'excused')->count();
            $notRecorded = max(0, $totalStudents - $present - $late - $absent - $excused);

            $scheduleStats[] = [
                'schedule' => $schedule,
                'present' => $present,
                'late' => $late,
                'absent' => $absent,
                'excused' => $excused,
                'not_recorded' => $notRecorded,
                'is_closed' => $attendances->where('is_auto_closed', true)->isNotEmpty(),
            ];

            // Aggregate (unique students across schedules)
            foreach ($attendances->where('status', 'present') as $a) {
                $overallPresent[$a->student_id] = true;
            }
            foreach ($attendances->where('status', 'late') as $a) {
                $overallLate[$a->student_id] = true;
            }
            foreach ($attendances->where('status', 'absent') as $a) {
                $overallAbsent[$a->student_id] = true;
            }
        }

        return [
            'total_students' => $totalStudents,
            'present' => count($overallPresent),
            'late' => count($overallLate),
            'absent' => count($overallAbsent),
            'schedules' => $scheduleStats,
            'schedule_count' => $schedules->count(),
        ];
    }
}
