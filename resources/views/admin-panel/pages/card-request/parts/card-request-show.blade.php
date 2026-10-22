<div>
    @livewire('admin-panel.parts.flash-message', [
        'successKey' => 'card-request-success',
        'errorKey' => 'card-request-error',
    ])

    {{-- Header --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <div class="d-flex flex-wrap justify-content-between align-items-start gap-3">
                <div>
                    <h5 class="mb-2">{{ $cardRequest->reference }}</h5>
                    <div class="d-flex flex-wrap gap-2">
                        <span class="badge bg-{{ $cardRequest->status->color() }} text-white">
                            <i class="ph-duotone {{ $cardRequest->status->icon() }} me-1"></i>
                            {{ $cardRequest->status->label() }}
                        </span>
                        <span class="badge bg-primary text-white">{{ $cardRequest->cards_count }} كارت</span>
                        <span class="badge bg-success text-white">
                            {{ number_format((float) $cardRequest->unit_price, 2) }} ج.م / كارت
                        </span>
                        <span class="badge bg-secondary text-white">
                            {{ $cardRequest->created_at?->format('Y-m-d h:i A') }}
                        </span>
                    </div>
                    <div class="mt-3">
                        <div><strong>المدرس:</strong> {{ $cardRequest->teacher?->name ?? 'غير معروف' }}</div>
                        <div><strong>مقدم الطلب:</strong> {{ $cardRequest->creator?->name ?? '—' }}</div>
                    </div>
                </div>

                <div class="d-flex flex-wrap gap-2">
                    @if($canStartProcessing)
                        <button type="button" wire:click="askConfirm('start')" class="btn btn-primary">
                            <i class="ph-duotone ph-play-circle me-1"></i>بدء التنفيذ
                        </button>
                    @endif

                    @if($canComplete)
                        <button type="button" wire:click="askConfirm('complete')" class="btn btn-success">
                            <i class="ph-duotone ph-check-square me-1"></i>إنهاء الطلب
                        </button>
                    @endif

                    @if($canShip)
                        <button type="button" wire:click="askConfirm('ship')" class="btn btn-info">
                            <i class="ph-duotone ph-truck me-1"></i>شحن الطلب
                        </button>
                    @endif

                    @if($canDeliver)
                        <button type="button" wire:click="askConfirm('deliver')" class="btn btn-success">
                            <i class="ph-duotone ph-package me-1"></i>تم التسليم
                        </button>
                    @endif

                    @if($canExport)
                        <a href="{{ route('admin.card-requests.export-zip', $cardRequest->id) }}" class="btn btn-success"
                            target="_blank">
                            <i class="ph-duotone ph-download-simple me-1"></i>تنزيل ملفات الطباعة
                        </a>
                    @endif

                    @if($canCancel)
                        <button type="button" wire:click="askConfirm('cancel')" class="btn btn-outline-danger">
                            <i class="ph-duotone ph-x-circle me-1"></i>إلغاء الطلب
                        </button>
                    @endif

                    <a href="{{ route('admin.card-requests.index') }}" wire:navigate class="btn btn-outline-secondary">
                        <i class="ph-duotone ph-arrow-right me-1"></i>رجوع
                    </a>
                </div>
            </div>

            @if($cardRequest->notes)
                <div class="alert alert-light border mt-3 mb-0 small">
                    <strong>ملاحظات المدرس:</strong> {{ $cardRequest->notes }}
                </div>
            @endif
            @if($cardRequest->admin_notes)
                <div class="alert alert-info border mt-3 mb-0 small">
                    <strong>ملاحظات الإدارة:</strong> {{ $cardRequest->admin_notes }}
                </div>
            @endif
        </div>
    </div>

    {{-- Confirmation Dialog (inline, بدون مودال المتصفح) --}}
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

    {{-- Review --}}
    @if($canReview)
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-body">
                <h6 class="mb-0 fw-bold">مراجعة الطلب</h6>
            </div>
            <div class="card-body">
                <label for="admin-notes" class="form-label">ملاحظات الإدارة</label>
                <textarea id="admin-notes" wire:model="adminNotes" rows="3" maxlength="2000"
                    class="form-control @error('admin_notes') is-invalid @enderror"
                    placeholder="أضف ملاحظات أو سبب الرفض..."></textarea>
                @error('admin_notes')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
                <div class="d-flex flex-wrap gap-2 mt-3">
                    <button type="button" wire:click="askConfirm('approve')" class="btn btn-success">
                        <i class="ph-duotone ph-check-circle me-1"></i>قبول الطلب
                    </button>
                    <button type="button" wire:click="askConfirm('reject')" class="btn btn-danger">
                        <i class="ph-duotone ph-x-circle me-1"></i>رفض الطلب
                    </button>
                </div>
            </div>
        </div>
    @endif

    {{-- Students Table (read-only, no payment columns for admin) --}}
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-body">
            <h6 class="mb-0 fw-bold">
                <i class="ph-duotone ph-users-three me-1"></i>الطلاب ({{ $cardRequest->items->count() }})
            </h6>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">#</th>
                            <th>الطالب</th>
                            <th>الكود</th>
                            <th>المرحلة</th>
                            <th>الفرع</th>
                            <th>المجموعة</th>
                            <th class="text-center">سعر الكارت</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($cardRequest->items as $item)
                            <tr wire:key="card-request-item-{{ $item->id }}">
                                <td class="ps-4 text-body-secondary fw-semibold">{{ $loop->iteration }}</td>
                                <td><span class="fw-semibold">{{ $item->student_name }}</span></td>
                                <td><small class="text-body-secondary">{{ $item->student_code ?? '—' }}</small></td>
                                <td>
                                    @if($item->grade)
                                        {{ \App\Enums\Teacher\GradeEnum::tryFrom($item->grade)?->label() ?? $item->grade }}
                                    @else
                                        <span class="text-body-secondary small">—</span>
                                    @endif
                                </td>
                                <td><small>{{ $item->branch?->name ?? '—' }}</small></td>
                                <td><small>{{ $item->group?->name ?? '—' }}</small></td>
                                <td class="text-center fw-bold">{{ number_format((float) $item->card_price, 2) }} ج.م</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5">لا توجد بنود في الطلب.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
