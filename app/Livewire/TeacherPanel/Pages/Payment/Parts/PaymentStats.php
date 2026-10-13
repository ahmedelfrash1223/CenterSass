<?php

namespace App\Livewire\TeacherPanel\Pages\Payment\Parts;

use App\Models\Invoice;
use App\Services\Teacher\PaymentQueryService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;

class PaymentStats extends Component
{
    public string $month = '';

    public string $branchId = '';

    public function mount(): void
    {
        $this->month = session('teacher.payments.selected_month', now()->format('Y-m'));
        $this->branchId = (string) session('teacher.payments.selected_branch', '');
    }

    #[On('month-changed')]
    public function onMonthChanged(string $month): void
    {
        $this->month = $month;
    }

    #[On('branch-changed')]
    public function onBranchChanged(string $branchId): void
    {
        $this->branchId = $branchId;
    }

    #[On('payment-recorded')]
    public function refreshStats(): void
    {
        // re-render
    }

    public function render()
    {
        $user = Auth::guard('teacher')->user();
        $ownerId = $user->getOwnerId();
        $accessibleBranches = $user->getAccessibleBranchIds();
        $service = app(PaymentQueryService::class);

        $date = Carbon::createFromFormat('Y-m', $this->month);
        $periodStart = $date->copy()->startOfMonth();
        $periodEnd = $date->copy()->endOfMonth();

        $branchFilter = $this->branchId !== '' ? (int) $this->branchId : null;

        if ($branchFilter && ! $user->isOwner() && ! in_array($branchFilter, $accessibleBranches)) {
            $branchFilter = null;
        }

        $studentsWithNoPayment = $service->studentsUnpaid($user, $this->month)
            ->when($branchFilter, fn ($q) => $q->where('branch_id', $branchFilter))
            ->count();

        $studentsWithRemaining = $service->studentsPartial($user, $this->month)
            ->when($branchFilter, fn ($q) => $q->where('branch_id', $branchFilter))
            ->count();

        $invoicesQuery = Invoice::query()
            ->where('teacher_id', $ownerId)
            ->whereDate('period_start', $periodStart->toDateString())
            ->whereDate('period_end', $periodEnd->toDateString())
            ->whereNull('cancelled_at');

        if (! $user->isOwner()) {
            empty($accessibleBranches)
                ? $invoicesQuery->whereRaw('1 = 0')
                : $invoicesQuery->whereIn('branch_id', $accessibleBranches);
        }

        if ($branchFilter) {
            $invoicesQuery->where('branch_id', $branchFilter);
        }

        $collected = (float) (clone $invoicesQuery)->sum('paid_amount');

        $paidInvoicesCount = (clone $invoicesQuery)
            ->where('total', '>', 0)
            ->whereRaw('paid_amount >= total')
            ->count();

        $stats = [
            'students_no_payment' => $studentsWithNoPayment,
            'students_remaining' => $studentsWithRemaining,
            'paid_invoices' => $paidInvoicesCount,
            'collected' => $collected,
        ];

        return view('teacher-panel.pages.payment.parts.payment-stats', [
            'stats' => $stats,
        ]);
    }
}
