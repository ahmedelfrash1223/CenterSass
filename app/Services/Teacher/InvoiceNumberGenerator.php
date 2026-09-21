<?php

namespace App\Services\Teacher;

use App\Models\Invoice;
use App\Models\Payment;
use Carbon\Carbon;

class InvoiceNumberGenerator
{
    public function invoice(): string
    {
        $year = Carbon::now()->year;
        $prefix = "INV-{$year}-";

        $lastNumber = Invoice::query()
            ->where('invoice_number', 'like', $prefix . '%')
            ->orderByDesc('id')
            ->value('invoice_number');

        $next = 1;

        if ($lastNumber) {
            $parts = explode('-', $lastNumber);
            $next = ((int) end($parts)) + 1;
        }

        do {
            $number = $prefix . str_pad((string) $next, 5, '0', STR_PAD_LEFT);
            $next++;
        } while (Invoice::where('invoice_number', $number)->exists());

        return $number;
    }

    public function receipt(): string
    {
        $year = Carbon::now()->year;
        $prefix = "REC-{$year}-";

        $lastNumber = Payment::query()
            ->where('receipt_number', 'like', $prefix . '%')
            ->orderByDesc('id')
            ->value('receipt_number');

        $next = 1;

        if ($lastNumber) {
            $parts = explode('-', $lastNumber);
            $next = ((int) end($parts)) + 1;
        }

        do {
            $number = $prefix . str_pad((string) $next, 5, '0', STR_PAD_LEFT);
            $next++;
        } while (Payment::where('receipt_number', $number)->exists());

        return $number;
    }
}
