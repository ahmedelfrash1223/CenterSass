<div>
    @livewire('teacher-panel.parts.flash-message', [
        'successKey' => 'monthly-closing-success',
        'errorKey' => 'monthly-closing-error',
    ])

    {{-- Header Card --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <div class="d-flex flex-wrap justify-content-between align-items-start gap-3">
                <div>
                    <h5 class="mb-2">{{ $closing->reference }}</h5>
                    <div class="d-flex flex-wrap gap-2">
                        @if($closing->isClosed())
                            <span class="badge bg-success text-white">
                                <i class="ph-duotone ph-lock me-1"></i>مقفل
                            </span>
                        @else
                            <span class="badge bg-warning text-dark">
                                <i class="ph-duotone ph-lock-open me-1"></i>مفتوح
                            </span>
                        @endif
                        <span class="badge bg-secondary text-white">
                            <i class="ph-duotone ph-calendar me-1"></i>{{ $closing->month }}
                        </span>
                        @if($closing->branch)
                            <span class="badge bg-info text-white">
                                <i class="ph-duotone ph-buildings me-1"></i>{{ $closing->branch->name }}
                            </span>
                        @endif
                        <span class="badge bg-primary text-white">
                            <i class="ph-duotone ph-clock me-1"></i>
                            {{ $closing->closed_at?->format('Y-m-d h:i A') }}
                        </span>
                    </div>
                    <div class="mt-3">
                        <div><strong>بواسطة:</strong> {{ $closing->closedBy?->name ?? '—' }}</div>
                        @if($closing->notes)
                            <div class="mt-1"><strong>ملاحظات:</strong> {{ $closing->notes }}</div>
                        @endif
                    </div>
                </div>

                <div class="d-flex flex-wrap gap-2">
                    @if($canExport)
                        <a href="{{ route('teacher.monthly-closing.revenue-pdf', $closing->id) }}"
                           target="_blank" class="btn btn-success">
                            <i class="ph-duotone ph-file-pdf me-1"></i>وارد PDF
                        </a>
                        <a href="{{ route('teacher.monthly-closing.expense-pdf', $closing->id) }}"
                           target="_blank" class="btn btn-danger">
                            <i class="ph-duotone ph-file-pdf me-1"></i>صادر PDF
                        </a>
                        <a href="{{ route('teacher.monthly-closing.summary-pdf', $closing->id) }}"
                           target="_blank" class="btn btn-primary">
                            <i class="ph-duotone ph-file-pdf me-1"></i>خلاصة PDF
                        </a>
                    @endif

                    @if($canManage && $closing->isClosed())
                        <button type="button" wire:click="askConfirm('reopen')" class="btn btn-warning">
                            <i class="ph-duotone ph-lock-open me-1"></i>إلغاء التقفيل
                        </button>
                    @endif

                    @if($canDelete)
                        <button type="button" wire:click="askConfirm('delete')" class="btn btn-outline-danger">
                            <i class="ph-duotone ph-trash me-1"></i>حذف
                        </button>
                    @endif

                    <a href="{{ route('teacher.monthly-closing.index') }}" wire:navigate
                       class="btn btn-outline-secondary">
                        <i class="ph-duotone ph-arrow-right me-1"></i>رجوع
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- Confirmation Dialog --}}
    @if($confirm)
        <div class="card border-0 shadow-sm mb-4" style="border-inline-start: 4px solid var(--bs-warning) !important;">
            <div class="card-body">
                <div class="d-flex align-items-start gap-3">
                    <div class="rounded-circle d-flex align-items-center justify-content-center bg-warning-subtle text-warning flex-shrink-0"
                         style="width: 48px; height: 48px;">
                        <i class="ph-duotone {{ $confirm['icon'] }} fs-4"></i>
                    </div>
                    <div class="flex-grow-1">
                        <h6 class="mb-1">{{ $confirm['title'] }}</h6>
                        <p class="text-body-secondary small mb-3">{{ $confirm['description'] }}</p>
                        <div class="d-flex flex-wrap gap-2">
                            <button type="button" wire:click="confirmAction" wire:loading.attr="disabled"
                                    wire:target="confirmAction" class="btn {{ $confirm['btn_class'] }}">
                                <span wire:loading.remove wire:target="confirmAction">
                                    <i class="ph-duotone ph-check me-1"></i>{{ $confirm['btn_label'] }}
                                </span>
                                <span wire:loading wire:target="confirmAction">
                                    <span class="spinner-border spinner-border-sm me-1"></span>جاري التنفيذ...
                                </span>
                            </button>
                            <button type="button" wire:click="cancelConfirm" class="btn btn-outline-secondary"
                                    wire:loading.attr="disabled" wire:target="confirmAction">
                                <i class="ph-duotone ph-x me-1"></i>إلغاء
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Financial Summary --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="rounded-3 d-flex align-items-center justify-content-center bg-success-subtle text-success flex-shrink-0"
                         style="width: 48px; height: 48px;">
                        <i class="ph-duotone ph-arrow-down-left fs-4"></i>
                    </div>
                    <div>
                        <div class="fs-4 fw-bold lh-1 text-success">
                            {{ number_format($closing->total_revenue, 2) }}
                        </div>
                        <div class="text-body-secondary small mt-1">إجمالي الوارد</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="rounded-3 d-flex align-items-center justify-content-center bg-danger-subtle text-danger flex-shrink-0"
                         style="width: 48px; height: 48px;">
                        <i class="ph-duotone ph-arrow-up-right fs-4"></i>
                    </div>
                    <div>
                        <div class="fs-4 fw-bold lh-1 text-danger">
                            {{ number_format($closing->total_expense, 2) }}
                        </div>
                        <div class="text-body-secondary small mt-1">إجمالي الصادر</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="rounded-3 d-flex align-items-center justify-content-center {{ $closing->net_amount >= 0 ? 'bg-primary-subtle text-primary' : 'bg-warning-subtle text-warning' }} flex-shrink-0"
                         style="width: 48px; height: 48px;">
                        <i class="ph-duotone ph-wallet fs-4"></i>
                    </div>
                    <div>
                        <div class="fs-4 fw-bold lh-1 {{ $closing->net_amount >= 0 ? 'text-primary' : 'text-warning' }}">
                            {{ number_format($closing->net_amount, 2) }}
                        </div>
                        <div class="text-body-secondary small mt-1">الصافي</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="rounded-3 d-flex align-items-center justify-content-center bg-info-subtle text-info flex-shrink-0"
                         style="width: 48px; height: 48px;">
                        <i class="ph-duotone ph-list-checks fs-4"></i>
                    </div>
                    <div>
                        <div class="fs-4 fw-bold lh-1 text-info">
                            {{ $closing->revenues_count + $closing->expenses_count }}
                        </div>
                        <div class="text-body-secondary small mt-1">عدد البنود</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Revenue Items --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-body d-flex justify-content-between align-items-center">
            <h6 class="mb-0 fw-bold">
                <i class="ph-duotone ph-arrow-down-left text-success me-1"></i>
                بنود الوارد ({{ $revenueItems->count() }})
            </h6>
            <span class="badge bg-success text-white">
                الإجمالي: {{ number_format($closing->total_revenue, 2) }} ج.م
            </span>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                <tr>
                    <th class="ps-4">#</th>
                    <th>الرقم</th>
                    <th>المصدر</th>
                    <th>الوصف</th>
                    <th class="text-center">المبلغ</th>
                    <th>التاريخ</th>
                </tr>
                </thead>
                <tbody>
                @forelse($revenueItems as $i => $item)
                    @php
                        $enum = \App\Enums\Teacher\RevenueSourceEnum::tryFrom($item->source_type);
                    @endphp
                    <tr wire:key="rev-{{ $item->id }}">
                        <td class="ps-4 text-body-secondary fw-semibold">{{ $i + 1 }}</td>
                        <td><span class="fw-semibold">{{ $item->reference }}</span></td>
                        <td>
                                <span class="badge bg-{{ $enum?->color() ?? 'secondary' }} {{ $enum?->textColor() ?? 'text-white' }}">
                                    <i class="ph-duotone {{ $enum?->icon() ?? 'ph-currency-circle-dollar' }} me-1"></i>
                                    {{ $enum?->label() ?? $item->source_type }}
                                </span>
                        </td>
                        <td><small>{{ $item->description ?: '—' }}</small></td>
                        <td class="text-center fw-bold text-success">
                            {{ number_format($item->amount, 2) }} ج.م
                        </td>
                        <td>
                            <small class="text-body-secondary">
                                {{ $item->occurred_at?->format('Y-m-d') }}
                            </small>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-4 text-body-secondary">
                            <i class="ph-duotone ph-info fs-3 d-block mb-2"></i>
                            <small>لا توجد بنود وارد</small>
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Expense Items --}}
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-body d-flex justify-content-between align-items-center">
            <h6 class="mb-0 fw-bold">
                <i class="ph-duotone ph-arrow-up-right text-danger me-1"></i>
                بنود الصادر ({{ $expenseItems->count() }})
            </h6>
            <span class="badge bg-danger text-white">
                الإجمالي: {{ number_format($closing->total_expense, 2) }} ج.م
            </span>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                <tr>
                    <th class="ps-4">#</th>
                    <th>الرقم</th>
                    <th>المصدر</th>
                    <th>الوصف / السبب</th>
                    <th class="text-center">المبلغ</th>
                    <th>التاريخ</th>
                </tr>
                </thead>
                <tbody>
                @forelse($expenseItems as $i => $item)
                    @php
                        $enum = \App\Enums\Teacher\ExpenseSourceEnum::tryFrom($item->source_type);
                    @endphp
                    <tr wire:key="exp-{{ $item->id }}">
                        <td class="ps-4 text-body-secondary fw-semibold">{{ $i + 1 }}</td>
                        <td><span class="fw-semibold">{{ $item->reference }}</span></td>
                        <td>
                                <span class="badge bg-{{ $enum?->color() ?? 'secondary' }} {{ $enum?->textColor() ?? 'text-white' }}">
                                    <i class="ph-duotone {{ $enum?->icon() ?? 'ph-wallet' }} me-1"></i>
                                    {{ $enum?->label() ?? $item->source_type }}
                                </span>
                        </td>
                        <td><small>{{ $item->description ?: '—' }}</small></td>
                        <td class="text-center fw-bold text-danger">
                            {{ number_format($item->amount, 2) }} ج.م
                        </td>
                        <td>
                            <small class="text-body-secondary">
                                {{ $item->occurred_at?->format('Y-m-d') }}
                            </small>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-4 text-body-secondary">
                            <i class="ph-duotone ph-info fs-3 d-block mb-2"></i>
                            <small>لا توجد بنود صادر</small>
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
