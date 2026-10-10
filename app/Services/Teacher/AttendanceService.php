<?php

namespace App\Services\Teacher;

use App\Enums\Teacher\ScheduleCancellationReasonEnum;
use App\Models\Attendance;
use App\Models\Group;
use App\Models\GroupSchedule;
use App\Models\Teacher;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class AttendanceService
{
    public function cancelSchedule(
        Group $group,
        int $scheduleId,
        Carbon $date,
        Teacher $user,
        ScheduleCancellationReasonEnum $reason,
        ?string $notes = null
    ): array {
        $schedule = GroupSchedule::query()
            ->where('group_id', $group->id)
            ->whereKey($scheduleId)
            ->firstOrFail();

        if ($schedule->day !== $date->format('l')) {
            throw ValidationException::withMessages([
                'schedule' => 'الحصة لا تتبع اليوم المحدد.',
            ]);
        }

        if ($this->isGroupClosed($group, $date)) {
            throw ValidationException::withMessages([
                'schedule' => 'لا يمكن إلغاء حصة في مجموعة مقفلة. افتح اليوم أولاً.',
            ]);
        }

        if ($schedule->is_cancelled) {
            throw ValidationException::withMessages([
                'schedule' => 'الحصة ملغاة بالفعل.',
            ]);
        }

        $hasAttendances = Attendance::query()
            ->where('schedule_id', $schedule->id)
            ->whereDate('attendance_date', $date->toDateString())
            ->exists();

        if ($hasAttendances) {
            throw ValidationException::withMessages([
                'schedule' => 'لا يمكن إلغاء الحصة لوجود سجلات حضور أو غياب مرتبطة بها. راجع السجلات أولاً.',
            ]);
        }

        $schedule->update([
            'is_cancelled' => true,
            'cancellation_reason' => $reason->value,
            'cancellation_notes' => $notes,
            'cancelled_at' => now(),
            'cancelled_by' => $user->id,
        ]);

        return [
            'success' => true,
            'message' => 'تم إلغاء الحصة بنجاح.',
        ];
    }

    public function restoreSchedule(Group $group, int $scheduleId, Carbon $date): array
    {
        $schedule = GroupSchedule::query()
            ->where('group_id', $group->id)
            ->whereKey($scheduleId)
            ->firstOrFail();

        if ($schedule->day !== $date->format('l')) {
            throw ValidationException::withMessages([
                'schedule' => 'الحصة لا تتبع اليوم المحدد.',
            ]);
        }

        if ($this->isGroupClosed($group, $date)) {
            throw ValidationException::withMessages([
                'schedule' => 'لا يمكن إعادة الحصة أثناء تقفيل المجموعة. افتح اليوم أولاً.',
            ]);
        }

        if (! $schedule->is_cancelled) {
            throw ValidationException::withMessages([
                'schedule' => 'الحصة ليست ملغاة.',
            ]);
        }

        $schedule->update([
            'is_cancelled' => false,
            'cancellation_reason' => null,
            'cancellation_notes' => null,
            'cancelled_at' => null,
            'cancelled_by' => null,
        ]);

        return [
            'success' => true,
            'message' => 'تمت إعادة الحصة إلى الجدول بنجاح.',
        ];
    }

    public function closeGroup(Group $group, Carbon $date, Teacher $user): array
    {
        if ($this->isGroupClosed($group, $date)) {
            return [
                'success' => false,
                'message' => 'المجموعة مقفلة بالفعل في هذا اليوم.',
                'total' => 0,
                'created' => 0,
                'closed_schedules' => 0,
            ];
        }

        $today = $date->format('l');

        // نتحقق أولاً من وجود حصة واحدة على الأقل غير ملغية في اليوم.
        $activeSchedules = GroupSchedule::query()
            ->where('group_id', $group->id)
            ->where('day', $today)
            ->where('is_cancelled', false)
            ->orderBy('start_time')
            ->get();

        if ($activeSchedules->isEmpty()) {
            return [
                'success' => false,
                'message' => 'لا يمكن تقفيل المجموعة لأن جميع حصص هذا اليوم ملغية أو لا توجد حصص غير ملغية.',
                'total' => 0,
                'created' => 0,
                'closed_schedules' => 0,
            ];
        }

        $now = $date->isToday() ? Carbon::now()->format('H:i') : '23:59';

        $schedules = $activeSchedules
            ->filter(fn ($schedule) => ! $date->isToday()
                || ($schedule->end_time && $schedule->end_time->format('H:i') < $now))
            ->values();

        if ($schedules->isEmpty()) {
            return [
                'success' => false,
                'message' => 'لا توجد حصص غير ملغية انتهى موعدها ويمكن تقفيلها حتى الآن.',
                'total' => 0,
                'created' => 0,
                'closed_schedules' => 0,
            ];
        }

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
            $schedules,
            $studentIds,
            $group,
            $user,
            $date,
            &$totalCreated,
            &$closedSchedules
        ) {
            foreach ($schedules as $schedule) {
                // حماية إضافية حتى لا تُنشأ سجلات لحصة تم إلغاؤها.
                if ($schedule->is_cancelled) {
                    continue;
                }

                $existingStudentIds = Attendance::query()
                    ->where('schedule_id', $schedule->id)
                    ->whereDate('attendance_date', $date->toDateString())
                    ->pluck('student_id')
                    ->toArray();

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

            if ($closedSchedules > 0) {
                $group->update([
                    'last_closed_date' => $date->toDateString(),
                ]);
            }
        });

        if ($closedSchedules === 0) {
            return [
                'success' => false,
                'message' => 'لم يتم تقفيل أي حصة غير ملغية.',
                'total' => 0,
                'created' => 0,
                'closed_schedules' => 0,
            ];
        }

        return [
            'success' => true,
            'message' => "تم تقفيل {$closedSchedules} حصة وإنشاء {$totalCreated} سجل غياب.",
            'total' => count($studentIds),
            'created' => $totalCreated,
            'closed_schedules' => $closedSchedules,
        ];
    }

    public function getGroupStats(Group $group, Carbon $date): array
    {
        $today = $date->format('l');

        $totalStudents = $group->students()
            ->wherePivot('status', 'active')
            ->count();

        $schedules = GroupSchedule::query()
            ->where('group_id', $group->id)
            ->where('day', $today)
            ->orderBy('start_time')
            ->get();

        $scheduleStats = [];
        $overallPresent = [];
        $overallLate = [];
        $overallAbsent = [];

        foreach ($schedules as $schedule) {
            $attendances = Attendance::query()
                ->where('schedule_id', $schedule->id)
                ->whereDate('attendance_date', $date->toDateString())
                ->get();

            $present = $attendances->where('status', 'present')->count();
            $late = $attendances->where('status', 'late')->count();
            $absent = $attendances->where('status', 'absent')->count();
            $excused = $attendances->where('status', 'excused')->count();

            $notRecorded = $schedule->is_cancelled
                ? 0
                : max(0, $totalStudents - $present - $late - $absent - $excused);

            $scheduleStats[] = [
                'schedule' => $schedule,
                'present' => $present,
                'late' => $late,
                'absent' => $absent,
                'excused' => $excused,
                'not_recorded' => $notRecorded,
                'is_closed' => ! $schedule->is_cancelled
                    && $attendances->where('is_auto_closed', true)->isNotEmpty(),
                'is_cancelled' => $schedule->is_cancelled,
            ];

            if ($schedule->is_cancelled) {
                continue;
            }

            foreach ($attendances->where('status', 'present') as $attendance) {
                $overallPresent[$attendance->student_id] = true;
            }

            foreach ($attendances->where('status', 'late') as $attendance) {
                $overallLate[$attendance->student_id] = true;
            }

            foreach ($attendances->where('status', 'absent') as $attendance) {
                $overallAbsent[$attendance->student_id] = true;
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
            ->when(
                ! $user->isOwner(),
                fn ($query) => $query->whereIn('branch_id', $accessibleBranchIds)
            )
            ->when(
                $branchId,
                fn ($query) => $query->where('branch_id', $branchId)
            )
            ->whereHas('schedules', fn ($query) => $query->where('day', $dayName))
            ->with([
                'schedules' => fn ($query) => $query
                    ->where('day', $dayName)
                    ->orderBy('start_time'),
            ])
            ->orderBy('name')
            ->get();
    }

    public function groupSchedulesFinishedToday(Group $group, Carbon $date): bool
    {
        if (! $group->relationLoaded('schedules')) {
            $group->load([
                'schedules' => fn ($query) => $query->where('day', $date->format('l')),
            ]);
        }

        $activeSchedules = $group->schedules
            ->where('is_cancelled', false);

        if ($activeSchedules->isEmpty()) {
            return true;
        }

        if ($date->isPast() && ! $date->isToday()) {
            return true;
        }

        if ($date->isFuture()) {
            return false;
        }

        $lastEndTime = $activeSchedules
            ->map(fn ($schedule) => $schedule->end_time)
            ->filter()
            ->sortByDesc(fn ($time) => $time->format('H:i'))
            ->first();

        if (! $lastEndTime) {
            return true;
        }

        $lastEnd = Carbon::parse(
            $date->toDateString().' '.$lastEndTime->format('H:i')
        );

        return Carbon::now()->greaterThan($lastEnd);
    }

    public function isGroupClosed(Group $group, Carbon $date): bool
    {
        return $group->last_closed_date !== null
            && $group->last_closed_date->isSameDay($date);
    }
}
