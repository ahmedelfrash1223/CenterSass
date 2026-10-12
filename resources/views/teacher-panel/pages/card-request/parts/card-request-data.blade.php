<div>
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <p class="text-body-secondary mb-0 small">متابعة طلبات الكروت المُرسلة</p>

        @if($canCreate)
            <a href="{{ route('teacher.card-requests.create') }}" wire:navigate
                class="btn btn-primary d-flex align-items-center gap-1">
                <i class="ph-duotone ph-plus-circle"></i>
                طلب كروت جديد
            </a>
        @endif
    </div>

    @livewire('teacher-panel.parts.flash-message', [
        'successKey' => 'card-request-success',
        'errorKey' => 'card-request-error',
    ])

    {{-- Filters --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <div class="row g-2">
                <div class="col-md-5">
                    <div class="position-relative">
                        <i
                            class="ph-duotone ph-magnifying-glass position-absolute top-50 translate-middle-y ms-3 text-body-secondary"></i>
                        <input type="text" wire:model.live.debounce.400ms="search" class="form-control ps-5"
                            placeholder="ابحث برقم الطلب...">
                    </div>
                </div>
                <div class="col-md-3">
                    <select wire:model.live="statusFilter" class="form-select">
                        <option value="">كل الحالات</option>
                        @foreach($statuses as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
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
                        <th>رقم الطلب</th>
                        <th>عدد الكروت</th>
                        <th>الحالة</th>
                        <th>التاريخ</th>
                        <th class="text-center">الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($requests as $request)
                        <tr wire:key="cr-{{ $request->id }}">
                            <td class="ps-4 text-body-secondary fw-semibold">
                                {{ $requests->firstItem() + $loop->index }}
                            </td>
                            <td>
                                <span class="fw-semibold">{{ $request->reference }}</span>
                            </td>
                            <td>
                                <span class="badge bg-primary">
                                    <i class="ph-duotone ph-identification-card me-1"></i>
                                    {{ $request->cards_count }} كارت
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-{{ $request->status->color() }} text-white">
                                    <i class="ph-duotone {{ $request->status->icon() }} me-1"></i>
                                    {{ $request->status->label() }}
                                </span>
                            </td>
                            <td>
                                <small class="text-body-secondary">
                                    {{ $request->created_at?->format('Y-m-d h:i A') }}
                                </small>
                            </td>
                            <td>
                                <div class="d-flex align-items-center justify-content-center gap-3">
                                    @if($deleteId === $request->id)
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
                                        @if($canView)
                                            <a wire:navigate href="{{ route('teacher.card-requests.show', $request->id) }}"
                                                class="border-0 bg-transparent p-0 text-info" title="عرض">
                                                <i class="ph-duotone ph-eye fs-4"></i>
                                            </a>
                                        @endif

                                        @if($canDelete && $request->canBeCancelled())
                                            <button type="button" wire:click="confirmDelete({{ $request->id }})"
                                                class="border-0 bg-transparent p-0 text-danger" title="إلغاء">
                                                <i class="ph-duotone ph-trash fs-4"></i>
                                            </button>
                                        @endif
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <i class="ph-duotone ph-identification-card fs-1 d-block mb-2 text-body-secondary"></i>
                                <h6 class="text-body-secondary mb-1">لا توجد طلبات</h6>
                                <small class="text-body-secondary">ابدأ بإرسال طلب كروت جديد.</small>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($requests->hasPages())
            <div class="card-footer bg-transparent border-top">
                {{ $requests->links() }}
            </div>
        @endif
    </div>
</div>
