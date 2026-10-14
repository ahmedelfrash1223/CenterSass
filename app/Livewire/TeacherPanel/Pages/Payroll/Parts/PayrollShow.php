<?php

namespace App\Livewire\TeacherPanel\Pages\Payroll\Parts;

use App\Enums\Teacher\TeacherPermissionEnum;
use App\Models\TeacherPayroll;
use App\Services\Teacher\TeacherPayrollService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class PayrollShow extends Component
{
    public TeacherPayroll $payroll;

    public string $bonusAmount = '';

    public string $bonusReason = '';

    public string $deductionAmount = '';

    public string $deductionReason = '';

    public string $payAmount = '';

    public string $paymentMethod = 'cash';

    public string $paymentNotes = '';

    public bool $showBonusForm = false;

    public bool $showDeductionForm = false;

    public bool $showPayForm = false;

    public function mount(TeacherPayroll $payroll): void
    {
        $user = Auth::guard('teacher')->user();

        abort_unless($user->hasPermission(TeacherPermissionEnum::PAYROLL_VIEW), 403);

        abort_unless(
            $payroll->teacher_id === $user->getOwnerId() ||
            $payroll->teacher?->getOwnerId() === $user->getOwnerId(),
            404
        );

        $this->payroll = $payroll->load(['teacher', 'branch', 'bonuses', 'deductionsItems', 'payments']);
    }

    public function addBonus(TeacherPayrollService $service): void
    {
        $user = Auth::guard('teacher')->user();
        abort_unless($user->hasPermission(TeacherPermissionEnum::PAYROLL_ADD_BONUS), 403);

        $this->validate([
            'bonusAmount' => 'required|numeric|min:0.01',
            'bonusReason' => 'required|string|max:255',
        ]);

        $service->addBonus($this->payroll, (float) $this->bonusAmount, $this->bonusReason, $user);

        $this->payroll->refresh()->load(['bonuses', 'deductionsItems', 'payments']);
        $this->reset(['bonusAmount', 'bonusReason', 'showBonusForm']);
        $this->dispatch('show-success-message', message: 'تم إضافة البونص.');
    }

    public function addDeduction(TeacherPayrollService $service): void
    {
        $user = Auth::guard('teacher')->user();
        abort_unless($user->hasPermission(TeacherPermissionEnum::PAYROLL_ADD_DEDUCTION), 403);

        $this->validate([
            'deductionAmount' => 'required|numeric|min:0.01',
            'deductionReason' => 'required|string|max:255',
        ]);

        $service->addDeduction($this->payroll, (float) $this->deductionAmount, $this->deductionReason, $user);

        $this->payroll->refresh()->load(['bonuses', 'deductionsItems', 'payments']);
        $this->reset(['deductionAmount', 'deductionReason', 'showDeductionForm']);
        $this->dispatch('show-success-message', message: 'تم إضافة الخصم.');
    }

    public function markAsPaid(TeacherPayrollService $service): void
    {
        $user = Auth::guard('teacher')->user();
        abort_unless($user->hasPermission(TeacherPermissionEnum::PAYROLL_MARK_PAID), 403);

        $amount = $this->payAmount !== '' ? (float) $this->payAmount : $this->payroll->remainingAmount();

        if ($amount <= 0) {
            $this->dispatch('show-error-message', message: 'المبلغ غير صحيح.');

            return;
        }

        $service->markAsPaid(
            payroll: $this->payroll,
            amount: $amount,
            user: $user,
            paymentMethod: $this->paymentMethod,
            notes: $this->paymentNotes ?: null,
        );

        $this->payroll->refresh()->load(['bonuses', 'deductionsItems', 'payments']);
        $this->reset(['payAmount', 'paymentNotes', 'showPayForm']);
        $this->dispatch('show-success-message', message: 'تم تسجيل الدفع.');
    }

    public function render()
    {
        $user = Auth::guard('teacher')->user();

        return view('teacher-panel.pages.payroll.parts.payroll-show', [
            'canAddBonus' => $user->hasPermission(TeacherPermissionEnum::PAYROLL_ADD_BONUS),
            'canAddDeduction' => $user->hasPermission(TeacherPermissionEnum::PAYROLL_ADD_DEDUCTION),
            'canMarkPaid' => $user->hasPermission(TeacherPermissionEnum::PAYROLL_MARK_PAID),
        ]);
    }
}
