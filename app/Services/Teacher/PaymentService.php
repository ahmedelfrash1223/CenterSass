<?php

namespace App\Services\Teacher;

use App\Enums\Teacher\PaymentMethodEnum;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Payment;
use App\Models\PaymentAllocation;
use App\Models\Teacher;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PaymentService
{
    public function __construct(
        private readonly InvoiceNumberGenerator $numberGenerator
    ) {}

    public function record(
        Invoice $invoice,
        array $items,
        string $paymentMethod,
        Teacher $user,
        ?string $reference = null,
        ?string $paidAt = null,
        ?string $paidTime = null,
        ?string $notes = null
    ): ?Payment {
        $ownerId = $user->getOwnerId();

        if ($invoice->teacher_id !== $ownerId) {
            Log::warning('PaymentService: invoice owner mismatch', ['invoice_id' => $invoice->id]);
            return null;
        }

        if ($invoice->isCancelled()) {
            Log::warning('PaymentService: invoice is cancelled', ['invoice_id' => $invoice->id]);
            return null;
        }

        if (!PaymentMethodEnum::tryFrom($paymentMethod)) {
            Log::warning('PaymentService: invalid payment method', ['method' => $paymentMethod]);
            return null;
        }

        $validatedItems = [];
        $totalAmount = 0;

        foreach ($items as $entry) {
            $itemId = $entry['invoice_item_id'] ?? null;
            $amount = round((float) ($entry['amount'] ?? 0), 2);

            if (!$itemId || $amount <= 0) continue;

            $item = InvoiceItem::where('id', $itemId)
                ->where('invoice_id', $invoice->id)
                ->first();

            if (!$item) {
                Log::warning('PaymentService: item not found', [
                    'item_id' => $itemId,
                    'invoice_id' => $invoice->id,
                ]);
                continue;
            }

            $itemRemaining = (float) $item->remaining_amount;

            if ($amount > $itemRemaining) {
                $amount = $itemRemaining;
            }

            if ($amount <= 0) continue;

            $validatedItems[] = ['item' => $item, 'amount' => $amount];
            $totalAmount += $amount;
        }

        if (empty($validatedItems) || $totalAmount <= 0) {
            Log::warning('PaymentService: no valid items', ['invoice_id' => $invoice->id]);
            return null;
        }

        try {
            return DB::transaction(function () use (
                $invoice, $validatedItems, $totalAmount, $paymentMethod,
                $user, $reference, $paidAt, $paidTime, $notes
            ) {
                $payment = Payment::create([
                    'receipt_number' => $this->numberGenerator->receipt(),
                    'invoice_id'     => $invoice->id,
                    'student_id'     => $invoice->student_id,
                    'teacher_id'     => $invoice->teacher_id,
                    'amount'         => $totalAmount,
                    'payment_method' => $paymentMethod,
                    'reference'      => $reference ?: null,
                    'paid_at'        => $paidAt ?: now()->toDateString(),
                    'paid_time'      => $paidTime ?: now()->format('H:i'),
                    'notes'          => $notes ?: null,
                    'created_by'     => $user->id,
                ]);

                foreach ($validatedItems as $entry) {
                    PaymentAllocation::create([
                        'payment_id'      => $payment->id,
                        'invoice_item_id' => $entry['item']->id,
                        'group_id'        => $entry['item']->group_id,
                        'amount'          => $entry['amount'],
                    ]);
                }

                $invoice->refresh();

                foreach ($invoice->items as $item) {
                    $item->recalculate();
                }

                $invoice->recalculate();

                return $payment;
            });
        } catch (\Throwable $e) {
            Log::error('PaymentService: exception', [
                'error' => $e->getMessage(),
                'invoice_id' => $invoice->id,
            ]);
            return null;
        }
    }

    public function delete(Payment $payment, Teacher $user): bool
    {
        if ($payment->teacher_id !== $user->getOwnerId()) {
            return false;
        }

        return DB::transaction(function () use ($payment) {
            $invoice = $payment->invoice;
            $payment->delete();
            if ($invoice) {
                $invoice->refresh();
                foreach ($invoice->items as $item) {
                    $item->recalculate();
                }
                $invoice->recalculate();
            }
            return true;
        });
    }
}
