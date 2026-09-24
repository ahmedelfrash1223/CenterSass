<?php

namespace App\Services\Teacher;

use App\Models\Attendance;
use App\Models\Group;
use App\Models\GroupSchedule;
use App\Models\Teacher;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;

class AttendanceService
{

    public function closeGroup(Group $group, Carbon $date, Teacher $user): array
    {
        if ($this->isGroupClosed($group, $date)) {
            return [
                'success'          => false,
                'message'          => 'المجموعة مقفلة بالفعل في هذا اليوم.',
                'total'            => 0,
                'created'          => 0,
                'closed_schedules' => 0,
            ];
        }

        $today = $date->format('l');
        $now = $date->isToday()
            ? Carbon::now()->format('H:i')
            : '23:59';

        $schedules = GroupSchedule::where('group_id', $group->id)
            ->where('day', $today)
            ->when($date->isToday(), fn($q) => $q->where('end_time', '<', $now))
            ->orderBy('start_time')
            ->get();

        if ($schedules->isEmpty()) {
            return [
                'success'          => false,
                'message'          => 'لا توجد حصص مكتملة لهذه المجموعة في هذا اليوم.',
                'total'            => 0,
                'created'          => 0,
                'closed_schedules' => 0,
            ];
        }

        $studentIds = $group->students()
            ->wherePivot('status', 'active')
            ->pluck('students.id')
            ->toArray();

        if (empty($studentIds)) {
            return [
                'success'          => false,
                'message'          => 'لا يوجد طلاب نشطون في هذه المجموعة.',
                'total'            => 0,
                'created'          => 0,
                'closed_schedules' => $schedules->count(),
            ];
        }

        $totalCreated = 0;
        $closedSchedules = 0;

        DB::transaction(function () use (
            $schedules, $studentIds, $group, $user, $date, &$totalCreated, &$closedSchedules
        ) {
            foreach ($schedules as $schedule) {
                $existingStudentIds = Attendance::where('schedule_id', $schedule->id)
                    ->where('attendance_date', $date->toDateString())
                    ->pluck('student_id')
                    ->toArray();

                $missingIds = array_diff($studentIds, $existingStudentIds);

                foreach ($missingIds as $studentId) {
                    Attendance::create([
                        'student_id'      => $studentId,
                        'group_id'        => $group->id,
                        'schedule_id'     => $schedule->id,
                        'branch_id'       => $group->branch_id,
                        'teacher_id'      => $user->getOwnerId(),
                        'created_by'      => $user->id,
                        'attendance_date' => $date->toDateString(),
                        'check_in_time'   => null,
                        'status'          => 'absent',
                        'is_auto_closed'  => true,
                        'closed_at'       => now(),
                    ]);

                    $totalCreated++;
                }

                $closedSchedules++;
            }
        });

        return [
            'success'          => true,
            'message'          => "تم تقفيل {$closedSchedules} حصة وإنشاء {$totalCreated} سجل غياب.",
            'total'            => count($studentIds),
            'created'          => $totalCreated,
            'closed_schedules' => $closedSchedules,
        ];
    }


    public function getGroupStats(Group $group, Carbon $date): array
    {
        $today = $date->format('l');

        $totalStudents = $group->students()
            ->wherePivot('status', 'active')
            ->count();

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
                'schedule'     => $schedule,
                'present'      => $present,
                'late'         => $late,
                'absent'       => $absent,
                'excused'      => $excused,
                'not_recorded' => $notRecorded,
                'is_closed'    => $attendances->where('is_auto_closed', true)->isNotEmpty(),
            ];

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
            'present'        => count($overallPresent),
            'late'           => count($overallLate),
            'absent'         => count($overallAbsent),
            'schedules'      => $scheduleStats,
            'schedule_count' => $schedules->count(),
        ];
    }


    public function getGroupsWithSchedulesOnDate(
        Teacher $user,
        Carbon $date,
        ?int $branchId = null
    ): Collection {
        $ownerId = $user->getOwnerId();
        $accessibleBranchIds = $user->getAccessibleBranchIds();
        $dayName = $date->format('l');

        return Group::query()
            ->with('branch')
            ->where('teacher_id', $ownerId)
            ->when(!$user->isOwner(), fn($q) => $q->whereIn('branch_id', $accessibleBranchIds))
            ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
            ->whereHas('schedules', fn($q) => $q->where('day', $dayName))
            ->with([
                'schedules' => fn($q) => $q
                    ->where('day', $dayName)
                    ->orderBy('start_time'),
            ])
            ->orderBy('name')
            ->get();
    }


    public function groupSchedulesFinishedToday(Group $group, Carbon $date): bool
    {
        if (!$group->relationLoaded('schedules')) {
            $group->load([
                'schedules' => fn($q) => $q->where('day', $date->format('l')),
            ]);
        }

        if ($group->schedules->isEmpty()) {
            return true;
        }

        if ($date->isPast() && !$date->isToday()) {
            return true;
        }

        if ($date->isFuture()) {
            return false;
        }

        $lastEndTime = $group->schedules
            ->map(fn($s) => $s->end_time)
            ->filter()
            ->sortByDesc(fn($t) => $t->format('H:i'))
            ->first();

        if (!$lastEndTime) {
            return true;
        }

        $lastEnd = Carbon::parse(
            $date->toDateString() . ' ' . $lastEndTime->format('H:i')
        );

        return Carbon::now()->greaterThan($lastEnd);
    }


    public function isGroupClosed(Group $group, Carbon $date): bool
    {
        $today = $date->format('l');

        $scheduleIds = GroupSchedule::where('group_id', $group->id)
            ->where('day', $today)
            ->pluck('id');

        if ($scheduleIds->isEmpty()) {
            return false;
        }

        $closedScheduleIds = Attendance::whereIn('schedule_id', $scheduleIds)
            ->where('attendance_date', $date->toDateString())
            ->where('is_auto_closed', 1)
            ->pluck('schedule_id')
            ->unique();

        return $closedScheduleIds->count() >= $scheduleIds->count();
    }
}
