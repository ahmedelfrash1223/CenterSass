<div>
    @livewire('teacher-panel.parts.flash-message', [
        'successKey' => 'expense-success',
        'errorKey' => 'expense-error',
    ])

    {{-- Month Selector --}}
    @livewire('teacher-panel.pages.payment.parts.month-selector', ['context' => 'expenses'])

    {{-- Header with Add button --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
        <p class="text-body-secondary mb-0 small">إدارة المصروفات الشهرية</p>
        <button type="button" wire:click="toggleOtherForm" class="btn btn-primary">
            <i class="ph-duotone {{ $showOtherForm ? 'ph-x-circle' : 'ph-plus-circle' }} me-1"></i>
            {{ $showOtherForm ? 'إغلاق الفورم' : 'إضافة مصروف آخر' }}
        </button>
    </div>

    {{-- Inline Form: مصروف آخر --}}
    @if($showOtherForm)
        <div class="card border-0 shadow-sm mb-4" style="border-inline-start: 4px solid var(--bs-primary) !important;">
            <div class="card-header bg-body">
                <h6 class="mb-0 fw-bold">
                    <i class="ph-duotone ph-plus-circle me-1"></i>مصروف آخر
                </h6>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label small fw-semibold">
                            نوع المصروف <span class="text-danger">*</span>
                        </label>
                        <select wire:model="otherSource" class="form-select @error('otherSource') is-invalid @enderror">
                            <option value="">— اختر النوع —</option>
                            @foreach($otherSources as $value => $label)
                                <option value="{{ $value }}">{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('otherSource')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label small fw-semibold">
                            المبلغ <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <input type="number" step="0.01" min="0" wire:model="otherAmount"
                                class="form-control @error('otherAmount') is-invalid @enderror" placeholder="0.00">
                            <span class="input-group-text">ج.م</span>
                            @error('otherAmount')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label small fw-semibold">الفرع</label>
                        <select wire:model="otherBranchId" class="form-select @error('otherBranchId') is-invalid @enderror">
                            <option value="">— بدون فرع —</option>
                            @foreach($branches as $branch)
                                <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                            @endforeach
                        </select>
                        @error('otherBranchId')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12">
                        <label class="form-label small fw-semibold">
                            السبب / الملاحظات <span class="text-danger">*</span>
                        </label>
                        <textarea wire:model="otherReason" rows="2"
                            class="form-control @error('otherReason') is-invalid @enderror"
                            placeholder="اكتب سبب المصروف أو أي ملاحظات..."></textarea>
                        @error('otherReason')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label small fw-semibold">وصف إضافي (اختياري)</label>
                        <input type="text" wire:model="otherDescription"
                            class="form-control @error('otherDescription') is-invalid @enderror" placeholder="وصف مختصر...">
                        @error('otherDescription')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label small fw-semibold">
                            تاريخ المصروف <span class="text-danger">*</span>
                        </label>
                        <input type="datetime-local" wire:model="otherOccurredAt"
                            class="form-control @error('otherOccurredAt') is-invalid @enderror">
                        @error('otherOccurredAt')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2 mt-3 pt-3 border-top">
                    <button type="button" wire:click="saveOtherExpense" wire:loading.attr="disabled"
                        wire:target="saveOtherExpense" class="btn btn-primary">
                        <span wire:loading.remove wire:target="saveOtherExpense">
                            <i class="ph-duotone ph-paper-plane-tilt me-1"></i>حفظ المصروف
                        </span>
                        <span wire:loading wire:target="saveOtherExpense">
                            <span class="spinner-border spinner-border-sm me-1"></span>جاري الحفظ...
                        </span>
                    </button>
                    <button type="button" wire:click="toggleOtherForm" class="btn btn-outline-secondary">
                        إلغاء
                    </button>
                </div>
            </div>
        </div>
    @endif

    {{-- Totals by Source --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-body d-flex justify-content-between align-items-center">
            <h6 class="mb-0 fw-bold">
                <i class="ph-duotone ph-chart-pie me-1"></i>المصروفات حسب المصدر
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
                        placeholder="ابحث برقم المصروف أو الوصف أو السبب...">
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

    {{-- ═════════ TABLE 1: مصروفات أخرى ═════════ --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-body d-flex justify-content-between align-items-center">
            <h6 class="mb-0 fw-bold">
                <i class="ph-duotone ph-wallet me-1"></i>مصروفات أخرى
                <span class="badge bg-secondary text-white ms-2">{{ $otherExpenses->total() }}</span>
            </h6>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th class="ps-4">#</th>
                        <th>الرقم</th>
                        <th>المصدر</th>
                        <th>السبب / الملاحظات</th>
                        <th>الفرع</th>
                        <th>القائم بالعملية</th>
                        <th class="text-center">المبلغ</th>
                        <th>التاريخ</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($otherExpenses as $expense)
                        <tr wire:key="other-{{ $expense->id }}">
                            <td class="ps-4 text-body-secondary fw-semibold">
                                {{ $otherExpenses->firstItem() + $loop->index }}
                            </td>
                            <td><span class="fw-semibold">{{ $expense->reference }}</span></td>
                            <td>
                                <span class="badge bg-{{ $expense->source->color() }} {{ $expense->source->textColor() }}">
                                    <i class="ph-duotone {{ $expense->source->icon() }} me-1"></i>
                                    {{ $expense->source->label() }}
                                </span>
                            </td>
                            <td>
                                @if($expense->reason)
                                    <small class="text-body-emphasis">{{ $expense->reason }}</small>
                                @else
                                    <span class="text-body-secondary small">—</span>
                                @endif
                                @if($expense->description)
                                    <br><small class="text-body-secondary">{{ $expense->description }}</small>
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
                                <i class="ph-duotone ph-wallet fs-1 d-block mb-2 text-body-secondary"></i>
                                <h6 class="text-body-secondary mb-1">لا توجد مصروفات أخرى</h6>
                                <small class="text-body-secondary">ابدأ بإضافة مصروف جديد من الزرار أعلى الصفحة.</small>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($otherExpenses->hasPages())
            <div class="card-footer bg-transparent border-top">
                {{ $otherExpenses->links() }}
            </div>
        @endif
    </div>

    {{-- ═════════ TABLE 2: مصروفات المدرسين ═════════ --}}
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-body d-flex justify-content-between align-items-center">
            <h6 class="mb-0 fw-bold">
                <i class="ph-duotone ph-user-circle me-1"></i>مصروفات المدرسين
                <span class="badge bg-secondary text-white ms-2">{{ $teacherExpenses->total() }}</span>
            </h6>
        </div>
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
                    @forelse($teacherExpenses as $expense)
                        <tr wire:key="teacher-{{ $expense->id }}">
                            <td class="ps-4 text-body-secondary fw-semibold">
                                {{ $teacherExpenses->firstItem() + $loop->index }}
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
                                <i class="ph-duotone ph-user-circle fs-1 d-block mb-2 text-body-secondary"></i>
                                <h6 class="text-body-secondary mb-1">لا توجد مصروفات مدرسين</h6>
                                <small class="text-body-secondary">لم يتم تسجيل رواتب أو بونص في هذا الشهر.</small>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($teacherExpenses->hasPages())
            <div class="card-footer bg-transparent border-top">
                {{ $teacherExpenses->links() }}
            </div>
        @endif
    </div>
</div>
