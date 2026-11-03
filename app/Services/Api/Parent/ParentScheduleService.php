<?php

namespace App\Services\Api\Parent;

use App\Enums\Teacher\SubjectEnum;
use App\Models\Attendance;
use App\Models\GroupSchedule;
use App\Models\Student;
use Carbon\Carbon;

class ParentScheduleService
{
    public function getChildSchedules(Student $child, ?string $day = null): array
    {
        $selectedDay = $day ?: now()->format('l');
        $currentTime = now()->format('H:i');
        $todayDate = now()->toDateString();

        $schedules = GroupSchedule::query()
            ->where('day', $selectedDay)
            ->whereHas('group.students', function ($q) use ($child) {
                $q->where('students.id', $child->id);
            })
            ->with(['group', 'subject', 'teacher'])
            ->orderBy('start_time')
            ->get();

        $attendanceRecords = Attendance::where('student_id', $child->id)
            ->where('attendance_date', $todayDate)
            ->get()
            ->keyBy('schedule_id');

        $formattedSchedules = $schedules->map(function ($schedule) use ($currentTime, $attendanceRecords) {
            $start = Carbon::parse($schedule->start_time->format('H:i'));
            $end = Carbon::parse($schedule->end_time->format('H:i'));
            $now = Carbon::parse($currentTime);

            $isCancelled = filter_var($schedule->is_cancelled, FILTER_VALIDATE_BOOLEAN);

            if ($isCancelled) {
                $timeStatus = 'cancelled';
                $timeStatusLabel = 'ملغية';
            } elseif ($now->lt($start)) {
                $timeStatus = 'upcoming';
                $timeStatusLabel = 'لم تبدأ';
            } elseif ($now->between($start, $end)) {
                $timeStatus = 'running';
                $timeStatusLabel = 'جارية الآن';
            } else {
                $timeStatus = 'finished';
                $timeStatusLabel = 'انتهت';
            }

            $attendance = ($timeStatus === 'running' || $timeStatus === 'finished')
                ? $attendanceRecords->get($schedule->id)
                : null;

            $attendanceStatus = $attendance?->status ?? 'not_recorded';
            $attendanceLabel = match($attendanceStatus) {
                'present' => 'حضر',
                'late' => 'متأخر',
                'absent' => 'غايب',
                'excused' => 'بعذر',
                default => 'لم يتم التسجيل'
            };

            $subjectValue = $schedule->subject?->subject;
            $subjectEnum = $subjectValue ? SubjectEnum::tryFrom($subjectValue) : null;

            return [
                'schedule_id' => $schedule->id,
                'group_name' => $schedule->group->name,
                'subject_name' => $subjectEnum?->label() ?? $subjectValue ?? '—',
                'teacher_name' => $schedule->teacher?->name,
                'start_time' => $start->format('H:i'),
                'end_time' => $end->format('H:i'),
                'duration_hours' => (float) $schedule->hours,
                'time_status' => $timeStatus,
                'time_status_label' => $timeStatusLabel,
                'attendance_status' => $attendanceStatus,
                'attendance_status_label' => $attendanceLabel,
            ];
        });

        return [
            'selected_day' => $selectedDay,
            'schedules' => $formattedSchedules,
        ];
    }
}
