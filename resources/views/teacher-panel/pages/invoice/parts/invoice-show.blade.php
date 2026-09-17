<div>
    @if(session()->has('invoice-success'))
        <div class="alert alert-success d-flex align-items-center gap-2 mb-4">
            <i class="ph-duotone ph-check-circle fs-5"></i>
            <span>{{ session('invoice-success') }}</span>
        </div>
    @endif

    @if(session()->has('invoice-error'))
        <div class="alert alert-danger d-flex align-items-center gap-2 mb-4">
            <i class="ph-duotone ph-x-circle fs-5"></i>
            <span>{{ session('invoice-error') }}</span>
        </div>
    @endif

    <div class="row g-4">

        {{-- Invoice Details --}}
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header close-day-header">
                    <h6 class="mb-0 fw-bold">
                        <i class="ph-duotone ph-info me-1"></i>
                        بيانات الفاتورة
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-sm-6">
                            <small class="text-muted d-block mb-1">رقم الفاتورة</small>
                            <span class="fw-semibold small">{{ $invoice->invoice_number }}</span>
                        </div>
                        <div class="col-sm-6">
                            <small class="text-muted d-block mb-1">الحالة</small>
                            <span class="badge bg-{{ $invoice->status_color }} bg-opacity-10 text-{{ $invoice->status_color }}">
                                {{ $invoice->status_label }}
                            </span>
                        </div>
                        <div class="col-sm-6">
                            <small class="text-muted d-block mb-1">الشهر</small>
                            <span class="fw-semibold small">{{ $invoice->period_label }}</span>
                        </div>
                        <div class="col-sm-6">
                            <small class="text-muted d-block mb-1">الفترة</small>
                            <span class="small">
                                {{ $invoice->period_start?->format('Y-m-d') }}
                                →
                                {{ $invoice->period_end?->format('Y-m-d') }}
                            </span>
                        </div>
                        <div class="col-sm-6">
                            <small class="text-muted d-block mb-1">تاريخ الإنشاء</small>
                            <span class="small">{{ $invoice->created_at?->format('Y-m-d h:i A') }}</span>
                        </div>
                        <div class="col-sm-6">
                            <small class="text-muted d-block mb-1">أنشأها</small>
                            <span class="small">{{ $invoice->creator?->name ?? '—' }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Amounts --}}
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header close-day-header">
                    <h6 class="mb-0 fw-bold">
                        <i class="ph-duotone ph-currency-circle-dollar me-1"></i>
                        المبالغ
                    </h6>
                </div>
                <div class="card-body">
                    <div class="summary-row">
                        <span class="text-muted">الإجمالي:</span>
                        <strong>{{ number_format((float) $invoice->total, 2) }} ج.م</strong>
                    </div>
                    <div class="summary-row">
                        <span class="text-muted">المدفوع:</span>
                        <strong class="text-success">{{ number_format((float) $invoice->paid_amount, 2) }} ج.م</strong>
                    </div>
                    <div class="summary-row summary-row-highlight">
                        <span class="text-muted">المتبقي:</span>
                        <strong class="text-danger">{{ number_format((float) $invoice->remaining_amount, 2) }} ج.م</strong>
                    </div>

                    @php
                        $percentage = $invoice->total > 0 ? ($invoice->paid_amount / $invoice->total) * 100 : 0;
                    @endphp
                    <div class="progress mt-3" style="height: 8px;">
                        <div class="progress-bar bg-{{ $invoice->status_color }}"
                             style="width: {{ min(100, $percentage) }}%"></div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Student Info --}}
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header close-day-header">
                    <h6 class="mb-0 fw-bold">
                        <i class="ph-duotone ph-student me-1"></i>
                        الطالب
                    </h6>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div class="student-payment-avatar" style="background: linear-gradient(135deg, #0d6efd, #0a58ca);">
                            {{ mb_substr($invoice->student?->name ?? '?', 0, 1) }}
                        </div>
                        <div>
                            <h6 class="mb-1 fw-bold">{{ $invoice->student?->name }}</h6>
                            <small class="text-muted d-block">
                                <i class="ph-duotone ph-identification-card me-1"></i>
                                {{ $invoice->student?->code }}
                            </small>
                        </div>
                    </div>

                    @if($invoice->student?->email)
                        <div class="small mb-2">
                            <i class="ph-duotone ph-envelope me-1 text-muted"></i>
                            {{ $invoice->student->email }}
                        </div>
                    @endif

                    @if($invoice->student?->branch)
                        <div class="small">
                            <i class="ph-duotone ph-git-branch me-1 text-muted"></i>
                            {{ $invoice->student->branch->name }}
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Invoice Items --}}
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header close-day-header d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-bold">
                        <i class="ph-duotone ph-list-checks me-1"></i>
                        بنود الفاتورة
                    </h6>
                    <span class="badge bg-primary bg-opacity-10 text-primary">
                        {{ $invoice->items->count() }} بند
                    </span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead>
                            <tr>
                                <th class="ps-4">#</th>
                                <th>البيان</th>
                                <th>النوع</th>
                                <th class="text-center">الكمية</th>
                                <th class="text-center">سعر الوحدة</th>
                                <th class="text-center">الإجمالي</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($invoice->items as $index => $item)
                                <tr>
                                    <td class="ps-4">{{ $index + 1 }}</td>
                                    <td>
                                        <span class="fw-semibold small">{{ $item->description }}</span>
                                        @if($item->group)
                                            <small class="text-muted d-block">
                                                <i class="ph-duotone ph-users-three me-1"></i>
                                                {{ $item->group->name }}
                                            </small>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary bg-opacity-10 text-secondary">
                                            {{ $item->pricing_type_label }}
                                        </span>
                                    </td>
                                    <td class="text-center">{{ (int) $item->quantity }}</td>
                                    <td class="text-center">{{ number_format((float) $item->unit_price, 2) }}</td>
                                    <td class="text-center fw-semibold">{{ number_format((float) $item->total, 2) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-muted">لا توجد بنود.</td>
                                </tr>
                            @endforelse
                            </tbody>
                            <tfoot class="table-light">
                            <tr>
                                <td colspan="5" class="text-end fw-bold ps-4">الإجمالي:</td>
                                <td class="text-center fw-bold">{{ number_format((float) $invoice->total, 2) }} ج.م</td>
                            </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- Payments History --}}
        @if($invoice->payments->count() > 0)
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-header close-day-header d-flex justify-content-between align-items-center">
                        <h6 class="mb-0 fw-bold">
                            <i class="ph-duotone ph-clock-counter-clockwise me-1"></i>
                            سجل الدفعات
                        </h6>
                        <span class="badge bg-success bg-opacity-10 text-success">
                            {{ $invoice->payments->count() }} دفعة
                        </span>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead>
                                <tr>
                                    <th class="ps-4">#</th>
                                    <th>رقم الإيصال</th>
                                    <th>التاريخ</th>
                                    <th>الطريقة</th>
                                    <th class="text-center">المبلغ</th>
                                    <th class="text-center">إجراءات</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($invoice->payments as $index => $payment)
                                    <tr>
                                        <td class="ps-4">{{ $index + 1 }}</td>
                                        <td><span class="fw-semibold small">{{ $payment->receipt_number }}</span></td>
                                        <td><small>{{ $payment->paid_at?->format('Y-m-d') }}</small></td>
                                        <td>
                                            <span class="badge bg-primary bg-opacity-10 text-primary">
                                                <i class="ph-duotone {{ $payment->payment_method_icon }} me-1"></i>
                                                {{ $payment->payment_method_label }}
                                            </span>
                                        </td>
                                        <td class="text-center fw-bold text-success">
                                            {{ number_format((float) $payment->amount, 2) }} ج.م
                                        </td>
                                        <td class="text-center">
                                            <button type="button"
                                                    wire:click="$dispatch('show-receipt', { paymentId: {{ $payment->id }} })"
                                                    class="btn btn-sm btn-light border"
                                                    title="عرض الإيصال">
                                                <i class="ph-duotone ph-receipt"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        @endif

    </div>

    {{-- Livewire components shared on the page --}}
    @livewire('teacher-panel.pages.payment.parts.payment-receipt')
</div>
