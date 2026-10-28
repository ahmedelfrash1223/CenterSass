<?php

namespace App\Http\Resources\Api\Student\Schedule;

use App\Enums\Teacher\GradeEnum;
use App\Enums\Teacher\SubjectEnum;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StudentScheduleResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'group_id' => $this->id,
            'group_name' => $this->name,
            'grade' => $this->grade,
            'grade_label' => GradeEnum::tryFrom($this->grade)?->label() ?? $this->grade,

            'branch' => $this->branch ? [
                'id' => $this->branch->id,
                'name' => $this->branch->name,
                'address' => $this->branch->address,
                'phone' => $this->branch->phone,
            ] : null,

            'teacher' => $this->teacher ? [
                'id' => $this->teacher->id,
                'name' => $this->teacher->name,
                'phone' => $this->teacher->phone,
                'email' => $this->teacher->email,
                'code' => $this->teacher->code,
            ] : null,

            'schedules' => $this->schedules->map(function ($schedule) {
                return $this->mapSchedule($schedule);
            })->values(),

            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }

    private function mapSchedule($schedule): array
    {
        $subjectValue = $schedule->subject?->subject;
        $subjectEnum = $subjectValue ? SubjectEnum::tryFrom($subjectValue) : null;

        return [
            'id' => $schedule->id,
            'day' => $schedule->day,
            'day_label' => $this->getDayLabel($schedule->day),
            'start_time' => $schedule->start_time?->format('H:i'),
            'end_time' => $schedule->end_time?->format('H:i'),
            'hours' => (float) $schedule->hours,
            'is_cancelled' => (bool) $schedule->is_cancelled,
            'subject' => $schedule->subject ? [
                'id' => $schedule->subject->id,
                'value' => $subjectValue,
                'name' => $subjectEnum?->label() ?? $subjectValue,
                'grade_label' => $schedule->subject->grade_label ?? null,
            ] : null,
            'teacher' => $schedule->teacher ? [
                'id' => $schedule->teacher->id,
                'name' => $schedule->teacher->name,
            ] : null,
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
