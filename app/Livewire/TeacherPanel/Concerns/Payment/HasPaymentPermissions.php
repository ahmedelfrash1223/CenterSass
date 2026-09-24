<?php

namespace App\Livewire\TeacherPanel\Concerns\Payment;

use App\Enums\Teacher\TeacherPermissionEnum;
use Illuminate\Support\Facades\Auth;

trait HasPaymentPermissions
{
    protected function currentUser()
    {
        return Auth::guard('teacher')->user();
    }

    protected function canManagePayments(): bool
    {
        return $this->currentUser()
            ->hasPermission(TeacherPermissionEnum::PAYMENT_MANAGE);
    }

    protected function canDeletePayments(): bool
    {
        return $this->currentUser()
            ->hasPermission(TeacherPermissionEnum::PAYMENT_DELETE);
    }

    protected function canViewPayments(): bool
    {
        return $this->currentUser()
            ->hasPermission(TeacherPermissionEnum::PAYMENT_VIEW);
    }

    protected function canPrintReceipts(): bool
    {
        return $this->currentUser()
            ->hasPermission(TeacherPermissionEnum::PAYMENT_RECEIPT);
    }

    protected function denyUnless(bool $condition, string $message = 'ليس لديك صلاحية.'): bool
    {
        if (!$condition) {
            $this->dispatch('show-error-message', message: $message);
            return false;
        }

        return true;
    }
}
