<div>
    @livewire('teacher-panel.parts.flash-message', [
        'successKey' => 'monthly-closing-success',
        'errorKey' => 'monthly-closing-error',
    ])

    @livewire('teacher-panel.pages.payment.parts.month-selector', ['context' => 'monthly-closing'])

    {{-- Header with button --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
        <p class="text-body-secondary mb-0 small">تقفيل شامل للوارد والصادر لكل شهر</p>
        <button type="button" wire:click="toggleCloseForm" class="btn btn-primary">
            <i class="ph-duotone {{ $showCloseForm ? 'ph-x-circle' : 'ph-lock' }} me-1"></i>
            {{ $showCloseForm ? 'إغلاق الفورم' : 'تقفيل شهر جديد' }}
        </button>
    </div>

    {{-- Inline Form --}}
    @if($showCloseForm)
        <div class="card border-0 shadow-sm mb-4" style="border-inline-start: 4px solid var(--bs-primary) !important;">
            <div class="card-header bg-body">
                <h6 class="mb-0 fw-bold">
                    <i class="ph-duotone ph-lock me-1"></i>تقفيل شهر جديد
                </h6>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label small fw-semibold">
                            الشهر <span class="text-danger">*</span>
                        </label>
                        <input type="month" wire:model="closeMonth"
                               class="form-control @error('closeMonth') is-invalid @enderror">
                        @error('closeMonth')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label small fw-semibold">الفرع</label>
                        <select wire:model="closeBranchId"
                                class="form-select @error('closeBranchId') is-invalid @enderror">
                            <option value="">— كل الفروع —</option>
                            @foreach($branches as $branch)
                                <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                            @endforeach
                        </select>
                        @error('closeBranchId')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label small fw-semibold">ملاحظات</label>
                        <input type="text" wire:model="closeNotes"
                               class="form-control @error('closeNotes') is-invalid @enderror"
                               placeholder="ملاحظات اختيارية...">
                        @error('closeNotes')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="alert alert-warning d-flex align-items-start gap-2 mt-3 mb-0 small">
                    <i class="ph-duotone ph-warning-circle fs-5"></i>
                    <div>
                        <strong>تنبيه:</strong> تقفيل الشهر بيجمع كل الإيرادات والمصروفات في الفترة المحددة،
                        وبيمنع تعديلها لاحقاً (لحد ما تعيد فتحها). راجع البيانات قبل التأكيد.
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2 mt-3 pt-3 border-top">
                    <button type="button" wire:click="closeMonth" wire:loading.attr="disabled"
                            wire:target="closeMonth" class="btn btn-primary">
                        <span wire:loading.remove wire:target="closeMonth">
                            <i class="ph-duotone ph-lock me-1"></i>تقفيل الشهر
                        </span>
                        <span wire:loading wire:target="closeMonth">
                            <span class="spinner-border spinner-border-sm me-1"></span>جاري التنفيذ...
                        </span>
                    </button>
                    <button type="button" wire:click="toggleCloseForm" class="btn btn-outline-secondary">
                        إلغاء
                    </button>
                </div>
            </div>
        </div>
    @endif

    {{-- Filters --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <div class="row g-2">
                <div class="col-md-5">
                    <input type="text" wire:model.live.debounce.400ms="search" class="form-control"
                           placeholder="ابحث برقم التقفيل...">
                </div>
                <div class="col-md-4">
                    <select wire:model.live="branchFilter" class="form-select">
                        <option value="">كل الفروع</option>
                        @foreach($branches as $branch)
                            <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
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
                    <th>الفرع</th>
                    <th>الشهر</th>
                    <th class="text-center">الإيرادات</th>
                    <th class="text-center">المصروفات</th>
                    <th class="text-center">الصافي</th>
                    <th>الحالة</th>
                    <th class="text-center">إجراءات</th>
                </tr>
                </thead>
                <tbody>
                @forelse($closings as $closing)
                    <tr wire:key="closing-{{ $closing->id }}">
                        <td class="ps-4 text-body-secondary fw-semibold">
                            {{ $closings->firstItem() + $loop->index }}
                        </td>
                        <td><span class="fw-semibold">{{ $closing->reference }}</span></td>
                        <td>
                            @if($closing->branch)
                                <small>{{ $closing->branch->name }}</small>
                            @else
                                <span class="text-body-secondary">—</span>
                            @endif
                        </td>
                        <td>
                            <small>{{ $closing->month }}</small>
                        </td>
                        <td class="text-center fw-bold text-success">
                            {{ number_format($closing->total_revenue, 2) }}
                        </td>
                        <td class="text-center fw-bold text-danger">
                            {{ number_format($closing->total_expense, 2) }}
                        </td>
                        <td class="text-center fw-bold {{ $closing->net_amount >= 0 ? 'text-success' : 'text-danger' }}">
                            {{ number_format($closing->net_amount, 2) }}
                        </td>
                        <td>
                            @if($closing->isClosed())
                                <span class="badge bg-success text-white">
                                        <i class="ph-duotone ph-lock me-1"></i>مقفل
                                    </span>
                            @else
                                <span class="badge bg-warning text-dark">
                                        <i class="ph-duotone ph-lock-open me-1"></i>مفتوح
                                    </span>
                            @endif
                        </td>
                        <td>
                            <div class="d-flex align-items-center justify-content-center gap-2">
                                <a href="{{ route('teacher.monthly-closing.revenue-pdf', $closing->id) }}"
                                   target="_blank" class="text-success" title="وارد PDF">
                                    <i class="ph-duotone ph-file-arrow-down fs-4"></i>
                                </a>
                                <a href="{{ route('teacher.monthly-closing.expense-pdf', $closing->id) }}"
                                   target="_blank" class="text-danger" title="صادر PDF">
                                    <i class="ph-duotone ph-file-arrow-down fs-4"></i>
                                </a>
                                <a href="{{ route('teacher.monthly-closing.summary-pdf', $closing->id) }}"
                                   target="_blank" class="text-primary" title="خلاصة PDF">
                                    <i class="ph-duotone ph-file-pdf fs-4"></i>
                                </a>

                                @if($confirmingReopenId === $closing->id)
                                    <button type="button" wire:click="reopen" class="border-0 bg-transparent p-0 text-success" title="تأكيد">
                                        <i class="ph-duotone ph-check-circle fs-4"></i>
                                    </button>
                                    <button type="button" wire:click="cancelReopen" class="border-0 bg-transparent p-0 text-secondary">
                                        <i class="ph-duotone ph-x-circle fs-4"></i>
                                    </button>
                                @elseif($confirmingDeleteId === $closing->id)
                                    <button type="button" wire:click="delete" class="border-0 bg-transparent p-0 text-success" title="تأكيد">
                                        <i class="ph-duotone ph-check-circle fs-4"></i>
                                    </button>
                                    <button type="button" wire:click="cancelDelete" class="border-0 bg-transparent p-0 text-secondary">
                                        <i class="ph-duotone ph-x-circle fs-4"></i>
                                    </button>
                                @else
                                    @if($closing->isClosed())
                                        <button type="button" wire:click="askReopen({{ $closing->id }})"
                                                class="border-0 bg-transparent p-0 text-warning" title="إعادة فتح">
                                            <i class="ph-duotone ph-lock-open fs-4"></i>
                                        </button>
                                    @endif
                                    <button type="button" wire:click="askDelete({{ $closing->id }})"
                                            class="border-0 bg-transparent p-0 text-danger" title="حذف">
                                        <i class="ph-duotone ph-trash fs-4"></i>
                                    </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="text-center py-5">
                            <i class="ph-duotone ph-lock fs-1 d-block mb-2 text-body-secondary"></i>
                            <h6 class="text-body-secondary mb-1">لا توجد تقفيلات</h6>
                            <small class="text-body-secondary">ابدأ بتقفيل الشهر من الزرار أعلى الصفحة.</small>
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        @if($closings->hasPages())
            <div class="card-footer bg-transparent border-top">
                {{ $closings->links() }}
            </div>
        @endif
    </div>
</div>
