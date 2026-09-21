<?php

namespace App\Livewire\TeacherPanel\Pages\Payment\Parts;

use App\Models\Invoice;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;

class PaymentStats extends Component
{
    public string $month = '';

    public function mount(): void
    {
        $this->month = session('teacher.payments.selected_month', now()->format('Y-m'));
    }

    #[On('month-changed')]
    public function onMonthChanged(string $month): void
    {
        $this->month = $month;
    }

    #[On('payment-recorded')]
    public function refreshStats(): void
    {
        // re-render
    }

    public function render()
    {
        $user = Auth::guard('teacher')->user();

        $date = Carbon::createFromFormat('Y-m', $this->month);
        $periodStart = $date->copy()->startOfMonth();
        $periodEnd = $date->copy()->endOfMonth();

        $ownerId = $user->getOwnerId();
        $accessibleBranches = $user->getAccessibleBranchIds();

        $invoicesQuery = Invoice::where('teacher_id', $ownerId)
            ->whereDate('period_start', $periodStart->toDateString())
            ->whereDate('period_end', $periodEnd->toDateString())
            ->whereNull('cancelled_at');

        if (!$user->isOwner()) {
            $invoicesQuery->whereIn('branch_id', $accessibleBranches);
        }

        $stats = [
            'total'     => (clone $invoicesQuery)->count(),
            'unpaid'    => (clone $invoicesQuery)->where('status', 'unpaid')->count(),
            'partial'   => (clone $invoicesQuery)->where('status', 'partial')->count(),
            'paid'      => (clone $invoicesQuery)->where('status', 'paid')->count(),
            'collected' => (float) (clone $invoicesQuery)->sum('paid_amount'),
            'remaining' => (float) (clone $invoicesQuery)->sum('remaining_amount'),
        ];

        return view('teacher-panel.pages.payment.parts.payment-stats', [
            'stats' => $stats,
        ]);
    }
}
