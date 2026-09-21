<?php

namespace App\Livewire\TeacherPanel\Pages\Payment\Parts;

use App\Enums\Teacher\DiscountTypeEnum;
use App\Enums\Teacher\PaymentMethodEnum;
use App\Services\Teacher\PaymentService;
use Livewire\Attributes\On;
use Livewire\Component;
use App\Livewire\TeacherPanel\Concerns\HasPaymentPermissions;
use App\Livewire\TeacherPanel\Concerns\HasStudentSelection;
use App\Livewire\TeacherPanel\Concerns\HasPaymentSelection;
use App\Livewire\TeacherPanel\Concerns\HasItemDiscount;
use App\Livewire\TeacherPanel\Concerns\RecordsStudentPayment;

class StudentPaymentCard extends Component
{
    use HasPaymentPermissions;
    use HasStudentSelection;
    use HasPaymentSelection;
    use HasItemDiscount;
    use RecordsStudentPayment;

    public string $selectedMonth = '';

    public function mount(): void
    {
        $this->paid_at = now()->format('Y-m-d');
        $this->selectedMonth = session('teacher.payments.selected_month', now()->format('Y-m'));
    }

    // ============================================
    // Events
    // ============================================

    #[On('month-changed')]
    public function onMonthChanged(string $month): void
    {
        $this->selectedMonth = $month;

        if ($this->student) {
            $this->resetAll();
            $this->refreshDueGroups();
            $this->loadDiscountData();
        }
    }

    #[On('student-selected')]
    public function onStudentSelected(int $studentId): void
    {
        $this->resetAll();

        if (!$this->loadStudentData($studentId)) {
            return;
        }

        $this->selectedMonth = session('teacher.payments.selected_month', now()->format('Y-m'));

        $this->refreshDueGroups();
        $this->loadDiscountData();
    }

    public function updatedSelectedMonth(): void
    {
        $this->resetAll();
        $this->refreshDueGroups();
        $this->loadDiscountData();
    }


    public function clear(): void
    {
        $this->clearStudent();
    }

    // ============================================
    // Helpers
    // ============================================

    protected function resetAll(): void
    {
        $this->resetSelections();
        $this->resetDiscounts();
        $this->resetPaymentForm();

        $this->activeReceipt = null;
        $this->activeReceiptBreakdown = [];
    }

    // ============================================
    // Render
    // ============================================

    public function render()
    {
        return view('teacher-panel.pages.payment.parts.student-payment-card', [
            'paymentMethods'   => PaymentMethodEnum::options(),
            'discountTypeList' => DiscountTypeEnum::options(),
            'canManage'        => $this->canManagePayments(),
        ]);
    }
}
