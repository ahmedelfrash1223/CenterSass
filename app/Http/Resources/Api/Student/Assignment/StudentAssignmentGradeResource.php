<?php

namespace App\Http\Resources\Api\Student\Assignment;

use App\Enums\Teacher\AssignmentGradeEnum;
use App\Enums\Teacher\GradeEnum;
use App\Enums\Teacher\SubjectEnum;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StudentAssignmentGradeResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $group = $this->group;

        $studentGrade = $this->grades->first();

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

        $percentage = null;
        $gradeEnum = null;

        if ($studentGrade && $studentGrade->score !== null && $this->max_score > 0) {
            $percentage = round(($studentGrade->score / $this->max_score) * 100, 2);
            $gradeEnum = AssignmentGradeEnum::fromPercentage($percentage);
        }

        return [
            'assignment_id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'type' => $this->type?->value,
            'type_label' => $this->type?->label(),
            'max_score' => (float) $this->max_score,
            'due_date' => $this->due_date?->format('Y-m-d'),

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

            'is_graded' => $studentGrade !== null && $studentGrade->score !== null,
            'score' => $studentGrade?->score !== null ? (float) $studentGrade->score : null,
            'percentage' => $percentage,
            'grade' => $studentGrade?->grade?->value,
            'grade_label' => $gradeEnum?->label() ?? $studentGrade?->grade?->label(),
            'notes' => $studentGrade?->notes,

            'graded_at' => $studentGrade?->graded_at?->toIso8601String(),
            'graded_by' => $studentGrade?->gradedBy ? [
                'id' => $studentGrade->gradedBy->id,
                'name' => $studentGrade->gradedBy->name,
            ] : null,

            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
