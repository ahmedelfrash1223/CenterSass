<div>
    @if($showModal && $payment)
        <div class="receipt-modal-backdrop" wire:click="close"></div>
        <div class="receipt-modal-wrapper">
            <div class="receipt-modal-content" wire:click.stop>

                {{-- Receipt Paper --}}
                <div class="receipt-paper" id="printable-receipt">

                    {{-- Header --}}
                    <div class="receipt-header">
                        <h3 class="receipt-shop-name">
                            {{ $payment->teacher?->name ?? 'مركز تعليمي' }}
                        </h3>
                        @if($payment->teacher?->phone)
                            <p class="receipt-shop-phone">
                                <i class="ph-duotone ph-phone me-1"></i>
                                {{ $payment->teacher->phone }}
                            </p>
                        @endif
                        <div class="receipt-divider"></div>
                        <h4 class="receipt-title">إيصال دفع</h4>
                        <p class="receipt-number">
                            <span class="text-muted">رقم الإيصال:</span>
                            <strong>{{ $payment->receipt_number }}</strong>
                        </p>
                    </div>

                    {{-- Student Info --}}
                    <div class="receipt-section">
                        <div class="receipt-info-row">
                            <span class="text-muted">الطالب:</span>
                            <strong>{{ $payment->student?->name }}</strong>
                        </div>
                        <div class="receipt-info-row">
                            <span class="text-muted">الكود:</span>
                            <strong>{{ $payment->student?->code }}</strong>
                        </div>
                        @if($payment->student?->branch)
                            <div class="receipt-info-row">
                                <span class="text-muted">الفرع:</span>
                                <strong>{{ $payment->student->branch->name }}</strong>
                            </div>
                        @endif
                    </div>

                    {{-- Barcode --}}
                    <div class="receipt-barcode">
                        {!! DNS1D::getBarcodeHTML($payment->student?->barcode_digits ?? '0', 'C128', 1.5, 40) !!}
                        <div class="barcode-value">{{ $payment->student?->barcode_digits }}</div>
                    </div>

                    <div class="receipt-divider-dashed"></div>

                    {{-- Payment Details --}}
                    <div class="receipt-section">
                        <div class="receipt-info-row">
                            <span class="text-muted">التاريخ:</span>
                            <strong>{{ $payment->paid_at?->format('Y-m-d') }}</strong>
                        </div>
                        <div class="receipt-info-row">
                            <span class="text-muted">الوقت:</span>
                            <strong>{{ $payment->paid_time?->format('H:i') ?? $payment->created_at->format('H:i') }}</strong>
                        </div>
                        <div class="receipt-info-row">
                            <span class="text-muted">طريقة الدفع:</span>
                            <strong>{{ $payment->payment_method_label }}</strong>
                        </div>
                        @if($payment->reference)
                            <div class="receipt-info-row">
                                <span class="text-muted">المرجع:</span>
                                <strong>{{ $payment->reference }}</strong>
                            </div>
                        @endif
                        <div class="receipt-info-row">
                            <span class="text-muted">الشهر:</span>
                            <strong>{{ $payment->invoice?->period_label }}</strong>
                        </div>
                    </div>

                    <div class="receipt-divider-dashed"></div>

                    {{-- Amount --}}
                    <div class="receipt-amount-section">
                        <div class="receipt-info-row">
                            <span class="text-muted">إجمالي الفاتورة:</span>
                            <span>{{ number_format((float) $payment->invoice?->total, 2) }} ج.م</span>
                        </div>
                        <div class="receipt-info-row">
                            <span class="text-muted">المدفوع سابقاً:</span>
                            <span>{{ number_format((float) $payment->invoice?->paid_amount - $payment->amount, 2) }} ج.م</span>
                        </div>
                        <div class="receipt-amount-total">
                            <span>المبلغ المدفوع الآن:</span>
                            <strong>{{ number_format((float) $payment->amount, 2) }} ج.م</strong>
                        </div>
                        @if($payment->invoice?->remaining_amount > 0)
                            <div class="receipt-info-row mt-2">
                                <span class="text-muted">المتبقي:</span>
                                <span class="text-danger">{{ number_format((float) $payment->invoice->remaining_amount, 2) }} ج.م</span>
                            </div>
                        @else
                            <div class="receipt-paid-stamp mt-2">
                                <i class="ph-duotone ph-check-circle me-1"></i>
                                <span>مدفوع بالكامل</span>
                            </div>
                        @endif
                    </div>

                    {{-- QR Code --}}
                    <div class="receipt-qr">
                        {!! DNS2D::getBarcodeHTML(route('teacher.payments.verify', $payment->receipt_number), 'QRCODE', 3, 3) !!}
                        <p class="qr-caption">امسح للتحقق</p>
                    </div>

                    <div class="receipt-divider-dashed"></div>

                    {{-- Footer --}}
                    <div class="receipt-footer">
                        <p class="mb-1 small text-muted">
                            <i class="ph-duotone ph-user me-1"></i>
                            تم التسجيل بواسطة: {{ $payment->creator?->name ?? '—' }}
                        </p>
                        <p class="mb-0 small text-muted">
                            {{ now()->format('Y-m-d H:i A') }}
                        </p>
                    </div>
                </div>

                {{-- Actions --}}
                <div class="receipt-actions">
                    <button type="button"
                            id="print-receipt-btn"
                            class="btn btn-primary flex-fill">
                        <i class="ph-duotone ph-printer me-1"></i>
                        طباعة
                    </button>
                    <a href="{{ route('teacher.payments.pdf', $payment->id) }}"
                       target="_blank"
                       class="btn btn-success flex-fill">
                        <i class="ph-duotone ph-file-pdf me-1"></i>
                        تحميل PDF
                    </a>
                    <button type="button"
                            wire:click="close"
                            class="btn btn-light border">
                        <i class="ph-duotone ph-x me-1"></i>
                        إغلاق
                    </button>
                </div>

            </div>
        </div>
    @endif

    @script
    <script>
        // Print receipt — attach handler to the button
        const printBtn = document.getElementById('print-receipt-btn');
        if (printBtn) {
            printBtn.addEventListener('click', function () {
                const content = document.getElementById('printable-receipt');
                if (!content) return;

                const printWindow = window.open('', '_blank', 'height=800,width=600');

                const styles = `
                    <style>
                        body { font-family: 'Cairo', Arial, sans-serif; padding: 20px; direction: rtl; }
                        .receipt-paper { max-width: 400px; margin: 0 auto; }
                        .receipt-divider { border-bottom: 2px solid #000; margin: 12px 0; }
                        .receipt-divider-dashed { border-bottom: 1px dashed #999; margin: 12px 0; }
                        .receipt-info-row { display: flex; justify-content: space-between; margin: 6px 0; font-size: 13px; }
                        .receipt-amount-total { display: flex; justify-content: space-between; padding: 10px; background: #f5f5f5; border-radius: 8px; margin-top: 10px; font-size: 16px; font-weight: bold; }
                        .receipt-paid-stamp { text-align: center; padding: 8px; background: #e7f5eb; color: #198754; border-radius: 8px; font-weight: bold; }
                        .receipt-header { text-align: center; }
                        .receipt-title { margin: 8px 0; }
                        .receipt-barcode, .receipt-qr { text-align: center; margin: 12px 0; }
                        .barcode-value, .qr-caption { font-family: monospace; font-size: 12px; letter-spacing: 2px; }
                        .receipt-footer { text-align: center; font-size: 11px; }
                    </style>
                `;

                printWindow.document.write('<!DOCTYPE html><html dir="rtl"><head><meta charset="UTF-8"><title>طباعة الإيصال</title>' + styles + '</head><body>');
                printWindow.document.write(content.innerHTML);
                printWindow.document.write('</body></html>');
                printWindow.document.close();

                setTimeout(function () {
                    printWindow.focus();
                    printWindow.print();
                    printWindow.close();
                }, 300);
            });
        }
    </script>
    @endscript
</div>
