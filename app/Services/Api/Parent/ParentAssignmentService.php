<?php

namespace App\Services\Api\Parent;

use App\Models\Assignment;
use App\Models\AssignmentStudent;
use App\Models\Student;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ParentAssignmentService
{

    public function listForChild(Student $child, array $filters): LengthAwarePaginator
    {
        $groupIds = $child->activeGroups()->pluck('groups.id');

        $query = Assignment::query()
            ->whereIn('group_id', $groupIds)
            ->with([
                'group.schedules.subject',
                'group',
                'creator',
                'grades' => function ($q) use ($child) {
                    $q->where('student_id', $child->id);
                },
                'grades.student',
                'grades.gradedBy'
            ])
            ->orderByDesc('created_at');

        if (! empty($filters['group_id'])) {
            $query->where('group_id', $filters['group_id']);
        }

        if (! empty($filters['type'])) {
            $query->where('type', $filters['type']);
        }

        if (! empty($filters['month'])) {
            $date = Carbon::createFromFormat('Y-m', $filters['month']);
            $query->whereBetween('created_at', [
                $date->copy()->startOfMonth(),
                $date->copy()->endOfMonth(),
            ]);
        }

        return $query->paginate($filters['per_page'] ?? 15);
    }


    public function statsForChild(Student $child, ?string $month = null): array
    {
        $groupIds = $child->activeGroups()->pluck('groups.id');

        $assignmentsQuery = Assignment::whereIn('group_id', $groupIds)
            ->with(['grades' => function($q) use ($child) {
                $q->where('student_id', $child->id);
            }]);

        if ($month) {
            $date = Carbon::createFromFormat('Y-m', $month);
            $assignmentsQuery->whereBetween('created_at', [
                $date->copy()->startOfMonth(),
                $date->copy()->endOfMonth(),
            ]);
        }

        $assignments = $assignmentsQuery->get();

        $totalAssignments = $assignments->count();
        $gradedCount = 0;
        $totalPercentage = 0;
        $highestPercentage = 0;
        $lowestPercentage = 100;

        foreach ($assignments as $assignment) {
            $studentGrade = $assignment->grades->first();

            if ($studentGrade && $studentGrade->score !== null) {
                $maxScore = (float) $assignment->max_score;
                $score = (float) $studentGrade->score;

                if ($maxScore > 0) {
                    $percentage = ($score / $maxScore) * 100;
                    $totalPercentage += $percentage;
                    $gradedCount++;

                    if ($percentage > $highestPercentage) $highestPercentage = $percentage;
                    if ($percentage < $lowestPercentage) $lowestPercentage = $percentage;
                }
            }
        }

        $averagePercentage = $gradedCount > 0 ? ($totalPercentage / $gradedCount) : 0;

        return [
            'total_assignments' => $totalAssignments,
            'graded_count' => $gradedCount,
            'ungraded_count' => $totalAssignments - $gradedCount,
            'average_percentage' => round($averagePercentage, 2),
            'highest_percentage' => $gradedCount > 0 ? round($highestPercentage, 2) : 0,
            'lowest_percentage' => $gradedCount > 0 ? round($lowestPercentage, 2) : 0,
        ];
    }
}
