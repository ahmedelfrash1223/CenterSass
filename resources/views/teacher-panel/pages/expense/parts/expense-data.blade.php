<div>
    @livewire('teacher-panel.parts.flash-message', [
        'successKey' => 'expense-success',
        'errorKey' => 'expense-error',
    ])

    {{-- Month Selector --}}
    @livewire('teacher-panel.pages.payment.parts.month-selector', ['context' => 'expenses'])

    {{-- Totals by Source --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-body d-flex justify-content-between align-items-center">
            <h6 class="mb-0 fw-bold">
                <i class="ph-duotone ph-chart-pie me-1"></i>
                المصروفات حسب المصدر
            </h6>
            <span class="badge bg-danger text-white fs-6">
                الإجمالي: {{ number_format($grandTotal, 2) }} ج.م
            </span>
        </div>
        <div class="card-body">
            <div class="row g-3">
                @php $hasAny = false; @endphp
                @foreach($sources as $sourceValue => $sourceLabel)
                    @php
                        $sourceEnum = \App\Enums\Teacher\ExpenseSourceEnum::tryFrom($sourceValue);
                        $total = (float) ($totalsBySource[$sourceValue]->total ?? 0);
                        $count = (int) ($totalsBySource[$sourceValue]->count ?? 0);
                        $percentage = $grandTotal > 0 ? ($total / $grandTotal) * 100 : 0;
                        if ($total > 0 || $count > 0) {
                            $hasAny = true;
                        }
                    @endphp
                    @if($total > 0 || $count > 0)
                        <div class="col-6 col-lg-3">
                            <div class="card border h-100 bg-body-secondary">
                                <div class="card-body">
                                    <div class="d-flex align-items-center gap-2 mb-2">
                                        <div class="rounded-3 d-flex align-items-center justify-content-center bg-{{ $sourceEnum->color() }}-subtle text-{{ $sourceEnum->color() }} flex-shrink-0"
                                            style="width: 36px; height: 36px;">
                                            <i class="ph-duotone {{ $sourceEnum->icon() }} fs-5"></i>
                                        </div>
                                        <div class="flex-grow-1">
                                            <div class="small fw-semibold">{{ $sourceLabel }}</div>
                                            <small class="text-body-secondary">{{ $count }} عملية</small>
                                        </div>
                                    </div>

                                    <div class="fs-5 fw-bold text-{{ $sourceEnum->color() }} mb-1">
                                        {{ number_format($total, 2) }}
                                    </div>

                                    <div class="progress" style="height: 4px;">
                                        <div class="progress-bar bg-{{ $sourceEnum->color() }}"
                                            style="width: {{ $percentage }}%"></div>
                                    </div>
                                    <small class="text-body-secondary">{{ number_format($percentage, 1) }}%</small>
                                </div>
                            </div>
                        </div>
                    @endif
                @endforeach

                @if(!$hasAny)
                    <div class="col-12">
                        <div class="text-center py-3 text-body-secondary">
                            <i class="ph-duotone ph-info fs-3 d-block mb-2"></i>
                            <small>لا توجد مصروفات هذا الشهر</small>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Filters --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <div class="row g-2">
                <div class="col-md-4">
                    <input type="text" wire:model.live.debounce.400ms="search" class="form-control"
                        placeholder="ابحث برقم المصروف أو الوصف...">
                </div>
                <div class="col-md-3">
                    <select wire:model.live="sourceFilter" class="form-select">
                        <option value="">كل المصادر</option>
                        @foreach($sources as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <select wire:model.live="branchFilter" class="form-select">
                        <option value="">كل الفروع</option>
                        @foreach($branches as $branch)
                            <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="button" class="btn btn-light border w-100" wire:click="clearFilters">
                        <i class="ph-duotone ph-funnel-simple me-1"></i>تفريغ
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Table --}}
    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th class="ps-4">#</th>
                        <th>الرقم</th>
                        <th>المصدر</th>
                        <th>المدرس المستفيد</th>
                        <th>الفرع</th>
                        <th>القائم بالعملية</th>
                        <th class="text-center">المبلغ</th>
                        <th>التاريخ</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($expenses as $expense)
                        <tr wire:key="exp-{{ $expense->id }}">
                            <td class="ps-4 text-body-secondary fw-semibold">
                                {{ $expenses->firstItem() + $loop->index }}
                            </td>
                            <td><span class="fw-semibold">{{ $expense->reference }}</span></td>
                            <td>
                                <span class="badge bg-{{ $expense->source->color() }} {{ $expense->source->textColor() }}">
                                    <i class="ph-duotone {{ $expense->source->icon() }} me-1"></i>
                                    {{ $expense->source->label() }}
                                </span>
                            </td>
                            <td>
                                @if($expense->teacherPayee)
                                    <small>{{ $expense->teacherPayee->name }}</small>
                                @else
                                    <span class="text-body-secondary">—</span>
                                @endif
                            </td>
                            <td>
                                @if($expense->branch)
                                    <small>{{ $expense->branch->name }}</small>
                                @else
                                    <span class="text-body-secondary">—</span>
                                @endif
                            </td>
                            <td>
                                @if($expense->recordedBy)
                                    <small>{{ $expense->recordedBy->name }}</small>
                                @else
                                    <span class="text-body-secondary">—</span>
                                @endif
                            </td>
                            <td class="text-center fw-bold text-danger">
                                {{ number_format($expense->amount, 2) }} ج.م
                            </td>
                            <td>
                                <small class="text-body-secondary">
                                    {{ $expense->occurred_at?->format('Y-m-d h:i A') }}
                                </small>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-5">
                                <i class="ph-duotone ph-arrow-circle-down fs-1 d-block mb-2 text-body-secondary"></i>
                                <h6 class="text-body-secondary mb-1">لا توجد مصروفات</h6>
                                <small class="text-body-secondary">لم يتم تسجيل أي مصروفات في هذا الشهر.</small>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($expenses->hasPages())
            <div class="card-footer bg-transparent border-top">
                {{ $expenses->links() }}
            </div>
        @endif
    </div>
</div>
