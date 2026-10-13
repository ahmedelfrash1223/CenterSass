<?php

namespace App\Services\Teacher;

use App\Enums\Teacher\RevenueSourceEnum;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Payment;
use App\Models\Teacher;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PaymentService
{
    public function __construct(
        protected RevenueService $revenueService,
    ) {}

    /**
     * تسجيل دفعة على فاتورة
     *
     * @param  array<int, array{invoice_item_id: int, amount: float}>  $items
     */
    public function record(
        Invoice $invoice,
        array $items,
        string $paymentMethod,
        Teacher $user,
        ?string $reference = null,
        ?string $paidAt = null,
        ?string $notes = null,
    ): ?Payment {
        if (empty($items)) {
            return null;
        }

        try {
            return DB::transaction(function () use (
                $invoice, $items, $paymentMethod, $user, $reference, $paidAt, $notes
            ) {
                $totalAmount = 0.0;
                foreach ($items as $row) {
                    $totalAmount += (float) $row['amount'];
                }

                if ($totalAmount <= 0) {
                    return null;
                }

                $payment = Payment::create([
                    'teacher_id' => $invoice->teacher_id,
                    'branch_id' => $invoice->branch_id,
                    'invoice_id' => $invoice->id,
                    'student_id' => $invoice->student_id,
                    'receipt_number' => Payment::generateReceiptNumber(),
                    'amount' => $totalAmount,
                    'payment_method' => $paymentMethod,
                    'reference' => $reference,
                    'paid_at' => $paidAt ?? now()->toDateString(),
                    'paid_time' => now()->format('H:i:s'),
                    'notes' => $notes,
                    'created_by' => $user->id,
                ]);

                foreach ($items as $row) {
                    $item = InvoiceItem::where('id', $row['invoice_item_id'])
                        ->where('invoice_id', $invoice->id)
                        ->first();

                    if (! $item) {
                        continue;
                    }

                    $amount = (float) $row['amount'];
                    if ($amount <= 0) {
                        continue;
                    }

                    $payment->allocations()->create([
                        'invoice_item_id' => $item->id,
                        'amount' => $amount,
                    ]);

                    $item->paid_amount = (float) $item->paid_amount + $amount;
                    $item->recalculate();
                    $item->save();
                }

                $invoice->refresh();
                $invoice->recalculate();

                $this->revenueService->recordForSource(
                    user: $user,
                    source: RevenueSourceEnum::STUDENT_PAYMENT,
                    sourceId: $payment->id,
                    amount: $totalAmount,
                    context: [
                        'branch_id' => $invoice->branch_id,
                        'student_id' => $invoice->student_id,
                        'description' => "دفعة إيصال {$payment->receipt_number}",
                        'meta' => [
                            'receipt_number' => $payment->receipt_number,
                            'invoice_id' => $invoice->id,
                        ],
                        'occurred_at' => $payment->paid_at,
                    ],
                );

                return $payment->fresh([
                    'allocations.invoiceItem',
                    'student',
                    'teacher',
                    'creator',
                ]);
            });
        } catch (\Throwable $e) {
            Log::error('Payment record failed', [
                'invoice_id' => $invoice->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return null;
        }
    }

    public function delete(Payment $payment, Teacher $user): bool
    {
        try {
            return DB::transaction(function () use ($payment) {
                foreach ($payment->allocations as $alloc) {
                    $item = $alloc->invoiceItem;
                    if ($item) {
                        $item->paid_amount = max(0, (float) $item->paid_amount - (float) $alloc->amount);
                        $item->recalculate();
                        $item->save();
                    }
                }

                $invoice = $payment->invoice;

                $this->revenueService->deleteForSource(
                    RevenueSourceEnum::STUDENT_PAYMENT,
                    $payment->id,
                );

                $payment->allocations()->delete();
                $payment->delete();

                if ($invoice) {
                    $invoice->refresh();
                    $invoice->recalculate();
                }

                return true;
            });
        } catch (\Throwable $e) {
            Log::error('Payment delete failed', [
                'payment_id' => $payment->id,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }
}
