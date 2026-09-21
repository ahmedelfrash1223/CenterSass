<?php

namespace App\Livewire\TeacherPanel\Pages\Invoice\Parts;

use App\Enums\Teacher\DiscountTypeEnum;
use App\Models\Invoice;
use App\Services\Teacher\DiscountService;
use Livewire\Component;
use App\Livewire\TeacherPanel\Concerns\HasPaymentPermissions;

class DiscountManager extends Component
{
    use HasPaymentPermissions;

    public Invoice $invoice;

    public string $discount_type = 'fixed';
    public string $discount_value = '0';
    public string $discount_reason = '';

    public bool $showForm = false;

    public function mount(Invoice $invoice): void
    {
        $this->invoice = $invoice;
        $this->resetForm();
    }

    protected function resetForm(): void
    {
        $this->discount_type = $this->invoice->discount_type ?: DiscountTypeEnum::FIXED->value;
        $this->discount_value = (string) ($this->invoice->discount_value ?: '0');
        $this->discount_reason = $this->invoice->discount_reason ?? '';
    }

    public function openForm(): void
    {
        $this->showForm = true;
    }

    public function cancel(): void
    {
        $this->showForm = false;
        $this->resetForm();
    }

    public function apply(DiscountService $service): void
    {
        if (!$this->denyUnless($this->canManagePayments())) {
            return;
        }

        $this->validate([
            'discount_type'   => 'required|in:' . implode(',', DiscountTypeEnum::values()),
            'discount_value'  => 'required|numeric|min:0',
            'discount_reason' => 'nullable|string|max:255',
        ], [
            'discount_type.required'  => 'نوع الخصم مطلوب.',
            'discount_value.required' => 'قيمة الخصم مطلوبة.',
            'discount_value.min'      => 'القيمة لا يمكن أن تكون سالبة.',
        ]);

        if ($this->discount_type === DiscountTypeEnum::PERCENTAGE->value
            && (float) $this->discount_value > 100) {
            $this->addError('discount_value', 'النسبة لا يمكن أن تتجاوز 100%.');
            return;
        }

        $ok = $service->apply(
            invoice: $this->invoice,
            discountType: $this->discount_type,
            discountValue: (float) $this->discount_value,
            reason: $this->discount_reason ?: null,
            user: $this->currentUser(),
        );

        if (!$ok) {
            $this->dispatch('show-error-message', message: 'فشل في تطبيق الخصم.');
            return;
        }

        $this->invoice->refresh();
        $this->showForm = false;

        $this->dispatch('show-success-message', message: 'تم تطبيق الخصم بنجاح.');
        $this->dispatch('discount-updated')->to('teacher-panel.pages.invoice.parts.invoice-show');
        $this->dispatch('discount-updated');
    }

    public function remove(DiscountService $service): void
    {
        if (!$this->denyUnless($this->canManagePayments())) {
            return;
        }

        $ok = $service->remove($this->invoice, $this->currentUser());

        if (!$ok) {
            $this->dispatch('show-error-message', message: 'فشل في إزالة الخصم.');
            return;
        }

        $this->invoice->refresh();
        $this->resetForm();
        $this->showForm = false;

        $this->dispatch('show-success-message', message: 'تم إزالة الخصم.');
        $this->dispatch('discount-updated')->to('teacher-panel.pages.invoice.parts.invoice-show');
        $this->dispatch('discount-updated');
    }

    public function render()
    {
        return view('teacher-panel.pages.invoice.parts.discount-manager', [
            'discountTypes' => DiscountTypeEnum::options(),
        ]);
    }
}
