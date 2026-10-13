<div>
    {{-- Filters --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <div class="row g-3 align-items-end">
                <div class="col-md-5">
                    <label class="form-label small fw-semibold text-body-secondary">البحث</label>
                    <input type="text" wire:model.live.debounce.300ms="search" class="form-control"
                        placeholder="اسم الطالب، رقم الفاتورة...">
                </div>

                <div class="col-md-4">
                    <label class="form-label small fw-semibold text-body-secondary">الحالة</label>
                    <select wire:model.live="statusFilter" class="form-select">
                        <option value="">كل الحالات</option>
                        <option value="unpaid">مستحق</option>
                        <option value="partial">مدفوع جزئياً</option>
                        <option value="paid">مدفوع</option>
                        <option value="cancelled">ملغي</option>
                    </select>
                </div>

                <div class="col-md-3">
                    <button type="button" wire:click="clearFilters" class="btn btn-outline-secondary w-100">
                        <i class="ph-duotone ph-funnel-simple me-1"></i>تفريغ
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Stats — نفس بتاعة صفحة الدفع --}}
    @include('teacher-panel.pages.payment.parts.payment-stats', ['stats' => $stats])

    {{-- Invoices Table --}}
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">#</th>
                            <th>الفاتورة</th>
                            <th>الطالب</th>
                            <th>الشهر</th>
                            <th class="text-center">قبل الخصم</th>
                            <th class="text-center">الخصم</th>
                            <th class="text-center">الإجمالي</th>
                            <th class="text-center">المدفوع</th>
                            <th class="text-center">المتبقي</th>
                            <th class="text-center">الحالة</th>
                            <th class="text-center">إجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($invoices as $invoice)
                            <tr wire:key="invoice-{{ $invoice->id }}">
                                <td class="ps-4 text-body-secondary small fw-semibold">
                                    {{ $invoices->firstItem() + $loop->index }}
                                </td>

                                <td>
                                    <span class="fw-semibold small">{{ $invoice->invoice_number }}</span>
                                    <small class="text-body-secondary d-block">
                                        {{ $invoice->created_at?->format('Y-m-d') }}
                                    </small>
                                </td>

                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="rounded-circle bg-primary-subtle text-primary d-flex align-items-center justify-content-center fw-bold flex-shrink-0"
                                            style="width: 34px; height: 34px; font-size: 14px;">
                                            {{ mb_substr($invoice->student?->name ?? '?', 0, 1) }}
                                        </div>
                                        <div>
                                            <span class="fw-semibold d-block small">{{ $invoice->student?->name }}</span>
                                            <small class="text-body-secondary">{{ $invoice->student?->code }}</small>
                                        </div>
                                    </div>
                                </td>

                                <td><small>{{ $invoice->period_label }}</small></td>

                                <td class="text-center">
                                    <small class="text-body-secondary">
                                        {{ number_format((float) $invoice->subtotal, 2) }}
                                    </small>
                                </td>

                                <td class="text-center">
                                    @if($invoice->has_discount)
                                        <span class="text-success small fw-semibold">
                                            - {{ number_format((float) $invoice->discount, 2) }}
                                        </span>
                                        @if($invoice->discount_type === 'percentage')
                                            <small class="text-body-secondary d-block">
                                                {{ (float) $invoice->discount_value }}%
                                            </small>
                                        @else
                                            <small class="text-body-secondary d-block">ثابت</small>
                                        @endif
                                    @else
                                        <span class="text-body-secondary">—</span>
                                    @endif
                                </td>

                                <td class="text-center fw-bold">{{ number_format((float) $invoice->total, 2) }}</td>

                                <td class="text-center text-success fw-semibold">
                                    {{ number_format((float) $invoice->paid_amount, 2) }}
                                </td>

                                <td class="text-center">
                                    @if($invoice->remaining_amount > 0)
                                        <span class="text-danger fw-semibold">
                                            {{ number_format((float) $invoice->remaining_amount, 2) }}
                                        </span>
                                    @else
                                        <span class="text-body-secondary">—</span>
                                    @endif
                                </td>

                                <td class="text-center">
                                    <span class="badge bg-{{ $invoice->status_color }} text-white">
                                        {{ $invoice->status_label }}
                                    </span>
                                </td>

                                <td class="text-center">
                                    <div class="d-flex justify-content-center gap-1">
                                        <a href="{{ route('teacher.invoices.show', $invoice->id) }}" wire:navigate
                                            class="btn btn-sm btn-outline-secondary" title="عرض">
                                            <i class="ph-duotone ph-eye"></i>
                                        </a>

                                        @if($canDelete)
                                            <button type="button" wire:click="requestDelete({{ $invoice->id }})"
                                                class="btn btn-sm btn-outline-danger" title="حذف">
                                                <i class="ph-duotone ph-trash"></i>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>

                            @if($confirmingDeleteId === $invoice->id)
                                <tr wire:key="confirm-delete-{{ $invoice->id }}">
                                    <td colspan="11" class="p-0">
                                        <div class="alert alert-danger m-0 rounded-0 d-flex align-items-center flex-wrap gap-3">
                                            <i class="ph-duotone ph-warning-circle fs-3"></i>
                                            <div class="flex-grow-1">
                                                <strong>تأكيد حذف الفاتورة</strong>
                                                <p class="small mb-0">
                                                    هل أنت متأكد من حذف الفاتورة
                                                    <strong>{{ $invoice->invoice_number }}</strong>؟
                                                    @if((float) $invoice->paid_amount > 0)
                                                        <br>
                                                        <span class="text-warning-emphasis">
                                                            <i class="ph-duotone ph-info me-1"></i>
                                                            الفاتورة عليها دفعات بقيمة
                                                            <strong>{{ number_format((float) $invoice->paid_amount, 2) }}
                                                                ج.م</strong>
                                                            — هتتحذف مع الفاتورة.
                                                        </span>
                                                    @endif
                                                </p>
                                            </div>
                                            <div class="d-flex gap-2">
                                                <button type="button" wire:click="deleteInvoice({{ $invoice->id }})"
                                                    wire:loading.attr="disabled" class="btn btn-danger btn-sm">
                                                    <span wire:loading.remove wire:target="deleteInvoice">
                                                        <i class="ph-duotone ph-check me-1"></i>
                                                        تأكيد الحذف
                                                    </span>
                                                    <span wire:loading wire:target="deleteInvoice">
                                                        <span class="spinner-border spinner-border-sm me-1"></span>
                                                        جاري...
                                                    </span>
                                                </button>
                                                <button type="button" wire:click="cancelDelete"
                                                    class="btn btn-outline-secondary btn-sm">
                                                    إلغاء
                                                </button>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endif

                        @empty
                            <tr>
                                <td colspan="11">
                                    <div class="text-center text-body-secondary py-5">
                                        <i class="ph-duotone ph-receipt fs-1 d-block mb-2"></i>
                                        <h6 class="fw-bold">لا توجد فواتير</h6>
                                        <p class="small mb-0">لم يتم توليد أي فواتير بعد.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if($invoices->hasPages())
            <div class="card-footer bg-body">{{ $invoices->links() }}</div>
        @endif
    </div>
</div>
