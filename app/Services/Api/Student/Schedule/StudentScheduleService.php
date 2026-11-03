<?php

namespace App\Services\Api\Student\Schedule;

use App\Models\Attendance;
use App\Models\GroupSchedule;
use App\Models\StudentAccount;
use Illuminate\Support\Collection;

class StudentScheduleService
{
    public function getDashboardForAccount(StudentAccount $account, ?string $day = null): array
    {
        $selectedDay = $day ?: now()->format('l');

        $students = $account->relationLoaded('students')
            ? $account->students
            : $account->students()->with('teacher')->get();        $studentIds = $students->pluck('id');

        $teachersData = $students->groupBy('teacher_id')->map(function ($teacherStudents) {
            $teacher = $teacherStudents->first()->teacher;

            $schedulesCount = GroupSchedule::whereIn('group_id', function ($q) use ($teacherStudents) {
                $q->select('group_id')->from('group_student')->whereIn('student_id', $teacherStudents->pluck('id'));
            })->where('is_cancelled', false)->count();

            return [
                'id' => $teacher->id,
                'name' => $teacher->name,
                'phone' => $teacher->phone,
                'weekly_schedules_count' => $schedulesCount,
            ];
        })->values();

        $schedules = GroupSchedule::query()
            ->where('day', $selectedDay)
            ->whereHas('group.students', function ($q) use ($studentIds) {
                $q->whereIn('students.id', $studentIds);
            })
            ->with(['group', 'subject', 'teacher'])
            ->with(['attendances' => function ($query) use ($studentIds) {
                $query->whereIn('student_id', $studentIds)
                    ->where('attendance_date', now()->toDateString());
            }])
            ->orderBy('start_time')
            ->get();

        return [
            'selected_day' => $selectedDay,
            'teachers' => $teachersData,
            'schedules' => $schedules,
        ];
    }
}
