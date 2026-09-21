<div>
    @if($showModal && $payment)
        <div class="modal fade show d-block" style="background: rgba(0,0,0,0.5);" wire:click.self="close">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="ph-duotone ph-receipt me-1"></i>
                            إيصال الدفع — {{ $payment->receipt_number }}
                        </h5>
                        <button type="button" class="btn-close" wire:click="close"></button>
                    </div>
                    <div class="modal-body bg-body-tertiary">
                        <div class="bg-white text-dark rounded-3 p-4 mx-auto border"
                             id="printable-receipt-modal" style="max-width: 400px;">
                            @include('teacher-panel.pages.payment.parts.receipt-content', [
                                'payment'   => $payment,
                                'breakdown' => $breakdown,
                            ])
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" onclick="printModalReceipt()" class="btn btn-primary">
                            <i class="ph-duotone ph-printer me-1"></i>طباعة
                        </button>
                        <button type="button" wire:click="close" class="btn btn-outline-secondary">إغلاق</button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    @script
    <script>
        window.printModalReceipt = function () {
            const content = document.getElementById('printable-receipt-modal');
            if (!content) return;

            const printWindow = window.open('', '_blank', 'height=800,width=600');
            printWindow.document.write(`
                <!DOCTYPE html>
                <html dir="rtl"><head><meta charset="UTF-8"><title>طباعة</title>
                <style>
                    * { margin:0; padding:0; box-sizing:border-box; }
                    body { font-family: 'Cairo', sans-serif; padding: 8mm; direction: rtl; font-size: 11px; }
                    .receipt-paper { max-width: 80mm; margin: 0 auto; }
                    .receipt-header { text-align: center; padding-bottom: 10px; border-bottom: 2px solid #212529; margin-bottom: 12px; }
                    .receipt-shop-name { font-size: 16px; font-weight: bold; }
                    .receipt-title { font-size: 13px; color: #0d6efd; }
                    .receipt-divider { border-top: 2px solid #212529; margin: 10px 0; }
                    .receipt-divider-dashed { border-top: 1px dashed #999; margin: 10px 0; }
                    .receipt-info-row { display: flex; justify-content: space-between; padding: 3px 0; font-size: 10px; }
                    .receipt-info-row span:first-child { color: #6c757d; }
                    .receipt-barcode, .receipt-qr { text-align: center; margin: 10px 0; }
                    .receipt-barcode svg, .receipt-qr svg { max-width: 100%; }
                    .barcode-value, .qr-caption { font-family: monospace; font-size: 10px; }
                    .receipt-amount-section { background: #f5f5f5; padding: 10px; border-radius: 6px; margin: 10px 0; }
                    .receipt-amount-total { display: flex; justify-content: space-between; padding-top: 8px; margin-top: 8px; border-top: 2px solid #ccc; font-size: 13px; font-weight: bold; color: #198754; }
                    .receipt-paid-stamp { text-align: center; padding: 8px; background: #e7f5eb; color: #198754; border-radius: 6px; font-weight: bold; }
                    .receipt-footer { text-align: center; font-size: 9px; color: #6c757d; margin-top: 14px; padding-top: 8px; border-top: 1px dashed #ccc; }
                    @page { margin: 5mm; size: 80mm auto; }
                </style>
                </head><body>${content.innerHTML}</body></html>
            `);
            printWindow.document.close();
            setTimeout(() => { printWindow.focus(); printWindow.print(); printWindow.close(); }, 300);
        };
    </script>
    @endscript
</div>
