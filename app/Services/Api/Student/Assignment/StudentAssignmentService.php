<?php

namespace App\Services\Api\Student\Assignment;

use App\Models\AssignmentStudent;
use App\Models\StudentAccount;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class StudentAssignmentService
{

    public function listForAccount(StudentAccount $account, array $filters): LengthAwarePaginator
    {
        $studentIds = $account->students()->pluck('students.id');

        $query = AssignmentStudent::query()
            ->whereIn('student_id', $studentIds)
            ->with([
                'assignment.group.schedules.subject',
                'assignment.group',
                'gradedBy',
            ])
            ->whereNotNull('score')
            ->orderByDesc('graded_at')
            ->orderByDesc('id');

        if (! empty($filters['group_id'])) {
            $query->whereHas('assignment', function ($q) use ($filters) {
                $q->where('group_id', $filters['group_id']);
            });
        }

        if (! empty($filters['type'])) {
            $query->whereHas('assignment', function ($q) use ($filters) {
                $q->where('type', $filters['type']);
            });
        }

        if (! empty($filters['month'])) {
            $date = Carbon::createFromFormat('Y-m', $filters['month']);
            $query->whereBetween('graded_at', [
                $date->copy()->startOfMonth(),
                $date->copy()->endOfMonth(),
            ]);
        }

        $perPage = $filters['per_page'] ?? 15;

        return $query->paginate($perPage);
    }


    public function statsForAccount(StudentAccount $account, ?string $month = null): array
    {
        $studentIds = $account->students()->pluck('students.id');

        $query = AssignmentStudent::query()
            ->whereIn('student_id', $studentIds)
            ->whereNotNull('score');

        if ($month) {
            $date = Carbon::createFromFormat('Y-m', $month);
            $query->whereBetween('graded_at', [
                $date->copy()->startOfMonth(),
                $date->copy()->endOfMonth(),
            ]);
        }

        $total = (clone $query)->count();
        $average = (clone $query)->avg('score');

        return [
            'total_graded' => $total,
            'average_score' => $average ? round((float) $average, 2) : 0,
        ];
    }
}
