<div>
    @livewire('teacher-panel.parts.flash-message', [
        'successKey' => 'card-request-success',
        'errorKey' => 'card-request-error',
    ])

    {{-- Header --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
                <div>
                    <h5 class="mb-2">{{ $cardRequest->reference }}</h5>
                    <div class="d-flex flex-wrap gap-2">
                        <span
                            class="badge bg-{{ $cardRequest->status->color() }} {{ $cardRequest->status->color() === 'warning' ? 'text-dark' : 'text-white' }}">
                            <i class="ph-duotone {{ $cardRequest->status->icon() }} me-1"></i>
                            {{ $cardRequest->status->label() }}
                        </span>
                        <span class="badge bg-primary text-white">
                            <i class="ph-duotone ph-identification-card me-1"></i>
                            {{ $cardRequest->cards_count }} كارت
                        </span>
                        <span class="badge bg-success text-white">
                            <i class="ph-duotone ph-currency-circle-dollar me-1"></i>
                            {{ number_format((float) $cardRequest->unit_price, 2) }} ج.م / كارت
                        </span>
                        <span class="badge bg-info text-white">
                            <i class="ph-duotone ph-check-circle me-1"></i>
                            مدفوع: {{ $cardRequest->paid_cards_count }} / {{ $cardRequest->cards_count }}
                        </span>
                        <span class="badge bg-secondary text-white">
                            <i class="ph-duotone ph-calendar me-1"></i>
                            {{ $cardRequest->created_at?->format('Y-m-d h:i A') }}
                        </span>
                    </div>
                </div>

                <a href="{{ route('teacher.card-requests.index') }}" wire:navigate class="btn btn-outline-secondary">
                    <i class="ph-duotone ph-arrow-right me-1"></i>رجوع
                </a>
            </div>

            @if($cardRequest->notes)
                <div class="alert alert-light border mt-3 mb-0 small">
                    <strong>ملاحظات المدرس:</strong> {{ $cardRequest->notes }}
                </div>
            @endif

            @if($cardRequest->admin_notes)
                <div class="alert alert-info border mt-3 mb-0 small">
                    <strong>ملاحظات الأدمن:</strong> {{ $cardRequest->admin_notes }}
                </div>
            @endif
        </div>
    </div>

    {{-- Financial Summary --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="rounded-3 d-flex align-items-center justify-content-center bg-primary-subtle text-primary flex-shrink-0"
                        style="width: 48px; height: 48px;">
                        <i class="ph-duotone ph-receipt fs-4"></i>
                    </div>
                    <div>
                        <div class="fs-4 fw-bold lh-1">{{ number_format((float) $cardRequest->total_price, 2) }}</div>
                        <div class="text-body-secondary small mt-1">الإجمالي</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="rounded-3 d-flex align-items-center justify-content-center bg-success-subtle text-success flex-shrink-0"
                        style="width: 48px; height: 48px;">
                        <i class="ph-duotone ph-check-circle fs-4"></i>
                    </div>
                    <div>
                        <div class="fs-4 fw-bold lh-1 text-success">
                            {{ number_format((float) $cardRequest->paid_amount, 2) }}</div>
                        <div class="text-body-secondary small mt-1">المدفوع</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="rounded-3 d-flex align-items-center justify-content-center bg-danger-subtle text-danger flex-shrink-0"
                        style="width: 48px; height: 48px;">
                        <i class="ph-duotone ph-warning-circle fs-4"></i>
                    </div>
                    <div>
                        <div class="fs-4 fw-bold lh-1 text-danger">
                            {{ number_format($cardRequest->remainingAmount(), 2) }}</div>
                        <div class="text-body-secondary small mt-1">المتبقي</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="rounded-3 d-flex align-items-center justify-content-center bg-info-subtle text-info flex-shrink-0"
                        style="width: 48px; height: 48px;">
                        <i class="ph-duotone ph-users-three fs-4"></i>
                    </div>
                    <div>
                        <div class="fs-4 fw-bold lh-1 text-info">
                            {{ $cardRequest->paid_cards_count }} / {{ $cardRequest->cards_count }}
                        </div>
                        <div class="text-body-secondary small mt-1">كروت مدفوعة</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Students Table with Payment --}}
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-body d-flex justify-content-between align-items-center flex-wrap gap-2">
            <h6 class="mb-0 fw-bold">
                <i class="ph-duotone ph-users-three me-1"></i>
                الطلاب ({{ $cardRequest->items->count() }})
            </h6>

            @if($canMarkPaid && $cardRequest->paid_cards_count < $cardRequest->cards_count)
                <div class="d-flex gap-2">
                    <button type="button" wire:click="selectAllUnpaid" class="btn btn-sm btn-outline-primary">
                        <i class="ph-duotone ph-check-square me-1"></i>تحديد الكل غير المدفوع
                    </button>
                    @if(!empty($paidItemIds))
                        <button type="button" wire:click="markAsPaid" class="btn btn-sm btn-success"
                            wire:loading.attr="disabled">
                            <span wire:loading.remove wire:target="markAsPaid">
                                <i class="ph-duotone ph-check me-1"></i>
                                تسجيل دفع ({{ count($paidItemIds) }})
                            </span>
                            <span wire:loading wire:target="markAsPaid">
                                <span class="spinner-border spinner-border-sm me-1"></span>جاري...
                            </span>
                        </button>
                        <button type="button" wire:click="clearSelection" class="btn btn-sm btn-outline-secondary">
                            إلغاء
                        </button>
                    @endif
                </div>
            @endif
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            @if($canMarkPaid)
                                <th class="ps-4" style="width: 40px;"></th>
                            @endif
                            <th class="{{ !$canMarkPaid ? 'ps-4' : '' }}">#</th>
                            <th>الطالب</th>
                            <th>الكود</th>
                            <th>المرحلة</th>
                            <th>الفرع</th>
                            <th>المجموعة</th>
                            <th class="text-center">سعر الكارت</th>
                            <th class="text-center">حالة الدفع</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($cardRequest->items as $item)
                            <tr wire:key="item-{{ $item->id }}"
                                class="{{ in_array($item->id, $paidItemIds) ? 'table-primary' : '' }}">
                                @if($canMarkPaid)
                                    <td class="ps-4">
                                        @if(!$item->is_paid)
                                            <input type="checkbox" class="form-check-input" wire:click="togglePaid({{ $item->id }})"
                                                @if(in_array($item->id, $paidItemIds)) checked @endif>
                                        @else
                                            <i class="ph-duotone ph-check-circle text-success fs-5"></i>
                                        @endif
                                    </td>
                                @endif
                                <td class="{{ !$canMarkPaid ? 'ps-4' : '' }} text-body-secondary fw-semibold">
                                    {{ $loop->iteration }}
                                </td>
                                <td><span class="fw-semibold">{{ $item->student_name }}</span></td>
                                <td><small class="text-body-secondary">{{ $item->student_code ?? '—' }}</small></td>
                                <td>
                                    @if($item->grade)
                                        <span class="badge bg-info text-white">
                                            {{ \App\Enums\Teacher\GradeEnum::tryFrom($item->grade)?->label() ?? $item->grade }}
                                        </span>
                                    @else
                                        <span class="text-body-secondary small">—</span>
                                    @endif
                                </td>
                                <td><small>{{ $item->branch?->name ?? '—' }}</small></td>
                                <td><small>{{ $item->group?->name ?? '—' }}</small></td>
                                <td class="text-center fw-bold">
                                    {{ number_format((float) $item->card_price, 2) }} ج.م
                                </td>
                                <td class="text-center">
                                    @if($item->is_paid)
                                        <span class="badge bg-success text-white">
                                            <i class="ph-duotone ph-check-circle me-1"></i>
                                            مدفوع {{ $item->paid_at?->format('Y-m-d') }}
                                        </span>
                                    @else
                                        <span class="badge bg-warning text-dark">
                                            <i class="ph-duotone ph-clock me-1"></i>
                                            لم يُدفع
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
