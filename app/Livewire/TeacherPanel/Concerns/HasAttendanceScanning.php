<?php

namespace App\Livewire\TeacherPanel\Concerns;

use App\Models\Attendance;
use App\Models\GroupSchedule;
use App\Models\Student;
use Carbon\Carbon;

trait HasAttendanceScanning
{
    private static int $preTolerance = 30;
    private static int $postTolerance = 30;

    /**
     * Multi-layer student lookup by code / uuid / barcode_digits.
     */
    protected function findStudent(string $value): ?Student
    {
        $upper = strtoupper(trim($value));
        $lower = strtolower($upper);

        // Layer 1: exact match
        $student = Student::where(function ($q) use ($upper, $lower) {
            $q->where('code', $upper)
                ->orWhere('code', $lower)
                ->orWhere('uuid', $lower)
                ->orWhere('barcode_digits', $upper)
                ->orWhere('barcode_digits', $lower);
        })->first();

        if ($student) {
            return $student;
        }

        // Layer 2: digits only
        $digits = preg_replace('/\D/', '', $value);

        if (strlen($digits) < 4) {
            return null;
        }

        $student = Student::where('barcode_digits', $digits)->first();

        if ($student) {
            return $student;
        }

        // Layer 3: suffix match
        foreach ([10, 9, 8, 7, 6, 5, 4] as $len) {
            if (strlen($digits) < $len) {
                continue;
            }

            $suffix = substr($digits, -$len);

            $matches = Student::where('barcode_digits', 'like', '%' . $suffix)
                ->limit(2)
                ->get();

            if ($matches->count() === 1) {
                return $matches->first();
            }
        }

        return null;
    }

    /**
     * Match schedules that are running OR about to start.
     */
    protected function matchSchedules($schedules, string $currentTime)
    {
        $now = Carbon::createFromFormat('H:i', $currentTime);

        return $schedules->filter(function ($s) use ($now) {
            $start = Carbon::parse($s->start_time->format('H:i'));
            $end = Carbon::parse($s->end_time->format('H:i'));

            $isRunning = $now->between($start, $end);
            $isAboutToStart = $now->lt($start)
                && $start->diffInMinutes($now) <= self::$preTolerance;

            return $isRunning || $isAboutToStart;
        })->values();
    }

    /**
     * Determine present vs late.
     */
    protected function calculateStatus(GroupSchedule $schedule, string $currentTime): string
    {
        $start = Carbon::parse($schedule->start_time->format('H:i'));
        $now = Carbon::createFromFormat('H:i', $currentTime);

        return $now->diffInMinutes($start, false) < -10 ? 'late' : 'present';
    }

    /**
     * Register attendance on all matching schedules.
     */
    protected function registerAttendance(Student $student, $user): array
    {
        $now = Carbon::now();
        $today = $now->format('l');
        $currentTime = $now->format('H:i');

        $schedules = GroupSchedule::query()
            ->with(['group', 'subject'])
            ->where('day', $today)
            ->whereHas('group.students', function ($q) use ($student) {
                $q->where('students.id', $student->id)
                    ->where('group_student.status', 'active');
            })
            ->orderBy('start_time')
            ->get();

        if ($schedules->isEmpty()) {
            return [
                'success' => false,
                'message' => 'لا توجد حصص مسجلة لهذا الطالب اليوم.',
                'student_name' => $student->name,
            ];
        }

        $matched = $this->matchSchedules($schedules, $currentTime);

        if ($matched->isEmpty()) {
            return [
                'success' => false,
                'message' => 'لا توجد حصة جارية الآن.',
                'student_name' => $student->name,
                'today_schedules' => $schedules->map(fn($s) => [
                    'start' => $s->start_time->format('H:i'),
                    'end' => $s->end_time->format('H:i'),
                    'subject' => $s->subject?->subject_label,
                    'group' => $s->group?->name,
                ])->toArray(),
            ];
        }

        $registered = [];
        $skipped = [];

        foreach ($matched as $schedule) {
            $exists = Attendance::where('student_id', $student->id)
                ->where('schedule_id', $schedule->id)
                ->where('attendance_date', $now->toDateString())
                ->exists();

            if ($exists) {
                $skipped[] = [
                    'subject' => $schedule->subject?->subject_label,
                    'group' => $schedule->group?->name,
                    'start' => $schedule->start_time->format('H:i'),
                ];
                continue;
            }

            $status = $this->calculateStatus($schedule, $currentTime);

            Attendance::create([
                'student_id' => $student->id,
                'group_id' => $schedule->group_id,
                'schedule_id' => $schedule->id,
                'branch_id' => $student->branch_id,
                'teacher_id' => $user->getOwnerId(),
                'created_by' => $user->id,
                'attendance_date' => $now->toDateString(),
                'check_in_time' => $currentTime,
                'status' => $status,
            ]);

            $registered[] = [
                'subject' => $schedule->subject?->subject_label,
                'group' => $schedule->group?->name,
                'start' => $schedule->start_time->format('H:i'),
                'end' => $schedule->end_time->format('H:i'),
                'status' => $status,
            ];
        }

        if (!empty($registered)) {
            return [
                'success' => true,
                'message' => 'تم تسجيل الحضور بنجاح.',
                'student_name' => $student->name,
                'student_code' => $student->code,
                'branch' => $student->branch?->name,
                'time' => $currentTime,
                'registered' => $registered,
                'skipped' => $skipped,
            ];
        }

        return [
            'success' => false,
            'message' => 'الطالب مسجل بالفعل في كل حصص هذه الفترة.',
            'student_name' => $student->name,
            'skipped' => $skipped,
        ];
    }
}
