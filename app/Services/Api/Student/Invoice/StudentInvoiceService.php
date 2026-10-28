<?php

namespace App\Services\Api\Student\Invoice;

use App\Models\Invoice;
use App\Models\StudentAccount;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class StudentInvoiceService
{

    public function listForAccount(StudentAccount $account, array $filters): LengthAwarePaginator
    {
        $studentIds = $account->students()->pluck('students.id');

        $query = Invoice::query()
            ->whereIn('student_id', $studentIds)
            ->whereNull('cancelled_at')
            ->with(['items.group', 'payments'])
            ->orderByDesc('created_at');

        if (! empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (! empty($filters['month'])) {
            $date = Carbon::createFromFormat('Y-m', $filters['month']);
            $query->whereBetween('period_start', [
                $date->copy()->startOfMonth()->toDateString(),
                $date->copy()->endOfMonth()->toDateString(),
            ]);
        }

        $perPage = $filters['per_page'] ?? 15;

        return $query->paginate($perPage);
    }


    public function statsForAccount(StudentAccount $account, ?string $month = null): array
    {
        $studentIds = $account->students()->pluck('students.id');

        $query = Invoice::query()
            ->whereIn('student_id', $studentIds)
            ->whereNull('cancelled_at');

        if ($month) {
            $date = Carbon::createFromFormat('Y-m', $month);
            $query->whereBetween('period_start', [
                $date->copy()->startOfMonth()->toDateString(),
                $date->copy()->endOfMonth()->toDateString(),
            ]);
        }

        $totalAmount = (float) (clone $query)->sum('total');
        $totalPaid = (float) (clone $query)->sum('paid_amount');
        $totalRemaining = (float) (clone $query)->sum('remaining_amount');
        $unpaidCount = (clone $query)->whereIn('status', ['unpaid', 'partial'])->count();

        return [
            'total_invoices' => (clone $query)->count(),
            'total_amount' => round($totalAmount, 2),
            'total_paid' => round($totalPaid, 2),
            'total_remaining' => round($totalRemaining, 2),
            'unpaid_or_partial' => $unpaidCount,
        ];
    }
}
