<?php

namespace App\Http\Resources\Api\Student\Attendance;

use App\Enums\Teacher\GradeEnum;
use App\Enums\Teacher\SubjectEnum;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StudentAttendanceResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $schedule = $this->schedule;
        $subjectValue = $schedule?->subject?->subject;
        $subjectEnum = $subjectValue ? SubjectEnum::tryFrom($subjectValue) : null;

        return [
            'id' => $this->id,
            'attendance_date' => $this->attendance_date?->format('Y-m-d'),
            'check_in_time' => $this->check_in_time?->format('H:i'),

            'status' => $this->status,
            'status_label' => $this->status_label,
            'status_color' => $this->status_color,

            'is_auto_closed' => (bool) $this->is_auto_closed,
            'notes' => $this->notes,

            'group' => $this->group ? [
                'id' => $this->group->id,
                'name' => $this->group->name,
                'grade' => $this->group->grade,
                'grade_label' => GradeEnum::tryFrom($this->group->grade)?->label() ?? $this->group->grade,
            ] : null,

            'subject' => $schedule?->subject ? [
                'id' => $schedule->subject->id,
                'value' => $subjectValue,
                'name' => $subjectEnum?->label() ?? $subjectValue,
            ] : null,

            'schedule' => $schedule ? [
                'id' => $schedule->id,
                'day' => $schedule->day,
                'day_label' => $this->getDayLabel($schedule->day),
                'start_time' => $schedule->start_time?->format('H:i'),
                'end_time' => $schedule->end_time?->format('H:i'),
                'is_cancelled' => (bool) $schedule->is_cancelled,
            ] : null,

            'created_at' => $this->created_at?->toIso8601String(),
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
