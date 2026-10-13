<div>
    @livewire('teacher-panel.parts.flash-message', [
        'successKey' => 'revenue-success',
        'errorKey' => 'revenue-error',
    ])

    {{-- Month Selector --}}
    @livewire('teacher-panel.pages.payment.parts.month-selector', ['context' => 'revenues'])


    {{-- Stats by Source --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-body d-flex justify-content-between align-items-center">
            <h6 class="mb-0 fw-bold">
                <i class="ph-duotone ph-chart-pie me-1"></i>
                الإيرادات حسب المصدر
            </h6>
            <span class="badge bg-success text-white fs-6">
                الإجمالي: {{ number_format($grandTotal, 2) }} ج.م
            </span>
        </div>
        <div class="card-body">
            <div class="row g-3">
                @foreach($sources as $sourceValue => $sourceLabel)
                    @php
                        $sourceEnum = \App\Enums\Teacher\RevenueSourceEnum::tryFrom($sourceValue);
                        $total = (float) ($totalsBySource[$sourceValue]->total ?? 0);
                        $count = (int) ($totalsBySource[$sourceValue]->count ?? 0);
                        $percentage = $grandTotal > 0 ? ($total / $grandTotal) * 100 : 0;
                    @endphp
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
                                    {{ number_format($total, 2) }} <small class="fs-6">ج.م</small>
                                </div>

                                <div class="progress" style="height: 4px;">
                                    <div class="progress-bar bg-{{ $sourceEnum->color() }}"
                                        style="width: {{ $percentage }}%"></div>
                                </div>
                                <small class="text-body-secondary">{{ number_format($percentage, 1) }}%</small>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Filters --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <div class="row g-2">
                <div class="col-md-4">
                    <input type="text" wire:model.live.debounce.400ms="search" class="form-control"
                        placeholder="ابحث برقم الإيراد أو الوصف...">
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
                        <th>الطالب</th>
                        <th>الفرع</th>
                        <th>القائم بالعملية</th>
                        <th class="text-center">المبلغ</th>
                        <th>التاريخ</th>
                        @if($canDelete)
                            <th class="text-center">إجراءات</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @forelse($revenues as $revenue)
                        <tr wire:key="rev-{{ $revenue->id }}">
                            <td class="ps-4 text-body-secondary fw-semibold">
                                {{ $revenues->firstItem() + $loop->index }}
                            </td>
                            <td><span class="fw-semibold">{{ $revenue->reference }}</span></td>
                            <td>
                                <span class="badge bg-{{ $revenue->source->color() }} {{ $revenue->source->textColor() }}">
                                    <i class="ph-duotone {{ $revenue->source->icon() }} me-1"></i>
                                    {{ $revenue->source->label() }}
                                </span>
                            </td>
                            <td>
                                @if($revenue->student)
                                    <small>{{ $revenue->student->name }}</small>
                                @else
                                    <span class="text-body-secondary">—</span>
                                @endif
                            </td>
                            <td>
                                @if($revenue->branch)
                                    <small>{{ $revenue->branch->name }}</small>
                                @else
                                    <span class="text-body-secondary">—</span>
                                @endif
                            </td>
                            <td>
                                @if($revenue->recordedBy)
                                    <small>{{ $revenue->recordedBy->name }}</small>
                                @else
                                    <span class="text-body-secondary">—</span>
                                @endif
                            </td>
                            <td class="text-center fw-bold text-success">
                                {{ number_format($revenue->amount, 2) }} ج.م
                            </td>
                            <td>
                                <small class="text-body-secondary">
                                    {{ $revenue->occurred_at?->format('Y-m-d h:i A') }}
                                </small>
                            </td>
                            @if($canDelete)
                                <td>
                                    <div class="d-flex align-items-center justify-content-center gap-2">
                                        @if($deleteId === $revenue->id)
                                            <button type="button" wire:click="delete"
                                                class="border-0 bg-transparent p-0 text-success">
                                                <span wire:loading.remove wire:target="delete">
                                                    <i class="ph-duotone ph-check-circle fs-4"></i>
                                                </span>
                                                <span wire:loading wire:target="delete"
                                                    class="spinner-border spinner-border-sm"></span>
                                            </button>
                                            <button type="button" wire:click="cancelDelete"
                                                class="border-0 bg-transparent p-0 text-secondary">
                                                <i class="ph-duotone ph-x-circle fs-4"></i>
                                            </button>
                                        @else
                                            <button type="button" wire:click="confirmDelete({{ $revenue->id }})"
                                                class="border-0 bg-transparent p-0 text-danger" title="حذف">
                                                <i class="ph-duotone ph-trash fs-4"></i>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            @endif
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ $canDelete ? 9 : 8 }}" class="text-center py-5">
                                <i class="ph-duotone ph-currency-circle-dollar fs-1 d-block mb-2 text-body-secondary"></i>
                                <h6 class="text-body-secondary mb-1">لا توجد إيرادات</h6>
                                <small class="text-body-secondary">لم يتم تسجيل أي إيرادات في هذا الشهر.</small>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($revenues->hasPages())
            <div class="card-footer bg-transparent border-top">
                {{ $revenues->links() }}
            </div>
        @endif
    </div>
</div>
