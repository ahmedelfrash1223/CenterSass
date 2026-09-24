<?php

namespace App\Traits\Student;

trait HasStudentPresentation
{
    public function getOwnerId(): int
    {
        return $this->teacher_id;
    }

    public function getBarcodeValueAttribute(): string
    {
        return $this->code ?? ('STD-'.$this->barcode_digits);
    }

    public function getQrValueAttribute(): string
    {
        return $this->code ?? $this->uuid;
    }

    public function getTotalUnpaidAttribute(): float
    {
        return (float) $this->invoices()
            ->whereIn('status', ['unpaid', 'partial'])
            ->sum('remaining_amount');
    }

    public function getHasUnpaidInvoicesAttribute(): bool
    {
        return $this->invoices()
            ->whereIn('status', ['unpaid', 'partial'])
            ->exists();
    }
}
