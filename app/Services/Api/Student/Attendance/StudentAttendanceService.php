<?php

namespace App\Services\Api\Student\Attendance;

use App\Models\Attendance;
use App\Models\StudentAccount;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class StudentAttendanceService
{

    public function listForAccount(StudentAccount $account, array $filters): LengthAwarePaginator
    {
        $studentIds = $account->students()->pluck('students.id');

        $query = Attendance::query()
            ->whereIn('student_id', $studentIds)
            ->with([
                'group',
                'schedule.subject',
            ])
            ->orderByDesc('attendance_date')
            ->orderByDesc('check_in_time');

        if (! empty($filters['month'])) {
            $date = Carbon::createFromFormat('Y-m', $filters['month']);
            $query->whereBetween('attendance_date', [
                $date->copy()->startOfMonth()->toDateString(),
                $date->copy()->endOfMonth()->toDateString(),
            ]);
        }

        if (! empty($filters['date_from'])) {
            $query->whereDate('attendance_date', '>=', $filters['date_from']);
        }

        if (! empty($filters['date_to'])) {
            $query->whereDate('attendance_date', '<=', $filters['date_to']);
        }

        if (! empty($filters['group_id'])) {
            $query->where('group_id', $filters['group_id']);
        }

        if (! empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        $perPage = $filters['per_page'] ?? 15;

        return $query->paginate($perPage);
    }


    public function statsForAccount(StudentAccount $account, ?string $month = null): array
    {
        $studentIds = $account->students()->pluck('students.id');

        $query = Attendance::query()->whereIn('student_id', $studentIds);

        if ($month) {
            $date = Carbon::createFromFormat('Y-m', $month);
            $query->whereBetween('attendance_date', [
                $date->copy()->startOfMonth()->toDateString(),
                $date->copy()->endOfMonth()->toDateString(),
            ]);
        }

        $stats = $query
            ->selectRaw('status, count(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        $present = (int) ($stats['present'] ?? 0);
        $late = (int) ($stats['late'] ?? 0);
        $absent = (int) ($stats['absent'] ?? 0);
        $excused = (int) ($stats['excused'] ?? 0);
        $total = $present + $late + $absent + $excused;

        return [
            'total' => $total,
            'present' => $present,
            'late' => $late,
            'absent' => $absent,
            'excused' => $excused,
            'attendance_rate' => $total > 0
                ? round((($present + $late) / $total) * 100, 2)
                : 0,
        ];
    }
}
