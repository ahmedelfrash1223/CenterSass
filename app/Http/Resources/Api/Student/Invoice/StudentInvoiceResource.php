<?php

namespace App\Http\Resources\Api\Student\Invoice;

use App\Enums\Teacher\DiscountTypeEnum;
use App\Enums\Teacher\InvoiceStatusEnum;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StudentInvoiceResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $statusEnum = InvoiceStatusEnum::tryFrom($this->status);

        return [
            'id' => $this->id,
            'invoice_number' => $this->invoice_number,
            'period_label' => $this->period_label,
            'period_start' => $this->period_start?->format('Y-m-d'),
            'period_end' => $this->period_end?->format('Y-m-d'),

            'subtotal' => (float) $this->subtotal,
            'discount' => (float) $this->discount,
            'discount_type' => $this->discount_type,
            'discount_type_label' => $this->discount_type
                ? DiscountTypeEnum::tryFrom($this->discount_type)?->label()
                : null,
            'discount_reason' => $this->discount_reason,
            'extra' => (float) $this->extra,
            'total' => (float) $this->total,
            'paid_amount' => (float) $this->paid_amount,
            'remaining_amount' => (float) $this->remaining_amount,

            'status' => $this->status,
            'status_label' => $statusEnum?->label() ?? $this->status,
            'status_color' => $statusEnum?->color() ?? 'secondary',
            'is_paid' => $this->status === InvoiceStatusEnum::PAID->value,
            'is_partial' => $this->status === InvoiceStatusEnum::PARTIAL->value,
            'is_unpaid' => $this->status === InvoiceStatusEnum::UNPAID->value,
            'has_discount' => (bool) $this->has_discount,
            'has_remaining' => (bool) $this->has_remaining,

            'items' => $this->items->map(function ($item) {
                return [
                    'id' => $item->id,
                    'description' => $item->description,
                    'group' => $item->group ? [
                        'id' => $item->group->id,
                        'name' => $item->group->name,
                    ] : null,
                    'quantity' => (float) $item->quantity,
                    'unit_price' => (float) $item->unit_price,
                    'total' => (float) $item->total,
                    'discount_amount' => (float) $item->discount_amount,
                    'net_total' => (float) ($item->discounted_total ?: $item->total),
                    'paid_amount' => (float) $item->paid_amount,
                    'remaining_amount' => (float) $item->remaining_amount,
                ];
            })->values(),

            'payments' => $this->payments->map(function ($payment) {
                return [
                    'id' => $payment->id,
                    'receipt_number' => $payment->receipt_number,
                    'amount' => (float) $payment->amount,
                    'payment_method' => $payment->payment_method,
                    'payment_method_label' => $payment->payment_method_label,
                    'paid_at' => $payment->paid_at?->format('Y-m-d'),
                    'paid_time' => $payment->paid_time?->format('H:i'),
                ];
            })->values(),

            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
