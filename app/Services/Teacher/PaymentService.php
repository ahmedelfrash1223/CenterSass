<?php

namespace App\Services\Teacher;

use App\Enums\Teacher\PaymentMethodEnum;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Teacher;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class PaymentService
{
    public function __construct(
        private readonly InvoiceNumberGenerator $numberGenerator
    ) {}

    /**
     * Record a payment against an invoice.
     *
     * @return Payment|null
     */
    public function record(
        Invoice $invoice,
        float $amount,
        string $paymentMethod,
        Teacher $user,
        ?string $reference = null,
        ?string $paidAt = null,
        ?string $paidTime = null,
        ?string $notes = null
    ): ?Payment {
        $ownerId = $user->getOwnerId();

        // Guard: invoice must belong to the same owner
        if ($invoice->teacher_id !== $ownerId) {
            return null;
        }

        // Guard: invoice must not be cancelled or fully paid
        if ($invoice->isCancelled() || $invoice->isPaid()) {
            return null;
        }

        // Guard: amount must be > 0 and <= remaining
        $amount = round($amount, 2);
        $remaining = (float) $invoice->remaining_amount;

        if ($amount <= 0 || $amount > $remaining) {
            return null;
        }

        // Guard: valid payment method
        if (!PaymentMethodEnum::tryFrom($paymentMethod)) {
            return null;
        }

        return DB::transaction(function () use (
            $invoice, $amount, $paymentMethod, $user, $reference, $paidAt, $paidTime, $notes
        ) {
            $payment = Payment::create([
                'receipt_number' => $this->numberGenerator->receipt(),
                'invoice_id' => $invoice->id,
                'student_id' => $invoice->student_id,
                'teacher_id' => $invoice->teacher_id,
                'amount' => $amount,
                'payment_method' => $paymentMethod,
                'reference' => $reference ?: null,
                'paid_at' => $paidAt ?: now()->toDateString(),
                'paid_time' => $paidTime ?: now()->format('H:i'),
                'notes' => $notes ?: null,
                'created_by' => $user->id,
            ]);

            // Recalculate invoice totals + status
            $invoice->recalculate();

            return $payment;
        });
    }

    /**
     * Delete a payment and recalculate the invoice.
     * Only allowed for the same owner.
     */
    public function delete(Payment $payment, Teacher $user): bool
    {
        if ($payment->teacher_id !== $user->getOwnerId()) {
            return false;
        }

        return DB::transaction(function () use ($payment) {
            $invoice = $payment->invoice;

            $payment->delete();

            if ($invoice) {
                $invoice->recalculate();
            }

            return true;
        });
    }

    /**
     * Refund = create a negative payment entry (future use).
     * Not implemented yet.
     */
}
