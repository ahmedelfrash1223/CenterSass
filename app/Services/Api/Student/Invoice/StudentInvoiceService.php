<?php

namespace App\Services\Api\Student\Invoice;

use App\Models\Invoice;
use App\Models\StudentAccount;
use App\Services\Teacher\InvoiceService;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class StudentInvoiceService
{
    public function __construct(
        private readonly InvoiceService $invoiceService,
    ) {}


    public function listForAccount(StudentAccount $account, array $filters): LengthAwarePaginator
    {
        $month = $filters['month'] ?? now()->format('Y-m');
        $date = Carbon::createFromFormat('Y-m', $month);
        $periodStart = $date->copy()->startOfMonth();
        $periodEnd = $date->copy()->endOfMonth();

        $students = $account->students;
        $studentIds = $students->pluck('id');

        foreach ($students as $student) {
            $teacher = $student->teacher;
            if ($teacher) {
                $this->invoiceService->ensureForStudent($student, $periodStart, $periodEnd, $teacher);
            }
        }

        $query = Invoice::query()
            ->whereIn('student_id', $studentIds)
            ->whereNull('cancelled_at')
            ->with(['items.group', 'payments'])
            ->orderByDesc('created_at');

        if (! empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        $query->whereBetween('period_start', [
            $periodStart->toDateString(),
            $periodEnd->toDateString(),
        ]);

        $perPage = $filters['per_page'] ?? 15;

        return $query->paginate($perPage);
    }


    public function statsForAccount(StudentAccount $account, ?string $month = null): array
    {
        $month = $month ?: now()->format('Y-m');
        $date = Carbon::createFromFormat('Y-m', $month);
        $periodStart = $date->copy()->startOfMonth();
        $periodEnd = $date->copy()->endOfMonth();

        $students = $account->students;
        $studentIds = $students->pluck('id');

        foreach ($students as $student) {
            $teacher = $student->teacher;
            if ($teacher) {
                $this->invoiceService->ensureForStudent($student, $periodStart, $periodEnd, $teacher);
            }
        }

        $query = Invoice::query()
            ->whereIn('student_id', $studentIds)
            ->whereNull('cancelled_at')
            ->whereBetween('period_start', [
                $periodStart->toDateString(),
                $periodEnd->toDateString(),
            ]);

        $totalInvoices = (clone $query)->count();
        $totalAmount = (float) (clone $query)->sum('total');
        $totalPaid = (float) (clone $query)->sum('paid_amount');
        $totalRemaining = (float) (clone $query)->sum('remaining_amount');

        $unpaidCount = (clone $query)->where('status', 'unpaid')->count();
        $partialCount = (clone $query)->where('status', 'partial')->count();
        $paidCount = (clone $query)->where('status', 'paid')->count();

        return [
            'total_invoices' => $totalInvoices,
            'total_amount' => round($totalAmount, 2),
            'total_paid' => round($totalPaid, 2),
            'total_remaining' => round($totalRemaining, 2),
            'unpaid_count' => $unpaidCount,
            'partial_count' => $partialCount,
            'paid_count' => $paidCount,
        ];
    }
}
