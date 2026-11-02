<?php

namespace App\Http\Resources\Api\Student\Schedule;

use App\Enums\Teacher\SubjectEnum;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StudentScheduleResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $start = Carbon::parse($this->start_time->format('H:i'));
        $end = Carbon::parse($this->end_time->format('H:i'));
        $now = now();

        $isCancelled = filter_var($this->is_cancelled, FILTER_VALIDATE_BOOLEAN);

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

        $attendance = $this->attendances->first();

        $attendanceStatus = $attendance?->status ?? 'not_recorded';
        $attendanceLabel = match($attendanceStatus) {
            'present' => 'حضر',
            'late' => 'متأخر',
            'absent' => 'غايب',
            'excused' => 'بعذر',
            default => 'لم يتم التسجيل'
        };

        $subjectValue = $this->subject?->subject;
        $subjectEnum = $subjectValue ? SubjectEnum::tryFrom($subjectValue) : null;

        return [
            'schedule_id' => $this->id,
            'group_name' => $this->group->name,
            'subject_name' => $subjectEnum?->label() ?? $subjectValue ?? '—',
            'teacher_name' => $this->teacher?->name,
            'start_time' => $start->format('H:i'),
            'end_time' => $end->format('H:i'),
            'duration_hours' => (float) $this->hours,
            'time_status' => $timeStatus,
            'time_status_label' => $timeStatusLabel,
            'show_scan_button' => $timeStatus === 'upcoming',
            'attendance_status' => $attendanceStatus,
            'attendance_status_label' => $attendanceLabel,
        ];
    }
}
