<?php

namespace App\Livewire\TeacherPanel\Pages\Payroll\Parts;

use App\Enums\Teacher\TeacherPermissionEnum;
use App\Models\TeacherPayroll;
use App\Services\Teacher\TeacherPayrollService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class PayrollReview extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public array $reviewedIds = [];

    public array $bonusAmounts = [];

    public array $bonusReasons = [];

    public array $deductionAmounts = [];

    public array $deductionReasons = [];

    public function mount(): void
    {
        $user = Auth::guard('teacher')->user();

        abort_unless($user->hasPermission(TeacherPermissionEnum::PAYROLL_GENERATE), 403);

        $this->reviewedIds = session('payroll.reviewed_ids', []);
    }

    public function updatedReviewedIds(): void
    {
        session(['payroll.reviewed_ids' => $this->reviewedIds]);
    }

    public function markAsReviewed(int $payrollId, TeacherPayrollService $service): void
    {
        $user = Auth::guard('teacher')->user();

        $payroll = TeacherPayroll::whereHas('teacher', function ($q) use ($user) {
            $q->where('id', $user->getOwnerId())->orWhere('owner_id', $user->getOwnerId());
        })->findOrFail($payrollId);

        $service->markAsReviewed($payroll, $user);

        $this->reviewedIds[] = $payrollId;
        session(['payroll.reviewed_ids' => $this->reviewedIds]);

        $this->dispatch('show-success-message', message: 'تم تأكيد المراجعة.');
    }

    public function addBonus(int $payrollId, TeacherPayrollService $service): void
    {
        $user = Auth::guard('teacher')->user();

        $amount = (float) ($this->bonusAmounts[$payrollId] ?? 0);
        $reason = $this->bonusReasons[$payrollId] ?? '';

        if ($amount <= 0 || $reason === '') {
            $this->dispatch('show-error-message', message: 'أدخل المبلغ والسبب.');

            return;
        }

        $payroll = TeacherPayroll::whereHas('teacher', function ($q) use ($user) {
            $q->where('id', $user->getOwnerId())->orWhere('owner_id', $user->getOwnerId());
        })->findOrFail($payrollId);

        $service->addBonus($payroll, $amount, $reason, $user);

        $this->bonusAmounts[$payrollId] = '';
        $this->bonusReasons[$payrollId] = '';

        $this->dispatch('show-success-message', message: 'تم إضافة البونص.');
    }

    public function addDeduction(int $payrollId, TeacherPayrollService $service): void
    {
        $user = Auth::guard('teacher')->user();

        $amount = (float) ($this->deductionAmounts[$payrollId] ?? 0);
        $reason = $this->deductionReasons[$payrollId] ?? '';

        if ($amount <= 0 || $reason === '') {
            $this->dispatch('show-error-message', message: 'أدخل المبلغ والسبب.');

            return;
        }

        $payroll = TeacherPayroll::whereHas('teacher', function ($q) use ($user) {
            $q->where('id', $user->getOwnerId())->orWhere('owner_id', $user->getOwnerId());
        })->findOrFail($payrollId);

        $service->addDeduction($payroll, $amount, $reason, $user);

        $this->deductionAmounts[$payrollId] = '';
        $this->deductionReasons[$payrollId] = '';

        $this->dispatch('show-success-message', message: 'تم إضافة الخصم.');
    }

    public function render()
    {
        $user = Auth::guard('teacher')->user();
        $ownerId = $user->getOwnerId();

        $payrolls = TeacherPayroll::query()
            ->with(['teacher', 'branch', 'bonuses', 'deductionsItems', 'payments'])
            ->whereHas('teacher', fn ($q) => $q->where('id', $ownerId)->orWhere('owner_id', $ownerId))
            ->where('is_reviewed', false)
            ->orderByDesc('created_at')
            ->paginate(10);

        return view('teacher-panel.pages.payroll.parts.payroll-review', [
            'payrolls' => $payrolls,
        ]);
    }
}
