<?php

namespace App\Services\Teacher;

use App\Enums\Teacher\ExpenseSourceEnum;
use App\Enums\Teacher\RevenueSourceEnum;
use App\Models\Branch;
use App\Models\Expense;
use App\Models\MonthlyClosing;
use App\Models\MonthlyClosingItem;
use App\Models\MonthlyClosingLog;
use App\Models\Revenue;
use App\Models\Teacher;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class MonthlyClosingService
{
    public function __construct(
        protected MonthlyClosingPdfService $pdfService,
    ) {}


    public function closeMonth(Teacher $user, string $month, ?int $branchId = null, ?string $notes = null): MonthlyClosing
    {
        $ownerId = $user->getOwnerId();

        $date = Carbon::createFromFormat('Y-m', $month);
        $periodStart = $date->copy()->startOfMonth();
        $periodEnd = $date->copy()->endOfMonth();

        $exists = MonthlyClosing::where('teacher_id', $ownerId)
            ->where('month', $month)
            ->when($branchId, fn($q) => $q->where('branch_id', $branchId), fn($q) => $q->whereNull('branch_id'))
            ->where('status', 'closed')
            ->exists();

        if ($exists) {
            throw new \RuntimeException('تم تقفيل هذا الشهر بالفعل لهذا الفرع.');
        }

        return DB::transaction(function () use ($user, $ownerId, $month, $branchId, $notes, $periodStart, $periodEnd) {
            $revenues = Revenue::query()
                ->where('teacher_id', $ownerId)
                ->whereBetween('occurred_at', [$periodStart, $periodEnd])
                ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
                ->get();

            $expenses = Expense::query()
                ->where('teacher_id', $ownerId)
                ->whereBetween('occurred_at', [$periodStart, $periodEnd])
                ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
                ->get();

            $totalRevenue = (float) $revenues->sum('amount');
            $totalExpense = (float) $expenses->sum('amount');
            $netAmount = $totalRevenue - $totalExpense;

            $closing = MonthlyClosing::create([
                'teacher_id' => $ownerId,
                'branch_id' => $branchId,
                'closed_by' => $user->id,
                'month' => $month,
                'total_revenue' => $totalRevenue,
                'total_expense' => $totalExpense,
                'net_amount' => $netAmount,
                'revenues_count' => $revenues->count(),
                'expenses_count' => $expenses->count(),
                'status' => 'closed',
                'closed_at' => now(),
                'notes' => $notes,
            ]);

            // 5) بنود الإيرادات
            $revenues->each(function (Revenue $r) use ($closing) {
                MonthlyClosingItem::create([
                    'monthly_closing_id' => $closing->id,
                    'type' => 'revenue',
                    'source_id' => $r->id,
                    'reference' => $r->reference,
                    'amount' => $r->amount,
                    'source_type' => $r->source instanceof RevenueSourceEnum ? $r->source->value : $r->source,
                    'description' => $r->description,
                    'occurred_at' => $r->occurred_at,
                ]);
            });

            $expenses->each(function (Expense $e) use ($closing) {
                MonthlyClosingItem::create([
                    'monthly_closing_id' => $closing->id,
                    'type' => 'expense',
                    'source_id' => $e->id,
                    'reference' => $e->reference,
                    'amount' => $e->amount,
                    'source_type' => $e->source instanceof ExpenseSourceEnum ? $e->source->value : $e->source,
                    'description' => $e->description ?: ($e->reason ?? null),
                    'occurred_at' => $e->occurred_at,
                ]);
            });

            // 7) log
            MonthlyClosingLog::create([
                'monthly_closing_id' => $closing->id,
                'teacher_id' => $user->id,
                'action' => 'created',
                'notes' => 'تم تقفيل الشهر',
                'meta' => [
                    'revenues_count' => $revenues->count(),
                    'expenses_count' => $expenses->count(),
                ],
            ]);

            return $closing;
        });
    }


    public function reopen(Teacher $user, MonthlyClosing $closing): void
    {
        if ($user->getOwnerId() !== $closing->teacher_id) {
            throw new \RuntimeException('غير مصرح.');
        }

        if (! $closing->isClosed()) {
            return;
        }

        DB::transaction(function () use ($user, $closing) {
            $closing->update(['status' => 'draft', 'closed_at' => null]);

            MonthlyClosingLog::create([
                'monthly_closing_id' => $closing->id,
                'teacher_id' => $user->id,
                'action' => 'reopened',
                'notes' => 'تم إعادة فتح الشهر',
            ]);
        });
    }


    public function delete(Teacher $user, MonthlyClosing $closing): void
    {
        if ($user->getOwnerId() !== $closing->teacher_id) {
            throw new \RuntimeException('غير مصرح.');
        }

        DB::transaction(function () use ($closing) {
            $closing->items()->delete();
            $closing->logs()->delete();
            $closing->delete();
        });
    }
}
