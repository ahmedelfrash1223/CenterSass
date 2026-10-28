<?php

namespace App\Http\Resources\Api\Student\Assignment;

use App\Enums\Teacher\AssignmentGradeEnum;
use App\Enums\Teacher\AssignmentStatusEnum;
use App\Enums\Teacher\GradeEnum;
use App\Enums\Teacher\SubjectEnum;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StudentAssignmentGradeResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $assignment = $this->assignment;
        $group = $assignment?->group;

        $subjectValue = null;
        $subjectName = null;

        if ($group) {
            $schedule = $group->schedules?->first();
            if ($schedule && $schedule->subject) {
                $subjectValue = $schedule->subject->subject;
                $subjectEnum = SubjectEnum::tryFrom($subjectValue);
                $subjectName = $subjectEnum?->label() ?? $subjectValue;
            }
        }

        $gradeEnum = $this->grade
            ? AssignmentGradeEnum::tryFrom($this->grade?->value ?? $this->grade)
            : null;

        return [
            'id' => $this->id,
            'assignment_id' => $this->assignment_id,

            'assignment' => $assignment ? [
                'id' => $assignment->id,
                'title' => $assignment->title,
                'description' => $assignment->description,
                'type' => $assignment->type?->value,
                'type_label' => $assignment->type?->label(),
                'max_score' => (float) $assignment->max_score,
                'due_date' => $assignment->due_date?->format('Y-m-d'),
                'status' => $assignment->status?->value,
                'status_label' => $assignment->status?->label(),
            ] : null,

            'group' => $group ? [
                'id' => $group->id,
                'name' => $group->name,
                'grade' => $group->grade,
                'grade_label' => GradeEnum::tryFrom($group->grade)?->label() ?? $group->grade,
                'subject' => $subjectValue ? [
                    'value' => $subjectValue,
                    'name' => $subjectName,
                ] : null,
            ] : null,

            'score' => $this->score !== null ? (float) $this->score : null,
            'percentage' => $this->percentage(),
            'grade' => $this->grade?->value,
            'grade_label' => $gradeEnum?->label(),
            'notes' => $this->notes,

            'graded_at' => $this->graded_at?->toIso8601String(),
            'graded_by' => $this->gradedBy ? [
                'id' => $this->gradedBy->id,
                'name' => $this->gradedBy->name,
            ] : null,

            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
